<?php
/**
 * MySQL database details
 */
define('MYSQL_HOST', 'localhost'); # usually localhost
define('MYSQL_DATABASE', '');
define('MYSQL_USERNAME', '');
define('MYSQL_PASSWORD', '');

/**
 * Root path to this project. Likely just /
 * include both leading and trailing slashes!
 */
define('web_root', '/');

/**
 * just a random key. 
 */
define("csrf_key", "");

/**
 * Number of results to show per page
 */
define("per_page", 25);

/**
 * Imgur api key for uploading images
 */
define("imgur_key", "e41812326e69a80");

/**
 * Paypal configuration for smart buttons
 */
define("paypal_mode", "production"); # production or sandbox
define("paypal_sandbox", "");
define("paypal_production", "");

/**
 * Discord integration config
 * https://discord.com/developers
 */
const discord = [
    'api_url'       => 'https://discordapp.com/api', #likely doesn't need changed
    'client_id'     => '',
    'client_secret' => '',
    'guild_id'      => '',
    'bot_key'       => '',
    'redirect_uri'  => '',
    'webhook_url'   => '',
];

/**
 * Google recaptcha v3 keys
 */
const recaptcha = [
    'public'  => '6LcEsrAUAAAAAPjW2p3cYx-uI8VO5GfQDGcSBeNe',
    'private' => '6LcEsrAUAAAAAE9pC6ThHm14VRZYbEScfisIaesW'
];

/**
 * Github keys for updates page
 */
const github = [
    'api_url'       => 'https://api.github.com/repos/',
    'client_id'     => '',
    'client_secret' => '',
    'username'      => '',
    'repo'          => ''
];