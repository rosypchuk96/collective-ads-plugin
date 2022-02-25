<?php

if(!function_exists('register_deactivation_hook')){
    function register_deactivation_hook(){}
}

if(!function_exists('register_activation_hook')){
    function register_activation_hook(){}
}

if(!function_exists('add_action')){
    function add_action(){}
}

if (!defined('ABSPATH')) {
    define('ABSPATH', __DIR__ . '/');
}
