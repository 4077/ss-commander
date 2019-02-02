<?php namespace ss\commander\ui\panel\controllers\main\content\delete;

class Xhr extends \Controller
{
    public $allow = self::XHR;

    /**
     * @var $panel \ss\commander\Svc\Panel
     */
    private $panel;

    private $tree;

    private $cat;

    public function __create()
    {
        $this->panel = commanderPanel($this->_instance());

        $this->tree = $this->panel->getTree();
        $this->cat = $this->panel->getCat();

        $this->dmap('<|', 'items');
    }

    private function complete($updatedItems)
    {
        $this->d('<:items|', $updatedItems, RR);

        if ($updatedItems) {
            $this->c('<:reload|');
        } else {
            $this->c('\std\ui\dialogs~:close:delete|ss/commander');
        }
    }

    public function deleteFolders()
    {
        if ($ids = _j64($this->data('ids'))) {
            if ($folders = \ss\models\Cat::whereIn('id', $ids)->get()) {
                $deleteCatsIds = [];
                $deleteProductsIds = [];

                $updateCatsIds = [];

                foreach ($folders as $folder) {
                    if (ss()->cats->isCDable($folder)) {
                        merge($deleteCatsIds, $folder->id);

                        $deleteInfo = ss()->cats->getDeleteInfo($folder);

                        foreach ($deleteInfo['tree'] as $row) {
                            merge($deleteCatsIds, $row['cat']->id);
                            merge($deleteProductsIds, array_keys($row['products']));

                            /**
                             * @var $refsInfo \ss\Svc\Products\RefsInfo
                             */
                            if ($refsInfo = $row['refs_info']) {
                                if ($refsIds = $refsInfo->getRefsIds()) {
                                    merge($deleteProductsIds, $refsIds);
                                }
                            }
                        }

                        merge($updateCatsIds, $folder->parent_id);
                    }
                }

                foreach ($updateCatsIds as $catId) {
                    pusher()->trigger('ss/cat/update_cats', [
                        'id' => $catId
                    ]);
                }

                \ss\models\Cat::whereIn('id', $deleteCatsIds)->delete();

                $productsBuilder = \ss\models\Product::whereIn('id', $deleteProductsIds);
                $productsBuilder->delete();
            }

            $this->cancelFolders();
        }
    }

    public function deletePages()
    {
        if ($ids = _j64($this->data('ids'))) {
            if ($pages = \ss\models\Cat::whereIn('id', $ids)->get()) {
                $deleteCatsIds = [];
                $deleteProductsIds = [];

                $updateCatsIds = [];

                foreach ($pages as $page) {
                    if (ss()->cats->isCDable($page)) {
                        merge($deleteCatsIds, $page->id);

                        $deleteInfo = ss()->cats->getDeleteInfo($page);

                        foreach ($deleteInfo['tree'] as $row) {
                            merge($deleteCatsIds, $row['cat']->id);
                            merge($deleteProductsIds, array_keys($row['products']));

                            /**
                             * @var $refsInfo \ss\Svc\Products\RefsInfo
                             */
                            if ($refsInfo = $row['refs_info']) {
                                if ($refsIds = $refsInfo->getRefsIds()) {
                                    merge($deleteProductsIds, $refsIds);
                                }
                            }
                        }

                        merge($updateCatsIds, $page->parent_id);
                    }
                }

                foreach ($updateCatsIds as $catId) {
                    pusher()->trigger('ss/cat/update_cats', [
                        'id' => $catId
                    ]);
                }

                \ss\models\Cat::whereIn('id', $deleteCatsIds)->delete();

                $productsBuilder = \ss\models\Product::whereIn('id', $deleteProductsIds);
                $productsBuilder->delete();
            }

            $this->cancelPages();
        }
    }

