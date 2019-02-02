<?php namespace ss\commander\ui\panel\controllers\main\bottomBar\createPanel\treeSelector\dropdown\node;

class Xhr extends \Controller
{
    public $allow = self::XHR;

    public function select()
    {
        if ($tree = $this->unxpackModel('node')) {
            $panel = commanderPanel($this->_instance());

            $panelTree = $panel->getTree();

            $this->s('<<<<:selected_tree_id|' . $this->_instance() . '/tree-' . $panelTree->id, $tree->id, RR);
            $this->c('<<<<:reload|' . $panel->instance);
        }
    }
}
