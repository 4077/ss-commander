<?php namespace ss\commander\ui\controllers\main;

class Xhr extends \Controller
{
    public $allow = self::XHR;

    public function focus()
    {
        $this->s('~:focus|', $this->data('value'), RR);
    }
}
