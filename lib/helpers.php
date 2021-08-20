<?php
declare(strict_types = 1);

function str_prepend(string $str, string &$prepend2) : void {
    $prepend2 = $str . $prepend2;
}

function array_clear(array &$array) : void {
    while(!empty($array)) array_pop($array);
}

function getRandomString(int $length, string $from = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789') : string {
    $res = '';
    $from = str_shuffle($from);
    $size = strlen($from);
    for($i = 0; $i < $length; ++$i) $res .= $from[rand(0, $size - 1)];
    return $res;
}

function pathJoin(string ...$paths) : string {
    return join(DIRECTORY_SEPARATOR, $paths);
}

function file_empty(string $file) : void {
    $f = fopen($file, 'r+');
    if($f) {
        ftruncate($f, 0);
        fclose($f);
    }
}

function getInterfaceLanguage() : string {
    return isset($_COOKIE['lang']) && in_array($_COOKIE['lang'], getConfig('supported-langs')) ? $_COOKIE['lang'] : getConfig('default-lang');
}

function getConfig(string $cfg) {
    $spt = explode('.', $cfg);
    if(count($spt) == 1) return CONFIG[$cfg];
    $value = null;
    foreach($spt as $hunk) {
        if(is_null($value)) $value = CONFIG[$hunk];
        else $value = $value[$hunk];
    }
    return $value;
}

function hasConfig(string $cfg) : bool {
    $spt = explode('.', $cfg);
    if(count($spt) === 1) return isset(CONFIG[$cfg]);
    $value = null;
    foreach($spt as $hunk) {
        if(is_null($value)) {
            if(!isset(CONFIG[$hunk])) return false;
            $value = CONFIG[$hunk];
        }
        else {
            if(!isset($value[$hunk])) return false;
            $value = $value[$hunk];
        }
    }
    return true;
}

function hasSubDomain() : bool {
    $split = explode('.', $_SERVER['HTTP_HOST']);
    $cnt = count($split);
    return $split[$cnt - 1] == 'localhost' ? count($split) > 1 : count($split) > 2;
}

function getDomainName() : string {
    $split = explode('.', $_SERVER['HTTP_HOST']);
    $cnt = count($split);
    return $split[$cnt - 1] == 'localhost' ? 'localhost' : join('.', array_slice($split, $cnt - 2));
}

function getSubDomain() : ?string {
    if(!hasSubDomain()) return null;
    $split = explode('.', $_SERVER['HTTP_HOST']);
    $cnt = count($split);
    return join('.', array_slice($split, 0, $split[$cnt - 1] == 'localhost' ? $cnt - 1 : $cnt - 2));
}

function redirect(string $url) : void {
    header('Location: ' . $url);die;
}

function raise(int $error) : void {
    header('HTTP/1.1 ' . http_str_error($error));die;
}