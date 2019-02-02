<?php namespace ss\commander\ui\panel\controllers\main;

class BottomBar extends \Controller
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

        $panel = $this->panel;
        $tree = $this->tree;

        $opposite = $panel->getOpposite();
        $oppositeTree = $opposite->getTree();
        $relation = $panel->getOppositeRelation();

        $cat = $this->cat;
        $catXPack = xpack_model($cat);

        $treeEditable = $tree->editable;
        $catEditable = ss()->cats->isEditable($cat);

        $sCreatePanel = $this->s('>createPanel|' . $this->_instance() . '/tree-' . $tree->id);
        $createPanelPinned = $sCreatePanel['pinned'] ?? false;

        if ($treeEditable && $catEditable) {
            $v->assign('create_buttons');

            $v->assign([
                           'CREATE_PANEL_PINNED_CLASS' => $createPanelPinned ? 'pinned' : '',
                           'PIN_BUTTON_PINNED_CLASS'   => $createPanelPinned ? 'pinned' : ''
                       ]);

            if (ss()->cats->isProductsCDable($cat)) {
                $v->assign('create_product_button');
            }

            if ($tree->mode == 'pages') {
                $v->assign([
//                               'CREATE_PAGE_BUTTON'      => $this->c('\std\ui button:view', [
//                                   'path'  => '>xhr:createPage|',
//                                   'class' => 'create_button',
//                                   'icon'  => 'icon fa fa-file',
//                                   'title' => 'Создать страницу'
//                               ]),
'CREATE_CONTAINER_BUTTON' => $this->c('\std\ui button:view', [
    'path'  => '>xhr:createContainer|',
    'class' => 'create_button',
    'icon'  => 'icon fa fa-cube',
    'title' => 'Создать контейнер'
])
                           ]);
            }

            if ($tree->mode == 'folders') {
                $v->assign([
                               'CREATE_FOLDER_BUTTON' => $this->c('\std\ui button:view', [
                                   'path'  => '>xhr:createFolder|',
                                   'data'  => [
                                       'cat' => $catXPack
                                   ],
                                   'class' => 'create_button',
                                   'icon'  => 'icon fa fa-folder',
                                   'ешеду' => 'Создать папку'
                               ])
                           ]);
            }

            if ((!$relation || $relation == 'split') && $oppositeTree->editable && $tree->mode == $oppositeTree->mode) {
                $v->assign('copy_button');
            }

            if ($relation == 'split' && $oppositeTree->editable) {
                $v->assign('move_button');
            }
        }

        /// todo
        $productsEditable = ss()->cats->isProductsEditable($cat);

        if ($treeEditable && ($catEditable || $productsEditable)) {
            $v->assign('edit_button');
            $v->assign('delete_button');
        }
        ///

        if ($relation == 'source' && $oppositeTree->editable) {
            $v->assign('install_button');
        }

        // todo optimise, double
        $enablePlugins = $this->panel->getEnabledPlugins();

        $selectedPluginName = &ap($this->sPanel, 'plugins/selected_plugin');

        if (!$selectedPluginName) {
            $selectedPluginName = key($enablePlugins);
        }

        // todo optimize plugins
        $pluginsComponentsCatId = ss()->config('trees/plugins/components_cat_id');

        $pluginsComponents = \ewma\components\models\Component::where('cat_id', $pluginsComponentsCatId)->orderBy('position')->get();

        $pluginsComponentsByName = table_rows_by($pluginsComponents, 'name');

        $enabledPlugins = $this->panel->getEnabledPlugins();

        if ($enabledPlugins) {
            $v->assign('plugins');

            foreach ($enabledPlugins as $pluginName => $pluginTreeData) {
                $name = '...';

                if (isset($pluginsComponentsByName[$pluginName])) {
                    if ($pluginDataHandler = components()->getHandler($pluginsComponentsByName[$pluginName], 'data')) {
                        $pluginData = handlers()->render($pluginDataHandler);

                        $name = ap($pluginData, 'name');
                    }
                }

                $selected = $pluginName == $selectedPluginName;

                $v->assign('plugin', [
                    'BUTTON' => $this->c('\std\ui button:view', [
                        'path'    => '>xhr:selectPlugin|',
                        'data'    => [
                            'name' => $pluginName
                        ],
                        'class'   => 'plugin_button ' . ($selected ? 'selected' : ''),
                        'content' => $name
                    ]),
                ]);
            }
        }

        $v->assign([
                       'FOCUS_CLASS' => $this->panel->hasFocus('content') ? 'focus' : ''
                   ]);

        if ($createPanelPinned) {
            $v->assign('CREATE_PANEL', $this->c('>createPanel:view|'));
        }

        $this->css(':\css\std~, \css\std flex');

        $this->widget(':|', [
            '.w'                => [
                'content'  => $this->_w('@content/' . $tree->mode . ':|' . $panel->instance),
                'opposite' => $this->_w('@content/' . $oppositeTree->mode . ':|' . $opposite->instance),
                'panel'    => $this->_w('~:|' . $this->panel->instance)
            ],
            '.r'                => [
                'install'            => $this->_p('>xhr:install|'),
                'move'               => $this->_p('>xhr:move|'),
                'copy'               => $this->_p('>xhr:copy|'),
                'togglePluginsPanel' => $this->_p('@xhr:togglePluginsPanel|'),
                'loadCreatePanel'    => $this->_p('>xhr:loadCreatePanel|'),
                'toggleCreatePanel'  => $this->_p('>xhr:toggleCreatePanel|'),
                'createProduct'      => $this->_p('>xhr:createProduct|'),
                'createPage'         => $this->_p('>xhr:createPage|'),
            ],
            'split'             => $relation == 'split',
            'createPanelPinned' => $createPanelPinned,
            'panelName'         => $this->panel->name
        ]);

        return $v;
    }
}
