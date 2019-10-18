<?php

require_once __DIR__.'/vendor/autoload.php';
require_once 'config.php';

use Verot\Upload\Upload;

// 上傳圖片
function uploadImg($file, $name, $dirName)
{
    $handle = new Upload($file);
    if ($handle->uploaded) {
        // 大圖
        $handle->file_new_name_body = $name;
        $handle->file_overwrite = true;
        $handle->image_resize = true;
        $handle->image_x = 400;
        $handle->image_y = 530;
        $handle->image_convert = 'png';
        $handle->image_ratio_crop = true;
        $handle->png_compression = 3;
        $handle->Process("../uploads/{$dirName}/normal/");
        if (!$handle->processed) {
            return 'error : '.$handle->error;
        }

        // 縮圖
        $handle->file_new_name_body = $name;
        $handle->file_overwrite = true;
        $handle->image_resize = true;
        $handle->image_x = 300;
        $handle->image_y = 400;
        $handle->image_convert = 'png';
        $handle->image_ratio_crop = true;
        $handle->png_compression = 3;
        $handle->Process("../uploads/{$dirName}/thumbs/");
        if (!$handle->processed) {
            return 'error : '.$handle->error;
        }
    }
}

// 刪除圖片
function deleteImg($fileName, $dirName)
{
    if (file_exists("../uploads/{$dirName}/normal/$fileName")) {
        unlink("../uploads/{$dirName}/normal/$fileName");
    }
    if (file_exists("../uploads/{$dirName}/thumbs/$fileName")) {
        unlink("../uploads/{$dirName}/thumbs/$fileName");
    }
}

function returnData($status, $msg, $error = '')
{
    if ($error == '') {
        $data = [
            'status' => $status,
            'msg' => $msg,
        ];
    } else {
        $data = [
            'status' => $status,
            'msg' => $msg,
            'error' => $error,
        ];
    }

    return $data;
}

// 資料檢查
function myFilter($var)
{
    $var = htmlspecialchars($var);

    return $var;
}
