<?php namespace ss\commander\ui\panel\controllers\main\topBar\treeSelector\dropdown\node;

class Xhr extends \Controller
{
    public $allow = self::XHR;

    public function select()
    {
        if ($tree = $this->unxpackModel('node')) {
            $panel = commanderPanel($this->_instance());

            $panel->setTree($tree);

            $this->c('<~:reload|' . $panel->commander->instance);
        }
    }
}
