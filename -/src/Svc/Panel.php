<?php namespace ss\commander\Svc;

class Panel
{
    public $instance;

    public $commander;

    public $name;

    private $sMain;

    private $s;

    private $sectionFocus;

    public function __construct(\ss\commander\Svc $commander, $name)
    {
        $this->instance = $commander->instance . '/panel-' . $name;

        $this->commander = $commander;
        $this->name = $name;

        $this->sMain = &ap($this->commander->s, 'panels/' . $name);

        $this->s = apps('\ss\commander\ui\panel~:|' . $this->instance . '/tree-' . $this->getTree()->id);

        $this->sectionFocus = ap($this->commander->s, 'focus') == $name ? ap($this->s, 'focus') : '';
    }

    public function reload()
    {
        $this->commander->uiMainController->c('panel~:reload|' . $this->instance);
    }

    public function hasFocus($section = false)
    {
        if ($section) {
            return $section == $this->sectionFocus;
        } else {
            return $this->sectionFocus ? true : false;
        }
    }

    public function getPlugin($name)
    {
        if ($name) {
            $plugins = $this->getPlugins();

            return ap($plugins, $name);
        }
    }

    private $plugins;

    public function getPlugins()
    {
        if (null === $this->plugins) {
            $this->plugins = ss()->trees->plugins->get($this->getTree());
        }

        return $this->plugins;
    }

    private $enabledPlugins;

    public function getEnabledPlugins()
    {
        if (null === $this->enabledPlugins) {
            $plugins = $this->getPlugins();

            $this->enabledPlugins = [];

            foreach ($plugins as $name => $data) {
                if ($data['enabled'] ?? false) {
                    $this->enabledPlugins[$name] = $data;
                }
            }
        }

        return $this->enabledPlugins;
    }

    public function getOpposite()
    {
        return $this->commander->getPanel($this->name == 'a' ? 'b' : 'a');
    }

    public function getOppositeRelation()
    {
        return $this->commander->getRelation($this, $this->getOpposite());
    }

    private $tree;

    public function getTree()
    {
        if (null === $this->tree) {
            if (!$this->sMain['tree_id']) {
                if ($firstTree = \ss\models\Tree::query()->first()) {
                    $this->sMain['tree_id'] = $firstTree->id;
                }
            }

            $this->tree = \ss\models\Tree::find($this->sMain['tree_id']) ?? false;
        }

        return $this->tree;
    }

    public function setTree(\ss\models\Tree $tree)
    {
        $this->sMain['tree_id'] = $tree->id;

        if (!$catId = &ap($this->sMain, 'cat_id_by_tree_id/' . $tree->id)) {
            if ($rootCat = ss()->trees->getRootCat($tree->id)) {
                $this->setCat($rootCat);

                $catId = $rootCat->id;
            }
        }

        $this->tree = $tree;
    }

    private $cat;

    public function getCat()
    {
        if ($tree = $this->getTree()) {
            if (null === $this->cat) {
                $catId = &ap($this->sMain, 'cat_id_by_tree_id/' . $tree->id);

                if ($catId) {
                    $this->cat = \ss\models\Cat::find($catId);
                }

                if (null === $this->cat) {
                    $this->cat = ss()->trees->getRootCat($tree->id);
                }

                $catId = $this->cat->id;
            }

            if ($this->cat->tree_id != $tree->id) {
                $this->cat = ss()->trees->getRootCat($tree->id);
            }

            return $this->cat;
        }
    }

    public function setCat(\ss\models\Cat $cat)
    {
        if ($tree = $this->getTree() and $tree->id == $cat->tree_id) {
            ap($this->sMain, 'cat_id_by_tree_id/' . $tree->id, $cat->id);

            $this->cat = $cat;
        }
    }

    public function toggleOrdering()
    {
        $orderingField = &ap($this->sMain, 'ordering_field');

        if ($orderingField == 'name') {
            $orderingField = 'position';
        } else {
            $orderingField = 'name';
        }
    }
}
