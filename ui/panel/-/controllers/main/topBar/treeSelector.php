<?php namespace ss\commander\ui\panel\controllers\main\topBar;

class TreeSelector extends \Controller
{
    /**
     * @var $panel \ss\commander\Svc\Panel
     */
    private $panel;

    private $tree;

    private $cat;

    public function __create()
    {
        if ($this->panel = commanderPanel($this->_instance())) {
            $this->tree = $this->panel->getTree();
            $this->cat = $this->panel->getCat();
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

        if ($dev = true) {
            $this->css('>dropdown');
        }

        $relation = $panel->getOppositeRelation();

        $v->assign([
                       'FOCUS_CLASS'    => $this->panel->hasFocus('content') ? 'focus' : '',
                       'RELATION_CLASS' => $relation,
                       'CONTENT'        => $tree ? $tree->name : '...'
                   ]);

        $this->css();

        $this->widget(':|', [
            'panelName' => $this->panel->name,
            '.r'        => [
                'loadDropdown' => $this->_p('>xhr:loadDropdown|')
            ]
        ]);

        return $v;
    }
}
