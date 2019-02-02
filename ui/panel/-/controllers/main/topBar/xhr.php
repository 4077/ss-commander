<?php namespace ss\commander\ui\panel\controllers\main\topBar;

class Xhr extends \Controller
{
    public $allow = self::XHR;

    /**
     * @var $panel \ss\commander\Svc\Panel
     */
    private $panel;

    private $tree;

    private $cat;

//    private $sPanel;

    private $sTree;

    public function __create()
    {
        $this->panel = commanderPanel($this->_instance());

        $this->tree = $this->panel->getTree();
        $this->cat = $this->panel->getCat();

//        $this->sPanel = &$this->s('~:|' . $this->_instance() . '/tree-' . $this->tree->id);
        $this->sTree = &$this->s('<<content/' . $this->tree->mode . '|' . $this->_instance() . '/tree-' . $this->tree->id);
    }

    public function select()
    {
        $type = $this->data('target/type');
        $id = $this->data('target/id');

        if ($type == 'page' || $type == 'folder') {
            if ($cat = \ss\models\Cat::find($id)) {
                $this->panel->setCat($cat);

                $sTree = &$this->s('~content/' . $this->tree->mode . '|' . $this->panel->instance . '/tree-' . $this->tree->id);

                $sTree['focus'] = [
                    'type' => $type,
                    'id'   => $id
                ];

                $this->panel->reload();
            }
        }
    }

    public function toggleOrdering()
    {
        $orderingField = &ap($this->sTree, 'ordering_field');

        if ($orderingField == 'name') {
            $orderingField = 'position';
        } else {
            $orderingField = 'name';
        }

        $this->c('~:reload|');
    }

    public function updateForceCollapseMode()
    {
        ap($this->sTree, 'force_collapse_mode', $this->data('mode'));
    }

    public function selectDivision()
    {
        $divisionId = $this->data('value');

        ap($this->sTree, 'filters/multisource/division_id', $divisionId);

        if (!$divisionId) {
            ap($this->sTree, 'filters/multisource/warehouses_ids_by_divisions_ids/0', 0);
        }

        $this->panel->reload();
    }

    public function selectWarehouse()
    {
        $divisionId = ap($this->sTree, 'filters/multisource/division_id');

        $warehouseId = $this->data('value');

        if (!$divisionId) {
            if ($warehouse = \ss\multisource\models\Warehouse::find($warehouseId)) {
                $divisionId = $warehouse->target_id;

                ap($this->sTree, 'filters/multisource/division_id', $divisionId);
            }
        }

        if ($divisionId) {
            ap($this->sTree, 'filters/multisource/warehouses_ids_by_divisions_ids/' . $divisionId, $warehouseId);

            $this->panel->reload();
        }
    }
}
