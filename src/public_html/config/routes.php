<?php
/**
 * Created by PhpStorm.
 * User: Amrit
 * Date: 2017-10-05
 * Time: 2:32 PM
 */

use App\Controllers\Company as CompanyController;
use App\Controllers\Index as IndexController;

return [
    'GET' => [
        '/' => [
            'class' => IndexController::class,
            'method' => 'index',
        ],
        '/(\d+)' => [
            'class' => CompanyController::class,
            'method' => 'index',
        ],
    ],
    'POST' => [
        '/' => [
            'class' => CompanyController::class,
            'method' => 'create',
        ],
    ],
    'PUT' => [
        '/(\d+)' => [
            'class' => CompanyController::class,
            'method' => 'update',
        ],
    ],
    'DELETE' => [
        '/(\d+)' => [
            'class' => CompanyController::class,
            'method' => 'delete',
        ],
    ],
];