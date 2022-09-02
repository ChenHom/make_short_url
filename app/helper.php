<?php

function base62($num)
{
    $index = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
    $res = '';
    do {
        $res = $index[$num % 62] . $res;
        $num = intval($num / 62);
    } while ($num);
    return $res;
}
