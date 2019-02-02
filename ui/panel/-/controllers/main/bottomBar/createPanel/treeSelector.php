<?php namespace ss\commander\ui\panel\controllers\main\bottomBar\createPanel;

class TreeSelector extends \Controller
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

            $this->sCreatePanel = &$this->s('<|' . $this->_instance() . '/tree-' . $this->tree->id);
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

        $selectedTree = \ss\models\Tree::find($this->sCreatePanel['selected_tree_id']);

        if ($dev = true) {
            $this->css('>dropdown');
        }

        $v->assign([
                       'CONTENT' => $selectedTree ? $selectedTree->name : '...'
                   ]);

        $this->css();

        $this->widget(':|', [
            'panelName' => $panel->name,
            '.r'        => [
                'loadDropdown' => $this->_p('>xhr:loadDropdown|')
            ]
        ]);

        return $v;
    }
}
