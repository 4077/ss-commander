<?php namespace ss\commander\ui\controllers\main;

class App extends \Controller
{
    public function enableKeyboard()
    {
        $this->jsCall('ewma.trigger', 'ss/commander/enableKeyboard');
    }

    public function disableKeyboard()
    {
        $this->jsCall('ewma.trigger', 'ss/commander/disableKeyboard');
    }
}
