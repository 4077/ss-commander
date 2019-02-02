<?php namespace ss\commander\ui\panel\controllers\main;

class TopBar extends \Controller
{
    /**
     * @var $panel \ss\commander\Svc\Panel
     */
    private $panel;

    private $tree;

    private $cat;

    private $sPanel;

    private $sTree;

    public function __create()
    {
        if ($this->panel = commanderPanel($this->_instance())) {
            $this->tree = $this->panel->getTree();
            $this->cat = $this->panel->getCat();

            $this->sPanel = &$this->s('~:|' . $this->_instance() . '/tree-' . $this->tree->id);
            $this->sTree = &$this->s('@content/' . $this->tree->mode . '|' . $this->_instance() . '/tree-' . $this->tree->id);
        } else {
            $this->lock();
        }
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

        $branch = ss()->cats->getNamesBranch($cat, false);

        foreach ($branch as $id => $name) {
            $v->assign('branch_node', [
                'ID'   => $id,
                'NAME' => $name
            ]);
        }

        $selectedDivisionId = ap($this->sTree, 'filters/multisource/division_id');

        $divisions = \ss\multisource\models\Division::orderBy('position')->get();

        if ($division = \ss\multisource\models\Division::find($selectedDivisionId)) {
            $warehouses = $division->warehouses()->orderBy('position')->get();
        } else {
            $warehouses = \ss\multisource\models\Warehouse::orderBy('position')->get();
        }

        $v->assign([
                       'ROOT_INDICATOR_ICON'        => $tree->mode === 'folders' ? 'fa-folder' : 'fa-file',
                       'ROOT_INDICATOR_FOCUS_CLASS' => $this->sTree['focus']['id'] == $cat->id ? 'focus' : '',
                       'FOCUS_CLASS'                => $this->panel->hasFocus('content') ? 'focus' : '',
                       'TREE_SELECTOR'              => $this->c('>treeSelector:view|'),
                       'ORDERING_TOGGLE_BUTTON'     => $this->c('\std\ui button:view', [
                           'path'  => '>xhr:toggleOrdering|',
                           'class' => 'ordering_toggle_button ' . ($this->sTree['ordering_field'] ?? ''),
                           'icon'  => 'fa fa-sort-alpha-asc'
                       ]),
                       'DIVISION_SELECTOR'          => $this->c('\std\ui select:view', [
                           'path'     => '>xhr:selectDivision|',
                           'items'    => [0 => 'Все подразделения'] + table_cells_by_id($divisions, 'name'),
                           'selected' => $selectedDivisionId
                       ]),
                       'WAREHOUSE_SELECTOR'         => $this->c('\std\ui select:view', [
                           'path'     => '>xhr:selectWarehouse|',
                           'items'    => [0 => 'Все склады'] + table_cells_by_id($warehouses, 'name'),
                           'selected' => ap($this->sTree, 'filters/multisource/warehouses_ids_by_divisions_ids/' . $selectedDivisionId)
                       ])
                   ]);

        if ($this->tree->mode == 'pages') {
            $v->assign('force_collapse_buttons', [
                'COLLAPSE_PRESSED_CLASS' => $this->sTree['force_collapse_mode'] == 'collapse' ? 'pressed' : '',
                'EXPAND_PRESSED_CLASS'   => $this->sTree['force_collapse_mode'] == 'expand' ? 'pressed' : '',
            ]);
        }

        $this->css();

        $this->widget(':|', [
            '.r'                => [
                'reload'                  => $this->_p('>xhr:reload|'),
                'select'                  => $this->_p('>xhr:select|'),
                'open'                    => $this->_p('@content/xhr:open|'),
                'updateForceCollapseMode' => $this->_p('>xhr:updateForceCollapseMode|')
            ],
            '.w'                => [
                'content' => $this->_w('@content/' . $tree->mode . ':|' . $this->panel->instance),
                'panel'   => $this->_w('~:|' . $this->panel->instance)
            ],
            'forceCollapseMode' => ap($this->sTree, 'force_collapse_mode'),
            'catId'             => $cat->id,
            'parentId'          => $cat->parent_id,
            'treeMode'          => $tree->mode
        ]);

        return $v;
    }
}
