<?php

/*
Plugin Name: Publisher Collective Ads.Txt
Plugin URI: https://github.com/PathfinderMediaGroup/publisher-collective-ads-txt-wordpress
Description: Installs and frequently updates the ads.txt file for Publisher Collective websites
Version: 1.1.0
Author: Woeler
Author URI: https://www.pathfindermediagroup.com
License: GPL-3
*/

defined('ABSPATH') || exit;
add_action('plugins_loaded', 'PublisherCollective::setup');

final class PublisherCollective
{
    public function __construct()
    {
    }

    public static function setup()
    {
        $self = new PublisherCollective();
        add_action('wp', [$self, 'pc_cronstarter_activation']);
        add_action('fetch-publisher-collective-ads-txt', [$self, 'fetch_ads_txt']);
        add_filter('query_vars', [$self, 'display_pc_ads_txt']);
    }

    public static function pc_cronstarter_activation()
    {
        if (!wp_next_scheduled('fetch-publisher-collective-ads-txt')) {
            wp_schedule_event(time(), 'daily', 'fetch-publisher-collective-ads-txt');
        }
    }

    public static function fetch_ads_txt()
    {
        self::get_ads_txt_content_or_cache(true);
    }

    public static function pc_cronstarter_deactivate()
    {
        $timestamp = wp_next_scheduled('fetch-publisher-collective-ads-txt');
        wp_unschedule_event($timestamp, 'fetch-publisher-collective-ads-txt');
    }

    public static function pc_cronstarter_activate()
    {
        self::get_ads_txt_content_or_cache(true);
    }


    public static function display_pc_ads_txt($query_vars)
    {
        $request = isset($_SERVER['REQUEST_URI']) ? esc_url_raw(wp_unslash($_SERVER['REQUEST_URI'])) : false;
        if ('/ads.txt' === $request) {
            header('Content-Type: text/plain');
            echo esc_html(apply_filters('ads_txt_content', self::get_ads_txt_content_or_cache()));
            die();
        }

        return $query_vars;
    }

    public static function getServerName()
    {
        if (!empty(get_home_url())) {
            return rtrim(str_replace(['https://', 'http://', 'www.'], '', get_home_url()), '/');
        }
        if (!empty($_SERVER['SERVER_NAME'])) {
            return $_SERVER['SERVER_NAME'];
        }
        if (!empty($_SERVER['HTTP_HOST'])) {
            return $_SERVER['HTTP_HOST'];
        }

        return null;
    }

    public static function get_ads_txt_content_or_cache($renew = false)
    {
        $data = get_transient('publisher_collective_ads_txt');
        if (empty($data) || $renew) {
            $serverName = self::getServerName();
            $data = wp_remote_retrieve_body(wp_remote_get('https://kumo.network-n.com/adstxt/' . (empty($serverName) ? '' : ('?domain=' . $serverName))));
            if ($data !== false) {
                set_transient('publisher_collective_ads_txt', $data, 86400);
            }
        }

        return $data;
    }
}

register_deactivation_hook(__FILE__, ['PublisherCollective', 'pc_cronstarter_deactivate']);
register_activation_hook(__FILE__, ['PublisherCollective', 'pc_cronstarter_activate']);
