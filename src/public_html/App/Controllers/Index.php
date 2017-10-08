<?php
/**
 * Created by PhpStorm.
 * User: Amrit
 * Date: 2017-10-06
 * Time: 10:22 PM
 */

namespace App\Controllers;


class Index extends BaseController
{
    /**
     * Runs when a GET on / is called
     */
    public function index()
    {
        echo '<h1>It works!</h1>';
    }
}