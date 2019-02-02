<?php namespace ss\commander\ui\panel\controllers\main;

class Xhr extends \Controller
{
    public $allow = self::XHR;

    /**
     * @var $panel \ss\commander\Svc\Panel
     */
    private $panel;

    private $tree;

    private $cat;

    private $s;

    public function __create()
    {
        $this->panel = commanderPanel($this->_instance());

        $this->tree = $this->panel->getTree();
        $this->cat = $this->panel->getCat();

        $this->s = &$this->s('~|' . $this->_instance() . '/tree-' . $this->tree->id);
    }

    public function focus()
    {
        ap($this->s, 'focus', $this->data('focus'));
    }

    public function togglePluginsPanel()
    {
        $enabled = &$this->s('~:plugins/panel_enabled|' . $this->_instance() . '/tree-' . $this->tree->id);

        invert($enabled);

        $this->panel->reload();
    }
}
