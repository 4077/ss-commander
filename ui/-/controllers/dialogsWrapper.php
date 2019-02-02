<?php namespace ss\commander\ui\controllers;

class DialogsWrapper extends \Controller
{
    public function reload()
    {
        $this->jquery('|')->replace($this->view());
    }

    public function view()
    {
        $v = $this->v('|');

        $v->assign([
                       'CONTENT' => $this->getContent()
                   ]);

        $this->css();

        $this->widget(':|', [
            '.w' => [
                'commander' => $this->_w('~:|'),
            ]
        ]);

        $this->c('~app:disableKeyboard|');

        return $v;
    }

    private function getContent()
    {
        return $this->_call($this->data('content_call'))->perform();

//        $type = $this->data('type');
//
//        if ('container' === $type) {
//            return $this->c('\ss\cats\cp\container~:view|ss/commander', [], 'cat');
//        }
//
//        if ('page' === $type) {
//            return $this->c('\ss\cats\cp\page~:view|ss/commander', [], 'cat');
//        }
//
//        if ('folder' === $type) {
//            return $this->c('\ss\cats\cp\page~:view|ss/commander', [], 'cat'); // todo folder
//        }
//
//        if ('product' === $type) {
//            return $this->c('\ss\cats\cp\product~:view|ss/commander', [], 'product');
//        }
    }
}
