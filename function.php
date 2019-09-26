<?php

require_once 'plugin/upload/class.upload.php';

// 上傳圖片
function uploadImg($file, $name)
{
    $pic = new Upload($file, 'zh-TW');
    if ($pic->uploaded) {
        // 大圖
        $pic->file_new_name_body = $name;
        $pic->file_overwrite = true;
        $pic->img_resize = true;
        $pic->image_x = 600;
        $pic->image_y = 400;
        $pic->image_convert = 'png';
        $pic->image_ratio_crop = true;
        $pic->Process('../uploads/news/normal/');
        if (!$pic->processed) {
            return 'error : '.$pic->error;
        }

        // 縮圖
        $pic->file_new_name_body = $name;
        $pic->file_overwrite = true;
        $pic->img_resize = true;
        $pic->image_x = 300;
        $pic->image_y = 200;
        $pic->image_convert = 'png';
        $pic->image_ratio_crop = true;
        $pic->Process('../uploads/news/thumbs/');
        if (!$pic->processed) {
            return 'error : '.$pic->error;
        }
    }
}

// 資料檢查
function myFilter($var)
{
    $var = htmlspecialchars($var);

    return $var;
}
