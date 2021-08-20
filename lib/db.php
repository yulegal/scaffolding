<?php
declare(strict_types = 1);

function dbConnect(array $options = []) {
    $db = getConfig('db.mysql');
    $conn = mysqli_connect(
        $options['host'] ?? $db['host'],
        $options['user'] ?? $db['user'],
        $options['password'] ?? $db['password'],
        $options['db'] ?? $db['name'],
        $options['port'] ?? $db['port']
    );
    if(!$conn) throw new Exception('Unable to establish database connection!');
    mysqli_set_charset($conn, 'utf8');
    return $conn;
}

function query($conn, string $sql, array $replacements = [], array $filterCallbacks = []) {
    $sqlSplit = explode('?', $sql);
    $sqlCount = count($sqlSplit);
    $replaceCount = count($replacements);
    if($replaceCount != 0 && $replaceCount == $sqlCount - 1) {
        $res = '';
        foreach($replacements as $k => $replacement) {
            $res .= $sqlSplit[$k];
            if(is_int($replacement) || is_double($replacement) || (is_string($replacement) && is_numeric($replacement))) $res .= $replacement;
            else if(is_bool($replacement)) $res .= $replacement ? 'true' : 'false';
            else if(is_string($replacement)) {
                foreach($filterCallbacks as $cb) $replacement = call_user_func($cb, $replacement);
                $replacement = '\'' . mysqli_real_escape_string($conn, $replacement) . '\'';
                $res .= $replacement;
            }
        }
        $res .= $sqlSplit[$k + 1];
        $sql = $res;
    }
    return mysqli_query($conn, $sql);
}