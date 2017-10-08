<?php
/**
 * Created by PhpStorm.
 * User: Amrit
 * Date: 2017-10-06
 * Time: 10:18 PM
 */

/**
 * Gather routes and methods
 *
 * @return mixed
 */
$routes = function () {
    return include_once __DIR__ . '/../config/routes.php';
};
$routes = $routes();

/**
 * Ensure we're handling a request we can handle
 */
if (!array_key_exists($_SERVER['REQUEST_METHOD'], $routes)) {
    errorCode(405, '405 Method Not Allowed');
    exit;
}

/**
 * Route to a new instance of the controller
 */
foreach ($routes[$_SERVER['REQUEST_METHOD']] as $regexp => $route) {
    // parse the route, skip entry if no match was found
    $regexp = sprintf('/^\\%s$/', $regexp);
    if (false === preg_match($regexp, $_SERVER['REQUEST_URI'], $argv) ||
        (count($argv) <= 0)
    ) {
        continue;
    }

    array_shift($argv);

    // make a new instance of the controller
    $controller = new $route['class']();

    // invoke the method with the parsed values
    call_user_func_array([$controller, $route['method']], $argv);
    exit;
}

/**
 * Any request not matched returns a 404
 */
errorCode(404, 'Not Found');
