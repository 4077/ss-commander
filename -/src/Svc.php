<?php namespace ss\commander;

class Svc extends \ewma\Service\Service
{
    public static $instances = [];

    public $instance;

    /**
     * @return \ss\commander\Svc
     */
    public static function getInstance($instance)
    {
        if (!isset(static::$instances[$instance])) {
            $svc = new self;

            $svc->instance = $instance;

            static::$instances[$instance] = $svc;
            static::$instances[$instance]->__register__();
        }

        return static::$instances[$instance];
    }

    protected $services = [
        'plugins'
    ];

    /**
     * @var \ss\commander\Svc\Plugins
     */
    public $plugins = \ss\commander\Svc\Plugins::class;

    //
    //
    //

    /**
     * @var \ewma\Controllers\Controller
     */
    public $moduleRootController;

    /**
     * @var \ss\commander\ui\controllers\Main
     */
    public $uiMainController;

    public $s;

    public $d;

    protected function boot()
    {
        $this->moduleRootController = app()->modules->getByNamespace('ss\commander')->getController();

        $this->uiMainController = appc('\ss\commander\ui~|' . $this->instance);

        $this->s = &$this->uiMainController->s('|');
        $this->d = &$this->uiMainController->d('|');
    }

    /**
     * @return \ewma\Controllers\Controller
     */
    public function c()
    {
        $args = func_get_args();

        if ($args) {
            $output = call_user_func_array([$this->moduleRootController, 'c'], $args);
        } else {
            $output = $this->moduleRootController;
        }

        return $output;
    }

    public function reload()
    {
        $this->uiMainController->reload();
    }

    private $panels;

    /**
     * @param $name
     *
     * @return \ss\commander\Svc\Panel
     */
    public function getPanel($name)
    {
        if (!isset($this->panels[$name])) {
            $this->panels[$name] = new \ss\commander\Svc\Panel($this, $name);
        }

        return $this->panels[$name];
    }

    private $allConnections;

    private function getAllConnections($instance = '')
    {
        if (null === $this->allConnections[$instance]) {
            $this->allConnections[$instance] = \ss\models\TreesConnection::where('instance', $instance)->get();
        }

        return $this->allConnections[$instance];
    }

    public function getTrees($instance = '')
    {
        $connections = $this->getAllConnections($instance);

        $sourcesIds = table_column($connections, 'source_id');
        $targetIds = table_column($connections, 'target_id');

        $treesIds = [];

        merge($treesIds, $sourcesIds);
        merge($treesIds, $targetIds);

        return \ss\models\Tree::whereIn('id', $treesIds)->get();
    }

    private $relations2;

    public function getRelations($instance = '')
    {
        if (null === $this->relations2[$instance]) {
            $connections = $this->getAllConnections($instance);

            $this->relations2[$instance] = [];

            foreach ($connections as $connection) {
                $this->relations2[$instance][$connection->source_id][$connection->target_id] = 'source';
                $this->relations2[$instance][$connection->target_id][$connection->source_id] = 'target';
            }
        }

        return $this->relations2[$instance];
    }

    public function getRelation(\ss\commander\Svc\Panel $panel, \ss\commander\Svc\Panel $otherPanel, $instance = '')
    {
        $relations = $this->getRelations($instance);

        $tree = $panel->getTree();
        $otherTree = $otherPanel->getTree();

        if ($tree && $otherTree) {
            $treeId = $tree->id;
            $otherTreeId = $otherTree->id;

            if ($treeId == $otherTreeId) {
                $relation = 'split';
            } else {
                $relation = $relations[$treeId][$otherTreeId] ?? false;
            }

            return $relation;
        }
    }
}
