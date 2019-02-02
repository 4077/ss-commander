<?php namespace ss\commander\ui\panel\controllers\main\plugins;

class Xhr extends \Controller
{
    public $allow = self::XHR;

    /**
     * @var $panel \ss\commander\Svc\Panel
     */
    private $panel;

    private $tree;

    private $cat;

    private $sPanel;

    private $s;

    public function __create()
    {
        $this->panel = commanderPanel($this->_instance());

        $this->tree = $this->panel->getTree();
        $this->cat = $this->panel->getCat();

        $this->sPanel = &$this->s('~:|' . $this->_instance() . '/tree-' . $this->tree->id);
        $this->s = &$this->s('|' . $this->_instance() . '/tree-' . $this->tree->id);
    }

    public function updatePanelHeight()
    {
        ap($this->sPanel, 'plugins/panel_height', $this->data('height'));
    }
}
