<?php namespace ss\commander\ui\panel\controllers\main\content;

class Folders extends \Controller
{
    /**
     * @var $panel \ss\commander\Svc\Panel
     */
    private $panel;

    private $tree;

    private $cat;

    private $sTree;

    public function __create()
    {
        $this->panel = $panel = commanderPanel($this->_instance());

        $this->tree = $panel->getTree();
        $this->cat = $panel->getCat();

        $this->sTree = &$this->s('|' . $this->_instance() . '/tree-' . $this->tree->id, [
            'filters'        => [
                'multisource' => [
                    'division_id'                     => 0,
                    'warehouses_ids_by_divisions_ids' => []
                ]
            ],
            'focus'          => [
                'type' => null,
                'id'   => null
            ],
            'selection'      => [
                'type' => null,
                'id'   => null
            ],
            'scroll'         => [
                'left' => 0,
                'top'  => 0
            ],
            'ordering_field' => 'name'
        ]);
    }

    public function reload()
    {
        $this->jquery('|')->replace($this->view());
    }

    public function view()
    {
        $v = $this->v('|');

        $cat = $this->cat;
        $tree = $this->tree;

        $treeEditable = $tree->editable;

        $isRoot = $cat->parent_id == 0;

        if ($isRoot) {
            $v->assign('is_root');
        }

        $branch = ss()->cats->getNamesBranch($cat, false);

        foreach ($branch as $id => $name) {
            $v->assign('branch_node', [
                'ID'   => $id,
                'NAME' => $name
            ]);
        }

        $v->assign([
                       'FOCUS_CLASS'   => $this->panel->hasFocus('content') ? 'focus' : '',
                       'CAT_ID'        => $cat->id,
                       'PARENT_ID'     => $cat->parent_id,
                       'CURRENT_TITLE' => 'id=' . $cat->id . ' parent_id=' . $cat->parent_id . ' tree_id=' . $cat->tree_id,
                       'PARENT_TITLE'  => 'id=' . $cat->id . ' parent_id=' . $cat->parent_id . ' tree_id=' . $cat->tree_id // tmp
                   ]);

        $foldersBuilder = $cat->nested();

        if ($this->sTree['ordering_field'] == 'name') {
            $foldersBuilder = $foldersBuilder->orderBy('name')->orderBy('short_name');
        } else {
            $foldersBuilder = $foldersBuilder->orderBy('position');
        }

        $folders = $foldersBuilder->get();

        foreach ($folders as $folder) {
            $stat = _j($folder->stat);

            aa($stat, [
                'products_count'           => '-',
                'installed_products_count' => '-',
            ]);

            $folderEditable = ss()->cats->isEditable($folder);

            $v->assign('folder', [
                'ID'                       => $folder->id,
                'LOCKED_CLASS'             => $folderEditable ? '' : 'locked',
                'DISABLED_CLASS'           => $folder->enabled ? '' : 'disabled',
                'NOT_PUBLISHED_CLASS'      => $folder->published ? '' : 'not_published',
                'NAME'                     => ss()->cats->getName($folder),
                'PRODUCTS_COUNT'           => $stat['products_count'],
                'INSTALLED_PRODUCTS_COUNT' => $stat['installed_products_count'],
                'TITLE'                    => 'type=' . $cat->type . ' id=' . $folder->id . ' parent_id=' . $folder->parent_id . ' tree_id=' . $folder->tree_id // tmp
            ]);
        }

        $products = $cat->products()->with(['images', 'refs'])->orderBy($this->sTree['ordering_field'])->get();

        $divisionId = ap($this->sTree, 'filters/multisource/division_id');
        $warehouseId = ap($this->sTree, 'filters/multisource/warehouses_ids_by_divisions_ids/' . $divisionId);

        foreach ($products as $product) {
            $productEditable = ss()->products->isEditable($product);

            $installed = count($product->refs);
            $imagesCount = count($product->images);

            list($price, $discount, $stock, $reserved) = ss()->products->explodeMultisourceCache($product, $divisionId, $warehouseId);

            $v->assign('product', [
                'ID'                  => $product->id,
                'LOCKED_CLASS'        => $productEditable ? '' : 'locked',
                'INSTALLED_CLASS'     => $installed ? 'installed' : '',
                'DISABLED_CLASS'      => $product->enabled ? '' : 'disabled',
                'NOT_PUBLISHED_CLASS' => $product->published ? '' : 'not_published',
                //                'NAME'                => $product->name, // $product->import_name,
                'NAME'                => $product->name,
                'STOCK'               => $stock,
                'PRICE'               => $price,
                'DISCOUNT'            => $discount,
                'HAS_IMAGES_CLASS'    => $imagesCount ? 'has' : '',
                'IMAGES_COUNT'        => $imagesCount,
                'TITLE'               => 'id=' . $product->id . ' folder_id=' . $cat->id . ' tree_id=' . $product->tree_id . ' source_id=' . $product->source_id . ' position=' . $product->position // tmp
            ]);
        }

        $this->css(':\css\std~');

        $this->widget(':|', [
            '.w'        => [
                'panel' => $this->_w('~:|' . $this->panel->instance)
            ],
            '.r'        => [
                'reload'          => $this->_p('@xhr:reload|'),
                'focus'           => $this->_p('@xhr:focus|'),
                'select'          => $this->_p('@xhr:select|'),
                'open'            => $this->_p('@xhr:open|'),
                'install'         => $this->_p('@xhr:install|'),
                'delete'          => $this->_p('@xhr:delete|'),
                'scroll'          => $this->_p('@xhr:scroll|'),
                'qr'              => $this->_p('>xhr:qr|'),
                'arrangeProducts' => $this->_p('@xhr:arrangeProducts|'),
                'arrangeFolders'  => $this->_p('@xhr:arrangeFolders|')
            ],
            'editable'  => $tree->editable,
            'focus'     => $this->sTree['focus'],
            'selection' => $this->sTree['selection'],
            'scroll'    => $this->sTree['scroll'],
            'sortable'  => $treeEditable && $this->sTree['ordering_field'] == 'position',
            'catId'     => $cat->id,
            'parentId'  => $cat->parent_id,
            'isRoot'    => $isRoot,
            'panelName' => $this->panel->name
        ]);

        return $v;
    }
}
