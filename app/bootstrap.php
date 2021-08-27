<?php

spl_autoload_register(function(string $className) {
    $split = explode('\\', $className);
    $cnt = count($split);
    $name = $split[$cnt - 1];
    $nsList = ['Legax\\Core', 'Legax\\UI\\Views\\Support', 'Legax\\UI\\Views'];
    $ns = array_slice($split, 0, $cnt - 1);
    if(in_array($ns, $nsList)) require_once pathJoin(APP_PATH, $name . '.php');
});