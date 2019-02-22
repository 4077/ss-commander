<?php namespace ss\commander\ui\panel\controllers\main\content\pages;

class Container extends \Controller
{
    /**
     * @var $panel \ss\commander\Svc\Panel
     */
    private $panel;

    private $tree;

    private $cat;

    private $container;

    private $sTree;

    private $sCat;

    private $viewInstance;

    public function __create()
    {
        if ($this->container = $this->unpackModel('container')) {
            $this->viewInstance = $this->_instance() . '/' . $this->container->id;

            $this->cat = $this->data('cat') ?: $this->container->parent;
            $this->tree = $this->data('tree') ?: $this->cat->tree;

            $this->sTree = &ap($this->data, 'sTree') or
            $this->sTree = $this->s('|' . $this->_instance() . '/tree-' . $this->tree->id);

            $this->panel = $this->data('panel') ?: commanderPanel($this->_instance());

            $this->sCat = &ap($this->data, 'sCat') or
            $this->sCat = $this->s('|' . $this->panel->commander->instance . '/cat-' . $this->cat->id);
        } else {
            $this->lock();
        }
    }

    public function reload()
    {
        $this->jquery('|' . $this->viewInstance)->replace($this->view());
    }

    public function view()
    {
        $v = $this->v('|' . $this->viewInstance);

        // todo if moderation
        $statuses = (new \ss\moderation\Main)->statuses;

        $container = $this->container;

        $containerEditable = ss()->cats->isEditable($container);

        $collapsed = in($container->id, $this->sCat['collapsed_cats_ids']);

        $forceCollapseMode = ap($this->sTree, 'force_collapse_mode');

        $products = $container->products()->with('images')->orderBy($this->sTree['ordering_field'])->get();

        $v->assign([
                       'ID'                        => $container->id,
                       'LOCKED_CLASS'              => $containerEditable ? '' : 'locked',
                       'DISABLED_CLASS'            => $container->enabled ? '' : 'disabled',
                       'NOT_PUBLISHED_CLASS'       => $container->published ? '' : 'not_published',
                       'NAME'                      => ss()->cats->getName($container),
                       'COLLAPSED_CLASS'           => $collapsed ? 'collapsed' : 'not_collapsed',
                       'FORCE_COLLAPSE_MODE_CLASS' => $forceCollapseMode ? 'force_' . $forceCollapseMode : '',
                       'PRODUCT_COUNT_ICON_CLASS'  => $collapsed ? 'fa-caret-left' : 'fa-caret-down',
                       'PRODUCTS_COUNT'            => count($products),
                       'TITLE'                     => 'id=' . $container->id . ' parent_id=' . $container->parent_id . ' tree_id=' . $container->tree_id . ' position=' . $container->position // tmp
                   ]);

        // pages

        $pagesBuilder = $container->containedPages();

        if ($this->sTree['ordering_field'] == 'name') {
            $pagesBuilder = $pagesBuilder->orderBy('name')->orderBy('short_name');
        } else {
            $pagesBuilder = $pagesBuilder->orderBy('position');
        }

        $pages = $pagesBuilder->get();

        foreach ($pages as $page) {
            $pageEditable = ss()->cats->isEditable($page);

            $v->assign('page', [
                'ID'                  => $page->id,
                'LOCKED_CLASS'        => $pageEditable ? '' : 'locked',
                'DISABLED_CLASS'      => $page->enabled ? '' : 'disabled',
                'NOT_PUBLISHED_CLASS' => $page->published ? '' : 'not_published',
                'NAME'                => ss()->cats->getName($page),
                'TITLE'               => 'id=' . $page->id . ' parent_id=' . $page->parent_id . ' container_id=' . $container->id . ' tree_id=' . $page->tree_id . ' position=' . $page->position// tmp
            ]);
        }

        // products

        $divisionId = ap($this->sTree, 'filters/multisource/division_id');
        $warehouseId = ap($this->sTree, 'filters/multisource/warehouses_ids_by_divisions_ids/' . $divisionId);

        foreach ($products as $product) {
            $productEditable = ss()->products->isEditable($product);

            $imagesCount = count($product->images);

            list($price, $discount, $stock, $reserved) = ss()->products->explodeMultisourceCache($product, $divisionId, $warehouseId);

            $v->assign('product', [
                'ID'                  => $product->id,
                'NAME'                => $product->name,
                'LOCKED_CLASS'        => $productEditable ? '' : 'locked',
                'DISABLED_CLASS'      => $product->enabled ? '' : 'disabled',
                'NOT_PUBLISHED_CLASS' => $product->published ? '' : 'not_published',
                'STOCK'               => $stock,
                'PRICE'               => $price,
                'DISCOUNT'            => $discount,
                'STATUS_TITLE'        => $statuses[$product->status]['title'] . ' (' . \Carbon\Carbon::parse($product->status_datetime)->format('d.m.Y H:i:s') . ')',
                'ICON_CLASS'          => $statuses[$product->status]['icon'],
                'STATUS_CLASS'        => $product->status,
                'HAS_IMAGES_CLASS'    => $imagesCount ? 'has' : '',
                'IMAGES_COUNT'        => $imagesCount,
                'TITLE'               => 'id = ' . $product->id . ' container_id = ' . $container->id . ' tree_id = ' . $product->tree_id . ' source_id = ' . $product->source_id . ' position = ' . $product->position // tmp
            ]);
        }

        // todo optimize
        $enabledPlugins = ss()->trees->plugins->getEnabled($this->tree);

        if (isset($enabledPlugins['moderation'])) {
            $v->assign('moderation_plugin');
        }

        $this->css();

        return $v;
    }
}
