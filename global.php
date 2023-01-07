<?php

function strToBin($input) {
    if (!is_string($input)){
        return false;
    }
    $ret = '';
    for ($i = 0; $i < strlen($input); $i++){
        $temp = decbin(ord($input[$i]));
        $ret .= str_repeat("0", 8 - strlen($temp)) . $temp;
    }
    return $ret;
}

function binToStr($input) {
    if (!is_string($input)){
        return false;
    }
    $ret = '';
    for ($i = 0; $i < strlen($input); $i += 8)
    {
        $temp = bindec(substr($input, $i, 8));
        $ret .= chr($temp);
    }
    return $ret;
}

?>