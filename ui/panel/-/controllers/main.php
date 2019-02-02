<?php namespace ss\commander\ui\panel\controllers;

class Main extends \Controller
{
    /**
     * @var $panel \ss\commander\Svc\Panel
     */
    private $panel;

    private $tree;

    private $cat;

    private $s;

    public function __create()
    {
        if ($this->panel = commanderPanel($this->_instance())) {
            $this->tree = $this->panel->getTree();
            $this->cat = $this->panel->getCat();

            $this->s = &$this->s('|' . $this->_instance() . '/tree-' . $this->tree->id, [
                'plugins' => [
                    'panel_height'    => 250,
                    'panel_enabled'   => false,
                    'selected_plugin' => false
                ],
                'focus'   => 'content' // content|plugins
            ]);
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

        $v->assign([
                       // 1

                       'CONTENT' => $this->c('>content/' . $this->tree->mode . ':view|'),

                       // 2

                       'TOP_BAR'    => $this->c('>topBar:view|'),
                       'BOTTOM_BAR' => $this->c('>bottomBar:view|'),
                       'PLUGINS'    => $this->c('>plugins:view|')
                   ]);

        $this->css();

        $focus = $this->s['focus'];
        $pluginsPanelEnabled = $this->s['plugins']['panel_enabled'];

        if ($focus == 'plugins' && !$pluginsPanelEnabled) {
            $focus = 'content';
        }

        $this->widget(':|', [
            '.w'                  => [
                'main'      => $this->_w('<~:|' . $this->panel->commander->instance),
                'content'   => $this->_w('>content/' . $this->tree->mode . ':|'),
                'topBar'    => $this->_w('>topBar:|'),
                'bottomBar' => $this->_w('>bottomBar:|')
            ],
            '.r'                  => [
                'focus'              => $this->_p('>xhr:focus|'),
                'togglePluginsPanel' => $this->_p('>xhr:togglePluginsPanel|')
            ],
            'reload'              => $this->_calledMethodIn('reload'),
            'panelName'           => $this->panel->name,
            'focus'               => $focus,
            'pluginsPanelEnabled' => $pluginsPanelEnabled
        ]);

        return $v;
    }
}
