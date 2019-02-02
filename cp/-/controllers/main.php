<?php namespace ss\commander\cp\controllers;

class Main extends \Controller
{
    public function reload()
    {
        $this->jquery('|')->replace($this->view());
    }

    public function view()
    {
        $v = $this->v('|');

        $v->assign([
                       'CONTENT' => ''
                   ]);

        $this->css();

        return $v;
    }
}
