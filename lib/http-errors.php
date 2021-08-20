<?php
define('HTTP_404_ERROR', '404 Not Found');

define('HTTP_405_ERROR', '405 Method Not Allowed');

define('HTTP_400_ERROR', '400 Bad Request');

define('HTTP_403_ERROR', '403 Forbidden');

define('HTTP_401_ERROR', '401 Unauthorized');

function http_str_error(int $error) {
    return defined('HTTP_' . $error . '_ERROR') ? constant('HTTP_' . $error . '_ERROR') : null;
}