<?php namespace ss\commander\ui\panel\controllers\main\bottomBar\createPanel\treeSelector;

class Xhr extends \Controller
{
    public $allow = self::XHR;

    public function loadDropdown()
    {
        $this->jquery($this->_selector('<:|') . ' .dropdown')->html($this->c('@dropdown:view|'));
    }
}
