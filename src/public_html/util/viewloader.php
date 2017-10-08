<?php
/**
 * Created by PhpStorm.
 * User: Amrit
 * Date: 2017-10-06
 * Time: 7:13 PM
 */

/**
 * Quick and dirty http error generator
 *
 * @param $code
 * @param $message
 */
function errorCode($code, $message)
{
    $message = sprintf('%s %s', $code, $message);
    http_response_code($code);
    header($message);
    header('Content-Type: application/json; charset=utf-8');
    $out = [
        'error' => true,
        'code' => $code,
        'message' => $message
    ];
    echo json_encode($out,
        $_SERVER['APP_DEBUG'] == 'true' ? JSON_PRETTY_PRINT : null);
}

/**
 * Interpolate a view
 *
 * @param $view
 */
function loadView($view)
{
    $prefix = __DIR__ . '/App/Views/';
    $view = $prefix . $view;
    if (!file_exists($view)) {
        errorCode(500, 'An internal error occurred.');
        exit;
    }
    include $view;
}