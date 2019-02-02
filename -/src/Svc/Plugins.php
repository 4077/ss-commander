<?php namespace ss\commander\Svc;

class Plugins extends \Ewma\Service\Service
{
    protected $services = [
        'moderation'
    ];

    /**
     * @var \ss\commander\Plugins\Moderation
     */
    public $moderation = \ss\commander\Plugins\Moderation::class;
}
