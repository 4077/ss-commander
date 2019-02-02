<?php namespace ss\commander\ui\panel\controllers\main\bottomBar;

class Xhr extends \Controller
{
    public $allow = self::XHR;

    /**
     * @var $panel \ss\commander\Svc\Panel
     */
    private $panel;

    private $tree;

    private $cat;

    private $sTree;

    public function __create()
    {
        $this->panel = commanderPanel($this->_instance());

        $this->tree = $this->panel->getTree();
        $this->cat = $this->panel->getCat();

        $this->sTree = &$this->s('<<content/' . $this->tree->mode . '|' . $this->_instance() . '/tree-' . $this->tree->id);
    }

    public function createFolder()
    {
        if ($cat = $this->cat) {
            ss()->cats->createFolder($cat, ['enabled' => true]);

            pusher()->trigger('ss/cat/update_cats', [
                'id' => $cat->id
            ]);

            pusher()->trigger('ss/tree/update_cats', [
                'id' => $cat->tree_id
            ]);
        }
    }

    public function createPage()
    {
        $target = $this->data('target');

        $targetType = $target['type'] ?? null;
        $catId = $target['id'] ?? null;

        $targetCat = \ss\models\Cat::where('type', $targetType)->where('id', $catId)->first();

        if ($targetCat) {
            ss()->cats->createPage($targetCat, ['enabled' => true]);

            pusher()->trigger('ss/cat/update_cats', [
                'id' => $targetCat->id
            ]);

            pusher()->trigger('ss/tree/update_cats', [
                'id' => $targetCat->tree_id
            ]);
        }
    }

    public function createContainer()
    {
        if ($cat = $this->cat) {
            ss()->cats->createContainer($cat, ['enabled' => true]);

            pusher()->trigger('ss/cat/update_cats', [
                'id' => $cat->id
            ]);

            pusher()->trigger('ss/tree/update_cats', [
                'id' => $cat->tree_id
            ]);
        }
    }

    public function createProduct()
    {
        $target = $this->data('target');

        $targetType = $target['type'] ?? false;

        if (in($targetType, 'page, container, folder')) {
            $catType = $targetType;

            if ($targetType == 'folder') { // todo убрать после замены page>folder
                $catType = 'page';
            }

            $catId = $target['id'] ?? null;

            if ($catId) {
                $targetCat = \ss\models\Cat::where('type', $catType)->where('id', $catId)->first();

                $targetCat->products()->create([
                                                   'tree_id' => $targetCat->tree_id
                                               ]);

                $this->panel->commander->reload();
            }
        }
    }

    public function install()
    {
        $target = $this->data('target');
        $items = $this->data('items');

        $targetType = $target['type'] ?? false;

        if (in($targetType, 'page, container, folder')) {
            $catType = $targetType;

            if ($targetType == 'folder') { // todo убрать после замены page>folder
                $catType = 'page';
            }

            $catId = $target['id'] ?? null;

            if ($catId) {
                $targetCat = \ss\models\Cat::where('type', $catType)->where('id', $catId)->first();

                if ($targetCat) {
                    if (ss()->cats->isProductsCDable($targetCat)) {
//                        $updatedCats = [];

                        foreach ($items as $item) {
                            if ($item['type'] == 'product') {
                                $sourceProduct = \ss\models\Product::find($item['id']);

                                if ($sourceProduct) {
//                                    $targetProduct = \ss\models\Product::where('source_id', $sourceProduct->id)->whereHas('cat', function ($query) use ($targetCat) {
//                                        $query->where('tree_id', $targetCat->tree_id);
//                                    })->first();

                                    $targetProduct = \ss\models\Product::where('source_id', $sourceProduct->id)->where('tree_id', $targetCat->tree_id)->first();

                                    if (!$targetProduct) {
                                        $cloneData = $sourceProduct->toArray();

                                        ra($cloneData, [
                                            'source_id'       => $sourceProduct->id,
                                            'tree_id'         => $targetCat->tree_id,
                                            'status'          => 'initial',
                                            'status_datetime' => \Carbon\Carbon::now()->toDateTimeString(),
                                            'published'       => false
                                        ]);

                                        $newProduct = $targetCat->products()->create($cloneData);

                                        $this->c('\std\images~:copy', [
                                            'source' => $sourceProduct,
                                            'target' => $newProduct
                                        ]);

//                                        if (!isset($updatedCats[$importProduct->cat_id])) {
//                                            $updatedCats[$importProduct->cat_id] = $importProduct->cat;
//                                        }
                                    }
                                }
                            }
                        }

//                        foreach ($updatedCats as $updatedCat) {
//                            commander($this->_instance())->import->updateBranchStat($updatedCat);
//                        }
//
//                        commander($this->_instance())->queue->updateStatusFilterCache();

                        $this->panel->commander->reload();
                    }
                }
            }
        }
    }

