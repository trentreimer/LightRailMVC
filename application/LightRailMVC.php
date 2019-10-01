<?php

/**
 * LightRail minimalist MVC application framework
 *
 * LICENSE: This source file is subject to the MIT license
 * that is available through the word-wide-web at the following URL:
 * http://www.opensource.org/licenses/mit-license.php
 *
 * @author    Trent Reimer
 * @copyright 2019 Trent Reimer
 * @version   3.0
 */

class LightRailMVC
{
    /**
     * Run the MVC application.
     *
     * @param  string $request  the controller, action and optional arguments in path syntax
     *                         i.e. "controller/action/[arg1]"
     * @return null
     *
     * NOTE: You must provide at least the controller name to the $request argument.
     */
    public static function run($request = '')
    {
        // Process the application URL request.
        $request = trim(strval($request), '/');
        define('MVC_REQUEST', $request);
        $request = empty($request) ? array() : explode('/', $request);

        if (!empty($request[0])) $controller = str_replace(' ', '', ucwords(preg_replace('/[^a-z|A-Z|0-9]/', ' ', $request[0])));
        if (empty($controller)) $controller = 'Site';

        define('CONTROLLER', $controller);
        $controller .= 'Controller';

        if (!empty($request[1])) $action = lcfirst(str_replace(' ', '', ucwords(preg_replace('/[^a-z|A-Z|0-9]/', ' ', $request[1]))));
        if (empty($action)) $action = 'index';

        define('ACTION', $action);
        $action .= 'Action';

        if (method_exists($controller, $action)) {
            $controller = new $controller();
            call_user_func_array(array($controller, $action), array_slice($request, 2));
            exit;
        }

        // If we're still here we need to output a Not Found error message.
        header('Status: 404 Not Found');
        header('HTTP/1.1 404 Not Found');

        // NOTE: You can modify this HTML or include an external file instead.
        echo '<!doctype html>
<html>
  <head>
    <title>404 Not Found</title>
  </head>
  <body>
    <h1>Not Found</h1>
    <p>The requested URL ' . $_SERVER['REQUEST_URI'] . ' was not found on this server.</p>
    <hr>
    <address>' . $_SERVER['SERVER_NAME'] . '</address>
  </body>
</html>
';
    }
}

/**
 * Return a usable PDO instance for use in controllers and models
 *
 * PDO connection arguments need to be supplied first with
 * LightRailPDOInstance::setPDOArgs(arg1, arg2, arg3);
 */
final class LightRailPDOInstance
{
    private static $pdoInstance = null;
    private static $pdoArgs = array();

    /**
     * Set PDO arguments, either as a series of scalar values or as a single array
     */
    public static function setPDOArgs($arg1 = null, $arg2 = null, $arg3 = null)
    {
        if (is_array($arg1)) {
            self::$pdoArgs = $arg1;
        } else {
            self::$pdoArgs = array($arg1, $arg2, $arg3);
        }
    }

    public static function getInstance()
    {
        if (self::$pdoInstance === null) {
            if (count(self::$pdoArgs) == 0) {
                die('Error: no database configuration supplied to handler');
            }

            $args = self::$pdoArgs;

            try {
                self::$pdoInstance = new PDO(@$args[0], @$args[1], @$args[2]);
            } catch(Exception $e) {
                die('Error: unable to connect to database');
            }
        }

        return self::$pdoInstance;
    }
}

ini_set('include_path', ini_get('include_path') . PATH_SEPARATOR . __DIR__);

/**
 * Automatically load controller and model classes
 */
spl_autoload_register(function($class)
{
    if (substr($class, -10) == 'Controller') {
        @include __DIR__ . '/controllers/' . $class . '.php';
    } elseif (substr($class, -6) == 'Record') {
        @include __DIR__ . '/models/' . $class . '.php';
    }
});
