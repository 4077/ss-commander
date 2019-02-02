<?php namespace ss\commander\ui\panel\controllers\main\topBar\treeSelector;

class Dropdown extends \Controller
{
    public function reload()
    {
        $this->jquery('|')->replace($this->view());
    }

    public function view()
    {
        $v = $this->v('|');

        $v->assign([
                       'TREES_TREE' => $this->treesTreeView()
                   ]);

        $this->css();

        return $v;
    }

    private function treesTreeView()
    {
        $this->css('>node');

        $panel = commanderPanel($this->_instance());
        $panelTree = $panel->getTree();

        $relations = $panel->commander->getRelations();
        $trees = $panel->commander->getTrees();

        $rootNode = ss()->trees->getRootNode();

        $connectedTreesIds = table_ids($trees);

        return $this->c('\std\ui\tree~:view|' . $this->_nodeInstance(), [
            'default'           => [

            ],
            'node_control'      => [
                '>node:view|',
                [
                    'root_node_id'        => $rootNode->id,
                    'node'                => '%model',
                    'panel_tree'          => pack_model($panelTree),
                    'relations'           => $relations,
                    'connected_trees_ids' => $connectedTreesIds
                    //                    'trees'        => $trees
                ]
            ],
            'query_builder'     => ':treeQueryBuilder|',
            'root_node_id'      => $rootNode->id,
            'expand'            => false,
            'sortable'          => false,
            'movable'           => false,
            'selected_node_id'  => $panelTree->id,
            'root_node_visible' => false,
            'filter_ids'        => false
            //            'filter_ids'       => $connectedTreesIds + [$rootNode->id]

        ]);
    }

    public function treeQueryBuilder()
    {
        return \ss\models\Tree::orderBy('position');
    }
}
