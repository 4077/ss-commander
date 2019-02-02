<?php namespace ss\commander\ui\panel\controllers\main\bottomBar\createPanel;

class Tree extends \Controller
{
    /**
     * @var $panel \ss\commander\Svc\Panel
     */
    private $panel;

    private $tree;

    private $cat;

    private $sCreatePanel;

    public function __create()
    {
        if ($this->panel = commanderPanel($this->_instance())) {
            $this->tree = $this->panel->getTree();
            $this->cat = $this->panel->getCat();

            $this->sCreatePanel = &$this->s('<|' . $this->_instance() . '/tree-' . $this->tree->id, [
                'selected_tree_id' => false
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
                       'CONTENT' => $this->treeView()
                   ]);

        $this->css();

        return $v;
    }

    private function treeView()
    {
        $this->css('>node');

        if ($tree = \ss\models\Tree::find($this->sCreatePanel['selected_tree_id'])) {

//        $panel = commanderPanel($this->_instance());
//        $panelTree = $panel->getTree();

//        $relations = $panel->commander->getRelations();
//        $trees = $panel->commander->getTrees();

//        $rootNode = ss()->trees->getRootNode();

            {
                $rootNode = ss()->trees->getRootCat($tree->id);
            }

//        $connectedTreesIds = table_ids($trees);

            return $this->c('\std\ui\tree~:view|' . $this->_nodeInstance(), [
                'default'           => [

                ],
                'node_control'      => [
                    '>node:view|',
                    [
                        'root_node_id' => $rootNode->id,
                        'cat'          => '%model',
                        'tree'         => pack_model($tree),
                        //                    'relations'           => $relations,
                        //                    'connected_trees_ids' => $connectedTreesIds
                        //                    'trees'        => $trees
                    ]
                ],
                'query_builder'     => ':treeQueryBuilder|',
                'root_node_id'      => $rootNode->id,
                'expand'            => false,
                'sortable'          => false,
                'movable'           => false,
                //            'selected_node_id'  => $panelTree->id,
                'root_node_visible' => false,
                'filter_ids'        => false
                //            'filter_ids'       => $connectedTreesIds + [$rootNode->id]

            ]);
        }
    }

    public function treeQueryBuilder()
    {
        return \ss\models\Cat::where('tree_id', $this->sCreatePanel['selected_tree_id'])->orderBy('type')->orderBy('position');
    }
}
