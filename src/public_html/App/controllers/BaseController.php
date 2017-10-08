<?php
/**
 * Created by PhpStorm.
 * User: Amrit
 * Date: 2017-10-06
 * Time: 11:38 AM
 */

namespace App\Controllers;


use App\Models\BaseModel;

abstract class BaseController
{
    /**
     * Get the contents of the input buffer
     *
     * @return bool|string
     */
    protected function getInput()
    {
        return file_get_contents("php://input");
    }

    /**
     * Sets the necessary headers and generates output
     *
     * @param        $payload
     * @param string $contentType
     */
    protected function setOutput(
        $payload,
        $contentType = 'text/html; charset=utf-8'
    ) {
        if ($payload instanceof BaseModel) {
            $payload = $payload->toArray();
        }
        if (is_array($payload)) {
            $payload = json_encode($payload,
                $_SERVER['APP_DEBUG'] == 'true' ? JSON_PRETTY_PRINT : null);
            $contentType = 'application/json; charset=utf8';
        }
        header('Content-Type: ' . $contentType);
        echo $payload;
    }

}