    public function move()
    {
        $target = $this->data('target');
        $items = $this->data('items');

        $targetType = $target['type'] ?? false;

        if (in($targetType, 'page, container, folder')) {
            $catType = $targetType;

            if ($targetType == 'folder') { // todo убрать после замены page>folder
                $catType = 'page';
            }

            $catId = $target['id'] ?? null;

            if ($catId) {
                $targetCat = \ss\models\Cat::where('type', $catType)->where('id', $catId)->first();

                if ($targetCat) {
                    // приоритет: page/container, folder, product
                    //
                    // todo проверить, тут что-то не то происходит
                    //
                    foreach ($items as $item) {
                        if (in($item['type'], 'page, container, folder')) {
                            $moveType = $item['type'];
                        }

                        if ($item['type'] == 'product') {
                            if (empty($moveType)) {
                                $moveType = 'product';
                            }
                        }
                    }

                    if (!empty($moveType)) {
                        $moveIds = [];

                        foreach ($items as $item) {
                            if ($item['type'] == $moveType) {
                                isset($item['id']) and $moveIds[] = $item['id'];
                            }
                        }

                        if ($moveType == 'product') {
                            if ($targetCat->tree->mode == 'pages' && $targetCat->type == 'container') {
                                \ss\models\Product::whereIn('id', $moveIds)->update(['cat_id' => $targetCat->id]);
                            }

                            if ($targetCat->tree->mode == 'folders' && $targetCat->type == 'page') { // todo поменять на folder после замены page>folder
                                \ss\models\Product::whereIn('id', $moveIds)->update(['cat_id' => $targetCat->id]);
                            }
                        }

                        // todo убрать дубли

                        if ($moveType == 'page' && $targetCat->type == 'container') {
                            $targetBranchIds = table_ids(\ewma\Data\Tree::get(\ss\models\Cat::where('tree_id', $targetCat->tree_id))->getBranch($targetCat));

                            $diffMoveIds = array_intersect($moveIds, $targetBranchIds);

                            diff($moveIds, $diffMoveIds);

                            if ($diffMoveIds) {
                                foreach ($diffMoveIds as $notMoveId) {
                                    $this->console('не может быть перемещена: ' . ss()->cats->getShortName(\ss\models\Cat::find($notMoveId)));
                                }
                            }

                            if ($moveIds) {
                                \ss\models\Cat::whereIn('id', $moveIds)->update([
                                                                                    'parent_id'    => $targetCat->parent_id,
                                                                                    'container_id' => $targetCat->id
                                                                                ]);

                                // todo обновление алиасов
                            }
                        }

                        if (in($moveType, 'page, container, folder') && $targetCat->type == 'page') { // todo проверить когда будет заменено page>folder
                            $targetBranchIds = table_ids(\ewma\Data\Tree::get(\ss\models\Cat::where('tree_id', $targetCat->tree_id))->getBranch($targetCat));

                            $diffMoveIds = array_intersect($moveIds, $targetBranchIds);

                            diff($moveIds, $diffMoveIds);

                            if ($diffMoveIds) {
                                foreach ($diffMoveIds as $notMoveId) {
                                    $this->console('не может быть перемещена: ' . ss()->cats->getShortName(\ss\models\Cat::find($notMoveId)));
                                }
                            }

                            if ($moveIds) {
                                \ss\models\Cat::whereIn('id', $moveIds)->update(['parent_id' => $targetCat->id]);

                                // todo обновление алиасов
                            }
                        }

                        $this->panel->commander->reload();
                    }
                }
            }
        }
    }

