<?php namespace ss\commander\ui\panel\controllers\main\bottomBar\createPanel\tree;

class Node extends \Controller
{
    private $tree;

    private $cat;

    private $viewInstance;

    public function __create()
    {
        if ($cat = $this->unpackModel('cat')) {
            $this->cat = $cat;
            $this->tree = $this->unpackModel('tree') ?: $cat->tree;

            $this->viewInstance = $cat->id;
        } else {
            $this->lock();
        }
    }

    public function reload()
    {
        $this->jquery('|' . $this->viewInstance)->replace($this->view());
    }

    public function view()
    {
        $v = $this->v('|' . $this->viewInstance);

        $tree = $this->tree;
//        $treeXPack = xpack_model($tree);

        $cat = $this->cat;
        $catXPack = xpack_model($cat);

        $isRootNode = $this->data['root_node_id'] == $this->cat->id;

        if ($tree->mode == 'folders') {
            $iconClass = 'fa fa-folder';
        } else {
            $iconClass = $cat->type == 'container' ? 'fa fa-cube' : 'fa fa-file';
        }

        $v->assign([
                       'ID'               => $cat->id,
                       'ROOT_CLASS'       => $isRootNode ? 'root' : '',
                       'CONTAINER_CLASS'  => $cat->type == 'container' ? 'container' : '', //
                       'ICON_CLASS'       => $iconClass,
                       'NAME'             => $isRootNode ? '' : ss()->cats->getShortName($cat),
                       'DISABLED_CLASS'   => $this->cat->enabled ? '' : 'disabled',
                       'WITH_DATA_BUTTON' => $this->c('\std\ui button:view', [
                           'visible' => !$isRootNode,
                           'path'    => '>xhr:copyWithData|',
                           'data'    => [
                               'cat' => $catXPack,
                               //                               'tree' => $treeXPack
                           ],
                           'class'   => 'button duplicate',
                           'title'   => 'Копировать с данными',
                           'icon'    => 'fa fa-database'
                       ])
                   ]);

        $this->c('\std\ui button:bind', [
            'selector' => $this->_selector('|' . $this->viewInstance),
            'path'     => '>xhr:copy|',
            'data'     => [
                'cat' => $catXPack,
                //                'tree' => $treeXPack
            ]
        ]);

        $this->css();

        return $v;
    }
}
