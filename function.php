<?php

// 資料檢查
function myFilter($var)
{
    $var = htmlspecialchars($var);

    return $var;
}
