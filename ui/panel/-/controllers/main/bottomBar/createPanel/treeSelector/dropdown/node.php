<?php namespace ss\commander\ui\panel\controllers\main\bottomBar\createPanel\treeSelector\dropdown;

class Node extends \Controller
{
    public function view()
    {
        $node = $this->unpackModel('node');
        $panelTree = $this->unpackModel('panel_tree');

//        $panel = commanderPanel($this->_instance());

        $v = $this->v('|' . $node->id);

        $nodeXPack = xpack_model($node);

        $compatible = $panelTree->mode == $node->mode;

        $v->assign([
                       'ID'               => $node->id,
                       'MODE_ICON_CLASS'  => 'fa fa-' . ($node->mode == 'folders' ? 'folder' : 'file'),
                       'COMPATIBLE_CLASS' => $compatible ? 'compatible' : 'not_compatible',
                       'NAME'             => $node->name ?: '...'
                   ]);


        if ($compatible) {
            $this->c('\std\ui button:bind', [
                'selector' => $this->_selector('|' . $node->id),
                'path'     => '>xhr:select|',
                'data'     => [
                    'node' => $nodeXPack
                ]
            ]);
        }

        $this->css(':\js\jquery\ui icons');

        return $v;
    }
}
