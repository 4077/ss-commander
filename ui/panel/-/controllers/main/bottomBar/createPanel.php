<?php namespace ss\commander\ui\panel\controllers\main\bottomBar;

class CreatePanel extends \Controller
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
                'selected_tree_id' => false,
                'pinned'           => false
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
                       'TREE_SELECTOR' => $this->c('>treeSelector:view|'),
                       'TREE'          => $this->c('>tree:view|'),
                   ]);

        $this->css();

        return $v;
    }
}