    public function copy()
    {
        $target = $this->data('target');
        $items = $this->data('items');

        $targetType = $target['type'] ?? false;

        if (in($targetType, 'page, container, folder')) {
            $catType = $targetType;

            if ($targetType == 'folder') { // todo убрать после замены page>folder
                $catType = 'page';
            }

            $catId = $target['id'] ?? null;

            if ($catId) {
                $targetCat = \ss\models\Cat::where('type', $catType)->where('id', $catId)->first();

                if ($targetCat) {
                    // приоритет: page/container, folder, product
                    //
                    // todo проверить, тут что-то не то происходит
                    //
                    foreach ($items as $item) {
                        if (in($item['type'], 'page, container, folder')) {
                            $copyType = $item['type'];
                        }

                        if ($item['type'] == 'product') {
                            if (empty($copyType)) {
                                $copyType = 'product';
                            }
                        }
                    }

                    if (!empty($copyType)) {
                        $moveIds = [];

                        foreach ($items as $item) {
                            if ($item['type'] == $copyType) {
                                isset($item['id']) and $moveIds[] = $item['id'];
                            }
                        }

//                        if ($moveType == 'product') {
//                            if ($targetCat->tree->mode == 'pages' && $targetCat->type == 'container') {
//                                \ss\models\Product::whereIn('id', $moveIds)->update(['cat_id' => $targetCat->id]);
//                            }
//
//                            if ($targetCat->tree->mode == 'folders' && $targetCat->type == 'page') { // todo поменять на folder после замены page>folder
//                                \ss\models\Product::whereIn('id', $moveIds)->update(['cat_id' => $targetCat->id]);
//                            }
//                        }

                        if ($copyType == 'page' && $targetCat->type == 'container') {
                            $cats = \ss\models\Cat::whereIn('id', $moveIds)->get();

                            foreach ($cats as $cat) {
                                ss()->cats->copyTo($cat, $targetCat);
                            }
                        }

                        if (in($copyType, 'page, container, folder') && $targetCat->type == 'page') { // todo проверить когда будет заменено page>folder
                            $cats = \ss\models\Cat::whereIn('id', $moveIds)->get();

                            foreach ($cats as $cat) {
                                ss()->cats->copyTo($cat, $targetCat);
                            }
                        }

                        $this->panel->commander->reload();
                    }
                }
            }
        }
    }

    public function qr()
    {
        $list = $this->data('list');

        $this->c('@qrCodes/app:merge', [], 'list');

        $this->c('\std\ui\dialogs~:open:qrCodes, ss|ss/commander', [
            'path'          => '@qrCodes:view',
            'data'          => [
                'list' => $list
            ],
            'pluginOptions' => [
                'title' => 'qr'
            ]
        ]);
    }

    public function selectPlugin()
    {
        $this->s('~:plugins/selected_plugin|' . $this->_instance() . '/tree-' . $this->tree->id, $this->data('name'), RR);
        $this->s('~:plugins/panel_enabled|' . $this->_instance() . '/tree-' . $this->tree->id, true, RR);

        $this->panel->reload();
    }

    public function loadCreatePanel()
    {
        $this->jquery($this->_selector('<:|') . ' .create_panel > .content')->html($this->c('@createPanel:view|'));
    }

    public function toggleCreatePanel()
    {
        $sPath = '<createPanel:pinned|' . $this->_instance() . '/tree-' . $this->tree->id;

        $value = $this->s($sPath, !$this->s($sPath), RR);

        $this->app->response->sendJson(['pinned' => $value]);
    }
}
