<?php
/**
 * Util class
 */
class Utils
{
    public function __construct()
    {
    }

    public static function json_response(array $body = array(), int $code = null)
    {
        header('x-powered-by:');
        header('Content-Type: application/json');
        header('Cache-Control: no-transform,public,max-age=0,s-maxage=0');
        self::set_http_code(200);
        if ($code) {
            self::set_http_code($code);
        }

        exit(json_encode($body));
    }

    public static function set_http_code_old(int $code = null)
    {
        if ($code) {
            header('X-PHP-Response=Code: ' . $code, true, $code);
            return $code;
        }
    }

    public static function set_http_code(int $code = null)
    {
        $phpSapiName = substr(php_sapi_name(), 0, 3);
        if ($phpSapiName == 'cgi' || $phpSapiName == 'fpm' && $code !== null) {
            header('Status: ' . $code);
        } else {
            $protocol = isset($_SERVER['SERVER_PROTOCOL']) ? $_SERVER['SERVER_PROTOCOL'] : 'HTTP/1.0';
            header($protocol . ' ' . $code);
        }
    }

}
