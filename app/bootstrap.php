<?php

spl_autoload_register(function(string $className) {
    $split = explode('\\', $className);
    $cnt = count($split);
    require_once pathJoin(APP_PATH, $split[$cnt - 1] . '.php');
});