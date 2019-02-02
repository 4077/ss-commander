<?php namespace ss\commander\ui\panel\controllers\main\bottomBar\createPanel\tree\node;

class Xhr extends \Controller
{
    public $allow = self::XHR;

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

//            $this->sCreatePanel = &$this->s('<<<|' . $this->_instance() . '/tree-' . $this->tree->id);
        } else {
            $this->lock();
        }
    }

    public function copy()
    {
        if ($cat = $this->unxpackModel('cat')) {
            $targetCat = $this->cat;

            ss()->cats->copyTo($cat, $targetCat);

            pusher()->trigger('ss/cat/update_cats', [
                'id' => $targetCat->id
            ]);

            pusher()->trigger('ss/tree/update_cats', [
                'id' => $targetCat->tree_id
            ]);
        }
    }

    public function copyWithData()
    {

    }
}
