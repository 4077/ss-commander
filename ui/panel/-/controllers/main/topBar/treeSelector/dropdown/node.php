<?php namespace ss\commander\ui\panel\controllers\main\topBar\treeSelector\dropdown;

class Node extends \Controller
{
    public function view()
    {
        $node = $this->unpackModel('node');
        $panel = commanderPanel($this->_instance());

        $v = $this->v('|' . $node->id);

        $nodeXPack = xpack_model($node);

        $isRootNode = $this->data['root_node_id'] == $node->id;

        $oppositeTreeId = $panel->getOpposite()->getTree()->id;

        $relations = $this->data['relations'];

        if ($oppositeTreeId == $node->id) {
            $relation = 'split';
        } else {
            $relation = $relations[$node->id][$oppositeTreeId] ?? false;
        }

        $connectedTreeIds = $this->data('connected_trees_ids');

        $v->assign([
                       'ID'              => $node->id,
                       'ROOT_CLASS'      => $isRootNode ? 'root' : '',
                       'CONNECTED_CLASS' => in_array($node->id, $connectedTreeIds) ? '' : 'not_connected',
                       'MODE_ICON_CLASS' => !$isRootNode ? 'fa fa-' . ($node->mode == 'folders' ? 'folder' : 'file') : 'hidden',
                       'RELATION_CLASS'  => $node->mode . ' ' . $relation,
                       'NAME'            => $node->name ?: '...'
                   ]);

        $this->c('\std\ui button:bind', [
            'selector' => $this->_selector('|' . $node->id),
            'path'     => '>xhr:select|',
            'data'     => [
                'node' => $nodeXPack
            ]
        ]);

        $this->css(':\js\jquery\ui icons');

        return $v;
    }
}
