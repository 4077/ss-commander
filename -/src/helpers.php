<?php

function commander($instance = '')
{
    return \ss\commander\Svc::getInstance($instance);
}

/**
 * @param $instance
 *
 * @return \ss\commander\Svc\Panel
 */
function commanderPanel($instance)
{
    $commanderInstance = path_slice($instance, 0, -1);
    $panelNameSegment = path_slice($instance, -1);
    $panelName = substr($panelNameSegment, 6);

    return commander($commanderInstance)->getPanel($panelName);
}

function commanderc($instance = '')
{
    $args = func_get_args();

    if ($args) {
        return call_user_func_array([commander($instance)->moduleRootController, 'c'], $args);
    } else {
        return commander($instance)->moduleRootController;
    }
}
