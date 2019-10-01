<?php

/**
 * The bootstrap file handles routing and invokes the LightRailMVC::run() method.
 *
 * The directory of this file should not be one that can be browsed.
 * The included .htaccess file will instruct most Apache servers do deny any requests to this directory.
 */


// Load the LightRailMVC and LightRailPDOInstance classes.
require __DIR__ . '/LightRailMVC.php';


/////////////////////////////////////////////////////////////////////////////////////////
// Some convenience functions follow. They may be removed if you do not want to use them.

/**
 * Define the application's Base URL, i.e. the URL to the directory containing the index.php file.
 */
$baseURL = (empty($_SERVER['HTTPS']) ? 'http://' : 'https://') . $_SERVER['SERVER_NAME'];
if (!in_array($_SERVER['SERVER_PORT'], array(80, 443))) $baseURL .= ":{$_SERVER['SERVER_PORT']}";
$p = trim(dirname($_SERVER['SCRIPT_NAME']), '/');
if (!empty($p)) $baseURL .= "/$p";
define('BASEURL', "$baseURL");

/**
 * Return a full application URL from a partial path argument.
 */
function url($path)
{
    return BASEURL . '/' . trim(strval($url), '/');
}

/**
 * Safely quote variables for output in HTML.
 *
 * This helps prevent accidental HTML glitches and XSS attacks.
 */
function h($value)
{
    return htmlentities($value, ENT_QUOTES);
}

/**
 * Set the PDO constructor arguments for your database. (if desired)
 *
 * For reference see https://php.net/pdo.construct
 */
LightRailPDOInstance::setPDOArgs('', '', '');

/**
 * Safely quote variables for inclusion in SQL queries.
 */
function q($val)
{
    return LightRailPDOInstance::getInstance()->quote($val);
}

// end convenience functions
/////////////////////////////////////////////////////////////////////////////////////////


/////////////////////////////////////////////////////////////////////////////////////////
// Routing
$request = @$_GET['--request'];

// Run the framework with this request
LightRailMVC::run($request);
