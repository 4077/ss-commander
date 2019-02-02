<?php namespace ss\commander\ui\panel\controllers\main;

class Plugins extends \Controller
{
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
        if ($this->panel = commanderPanel($this->_instance())) {
            $this->tree = $this->panel->getTree();
            $this->cat = $this->panel->getCat();

            $this->sPanel = &$this->s('~:|' . $this->_instance() . '/tree-' . $this->tree->id);
            $this->s = &$this->s('|' . $this->_instance() . '/tree-' . $this->tree->id);
        } else {
            $this->lock();
        }
    }

    public function view()
    {
        $v = $this->v('|');

        $panelEnabled = ap($this->sPanel, 'plugins/panel_enabled');

        $enabledPlugins = $this->panel->getEnabledPlugins();

        if ($panelEnabled && $enabledPlugins) {
            // todo optimise, double
            $selectedPluginName = &ap($this->sPanel, 'plugins/selected_plugin');

            if (!$selectedPluginName) {
                $selectedPluginName = key($enabledPlugins);
            }

            if ($selectedPluginName && in($selectedPluginName, array_keys($enabledPlugins))) {
                $plugin = $this->panel->getPlugin($selectedPluginName);

                // todo optimize plugins
                $pluginsComponentsCatId = ss()->config('trees/plugins/components_cat_id');

                $pluginComponent = \ewma\components\models\Component::where('cat_id', $pluginsComponentsCatId)->where('name', $selectedPluginName)->orderBy('position')->first();

                if ($pluginPanelHandler = components()->getHandler($pluginComponent, 'panel')) {
                    $content = handlers()->render($pluginPanelHandler, [
                        'instance' => $this->_instance(),
                        'data'     => $plugin['data'] ?? []
                    ]);

                    $v->assign('CONTENT', $content);
                }
            }

            $v->assign([
                           'HEIGHT'              => ap($this->sPanel, 'plugins/panel_height'),
                           'PANEL_ENABLED_CLASS' => $panelEnabled && $enabledPlugins ? 'enabled' : ''
                       ]);

            $this->c('\std\ui resizable:bind', [
                'selector'      => $this->_selector('|'),
                'path'          => '>xhr:updatePanelHeight|',
                'pluginOptions' => [
                    'handles' => 'n'
                ]
            ]);
        }

        $this->css();

        return $v;
    }
}
