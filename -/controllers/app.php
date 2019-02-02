<?php namespace ss\commander\controllers;

class App extends \Controller
{
    public function resetSession()
    {
        $builder = \ewma\models\Session::where('module_namespace', 'ss\commander\ui')->where('key', $this->app->session->getKey());
        $builder->delete();

        $builder = \ewma\models\Session::where('module_namespace', 'ss\commander\ui\panel')->where('key', $this->app->session->getKey());
        $builder->delete();

        return 'commander 2 session reset';
    }
}
