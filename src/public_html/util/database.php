<?php
/**
 * Created by PhpStorm.
 * User: Amrit
 * Date: 2017-10-05
 * Time: 4:26 PM
 */

use App\Models\BaseModel;

/**
 * Instantiate and configure the MySQL data object
 */
try {
    BaseModel::$connection = new PDO($_SERVER['DB_DSN'], $_SERVER['DB_USERNAME'],
        $_SERVER['DB_PASSWORD']);
    BaseModel::$connection->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    BaseModel::$connection->setAttribute(PDO::ATTR_EMULATE_PREPARES, false);
} catch (Exception $e) {
    die ("A database error occurred");
}
