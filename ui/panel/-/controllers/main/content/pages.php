<?php namespace ss\commander\ui\panel\controllers\main\content;

class Pages extends \Controller
{
    /**
     * @var $panel \ss\commander\Svc\Panel
     */
    private $panel;

    private $tree;

    private $cat;

    private $sTree;

    private $sCat;

    public function __create()
    {
        $this->panel = $panel = commanderPanel($this->_instance());

        $this->tree = $panel->getTree();
        $this->cat = $panel->getCat();

        $this->sTree = &$this->s('|' . $this->_instance() . '/tree-' . $this->tree->id, [
            'filters'             => [
                'multisource' => [
                    'division_id'                     => 0,
                    'warehouses_ids_by_divisions_ids' => []
                ]
            ],
            'focus'               => [
                'type' => null,
                'id'   => null
            ],
            'selection'           => [
                'type' => null,
                'id'   => null
            ],
            'scroll'              => [
                'left' => 0,
                'top'  => 0
            ],
            'ordering_field'      => 'name',
            'force_collapse_mode' => false // false|collapse|expand
        ]);

        $this->sCat = &$this->s('|' . $panel->commander->instance . '/cat-' . $this->cat->id, [
            'collapsed_cats_ids' => []
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
                       'FOCUS_CLASS' => $this->panel->hasFocus('content') ? 'focus' : '',
                       'CAT_ID'      => $cat->id,
                       'PARENT_ID'   => $cat->parent_id,
                       //                       'CURRENT_TITLE' => 'id=' . $cat->id . ' parent_id=' . $cat->parent_id . ' container_id=' . $cat->container_id . ' tree_id=' . $cat->tree_id,
                       //                       'PARENT_TITLE'  => 'id=' . $cat->parent->id ?? '-' . ' container_id=' . $cat->parent->container_id ?? '-' . ' tree_id=' . $cat->tree_id ?? ''
                   ]);

        $pagesBuilder = $cat->pages()->where('container_id', false);

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
                'TITLE'               => 'id=' . $page->id . ' parent_id=' . $page->parent_id . ' tree_id=' . $page->tree_id // tmp
            ]);
        }

        $containersBuilder = $cat->containers();

        if ($this->sTree['ordering_field'] == 'name') {
            $containersBuilder = $containersBuilder->orderBy('name')->orderBy('short_name');
        } else {
            $containersBuilder = $containersBuilder->orderBy('position');
        }

        $containers = $containersBuilder->get();

        foreach ($containers as $container) {
            $v->assign('container', [
                'ID'      => $container->id,
                'CONTENT' => $this->c('>container:view|', [
                    'panel'     => $this->panel,
                    'tree'      => $tree,
                    'cat'       => $cat,
                    'container' => $container,
                    'sTree'     => &$this->sTree,
                    'sCat'      => &$this->sCat
                ])
            ]);
        }

        if ($this->sTree['ordering_field'] == 'position') {
            $this->c('\std\ui sortable:bind', [
                'selector'       => $this->_selector('|') . " .containers",
                'items_id_attr'  => 'container_id',
                'path'           => '@xhr:arrangeContainers|',
                'plugin_options' => [
                    'distance' => 10,
                    'axis'     => 'y',
                    'items'    => ' .sortable'
                ]
            ]);
        }

        $this->css(':\css\std~');

        $this->widget(':|', [
            '.w'                => [
                'panel' => $this->_w('~:|' . $this->panel->instance)
            ],
            '.r'                => [
                'reload'            => $this->_p('@xhr:reload|'),
                'focus'             => $this->_p('@xhr:focus|'),
                'select'            => $this->_p('@xhr:select|'),
                'open'              => $this->_p('@xhr:open|'),
                'delete'            => $this->_p('@xhr:delete|'),
                'scroll'            => $this->_p('@xhr:scroll|'),
                'toggleCollapse'    => $this->_p('@xhr:toggleCollapse|'),
                'togglePublished'   => $this->_p('@xhr:togglePublished|'),
                'toggleEnabled'     => $this->_p('@xhr:toggleEnabled|'),
                'qr'                => $this->_p('@xhr:qr|'),
                'arrangePages'      => $this->_p('@xhr:arrangePages|'),
                'arrangeContainers' => $this->_p('@xhr:arrangeContainers|'),
                'arrangeProducts'   => $this->_p('@xhr:arrangeProducts|')
            ],
            'editable'          => $tree->editable,
            'statuses'          => (new \ss\moderation\Main)->statuses,
            'focus'             => $this->sTree['focus'],
            'forceCollapseMode' => $this->sTree['force_collapse_mode'],
            'selection'         => $this->sTree['selection'],
            'scroll'            => $this->sTree['scroll'],
            'sortable'          => $this->sTree['ordering_field'] == 'position',
            'catId'             => $cat->id,
            'parentId'          => $cat->parent_id,
            'isRoot'            => $isRoot,
            'panelName'         => $this->panel->name
        ]);

        return $v;
    }
}
