<?php

namespace MyModule\Module;

/**
 * Class Tools
 * @package MyModule\Module
 */
class Tools
{
    /**
     * Outputs JSON content and terminates the application
     *
     * @param array $data
     * @param int   $statusCode
     *
     * @return void
     */
    public static function terminateWithJsonResponse(array $data, $statusCode = 200)
    {
        self::terminateWithResponse(
            json_encode($data),
            $statusCode,
            array('Content-Type' => 'application/json')
        );
    }

    /**
     * Outputs content with given headers and terminates the application
     *
     * @param string $content
     * @param int    $statusCode
     * @param array $headers
     *
     * @return void
     */
    public static function terminateWithResponse($content = '', $statusCode = 200, array $headers = array())
    {
        http_response_code($statusCode);

        foreach ($headers as $headerKey => $headerValue) {
            header($headerKey.': '.$headerValue);
        }

        // Goodbye, World!
        die($content);
    }
}