    public function deleteContainers()
    {
        if ($ids = _j64($this->data('ids'))) {
            if ($containers = \ss\models\Cat::whereIn('id', $ids)->get()) {
                $deleteCatsIds = [];
                $deleteProductsIds = [];

                $updateCatsIds = [];

                foreach ($containers as $container) {
                    if (ss()->cats->isCDable($container)) {
                        merge($deleteCatsIds, $container->id);

                        $deleteInfo = ss()->cats->getDeleteInfo($container);

                        foreach ($deleteInfo['tree'] as $row) {
                            merge($deleteProductsIds, array_keys($row['products']));

                            /**
                             * @var $refsInfo \ss\Svc\Products\RefsInfo
                             */
                            if ($refsInfo = $row['refs_info']) {
                                if ($refsIds = $refsInfo->getRefsIds()) {
                                    merge($deleteProductsIds, $refsIds);
                                }
                            }
                        }

                        merge($updateCatsIds, $container->parent_id);
                    }
                }

                foreach ($updateCatsIds as $catId) {
                    pusher()->trigger('ss/cat/update_cats', [
                        'id' => $catId
                    ]);
                }

                \ss\models\Cat::whereIn('id', $deleteCatsIds)->delete();

                \ss\models\Cat::whereIn('container_id', $deleteCatsIds)->update(['container_id' => false]);

                $productsBuilder = \ss\models\Product::whereIn('id', $deleteProductsIds);

//                $productsCatsIds = table_cells($productsBuilder->groupBy('cat_id')->get(), 'cat_id');

                $productsBuilder->delete();


//                foreach ($productsCatsIds as $productsCatId) {
//                    pusher()->trigger('ss/cat/update_products', [
//                        'id' => $productsCatId
//                    ]);
//
//                    pusher()->trigger('ss/cat/some_container_update_products', [
//                        'id' => $catId
//                    ]);
//                }
            }

            $this->cancelContainers();
        }
    }

    public function deleteProducts()
    {
        if ($ids = _j64($this->data('ids'))) {
            $products = \ss\models\Product::whereIn('id', $ids)->get();

            $deleteProductsIds = [];
            $updateCatsIds = [];

            foreach ($products as $product) {
                if (ss()->products->isCDable($product)) {
                    merge($deleteProductsIds, $product->id);
                    merge($updateCatsIds, $product->cat_id);

                    $refs = $product->refs;

                    foreach ($refs as $ref) {
                        merge($deleteProductsIds, $ref->id);
                        merge($updateCatsIds, $ref->cat_id);
                    }
                }
            }

            \ss\models\Product::whereIn('id', $deleteProductsIds)->delete();

            foreach ($updateCatsIds as $catId) {
                pusher()->trigger('ss/cat/update_products', [
                    'id' => $catId
                ]);
            }

            $updatedContainers = \ss\models\Cat::whereIn('id', $updateCatsIds)->where('type', 'container')->get();

            if (count($updatedContainers)) {
                foreach ($updatedContainers as $updatedContainer) {
                    pusher()->trigger('ss/cat/some_container_update_products', [
                        'id' => $updatedContainer->parent_id
                    ]);
                }
            }

            $this->cancelProducts();
        }
    }

    public function cancelFolders()
    {
        $items = $this->data('items');

        $updatedItems = [];

        foreach ($items as $item) {
            if ($item['type'] != 'folder') {
                $updatedItems[] = $item;
            }
        }

        $this->complete($updatedItems);
    }

    public function cancelPages()
    {
        $items = $this->data('items');

        $updatedItems = [];

        foreach ($items as $item) {
            if ($item['type'] != 'page') {
                $updatedItems[] = $item;
            }
        }

        $this->complete($updatedItems);
    }

    public function cancelContainers()
    {
        $items = $this->data('items');

        $updatedItems = [];

        foreach ($items as $item) {
            if ($item['type'] != 'container') {
                $updatedItems[] = $item;
            }
        }

        $this->complete($updatedItems);
    }

    public function cancelProducts()
    {
        $items = $this->data('items');

        $updatedItems = [];

        foreach ($items as $item) {
            if ($item['type'] != 'product') {
                $updatedItems[] = $item;
            }
        }

        $this->complete($updatedItems);
    }
}
