<?php
ini_set("display_errors", "1");
error_reporting(E_ALL);
$width  = 300;
$thumbwidth  = 300;
$height = 300;
$images = scandir("images", 1);
$crop   = 0;
$quality = 100;
print_r($images);
foreach ($images as $key => $value) {
    $path     = '/var/www/html/test/images/';
    $croppath = '/var/www/html/test/300/';
    $src      = $path . $value;
    $dst      = $croppath . '300_' . $value;
    if (file_exists($src)) {
        $mimetype = mime_content_type($path . $value);
        echo $mimetype . " ";
        $arr = explode('/', $mimetype);
        if ($arr[0] == 'image' || $arr[0] == '1image') {
            if (!list($w, $h) = getimagesize($src)) {
                echo "Unsupported picture type!";
            }

            $im1 = ImageCreateFromJPEG($src);

            //if(function_exists("exif_read_data")){
            $exif = exif_read_data($src);
            if (!empty($exif['Orientation'])) {
                switch ($exif['Orientation']) {
                    case 8:
                        $im1 = imagerotate($im1, 90, 0);
                        break;
                    case 3:
                        $im1 = imagerotate($im1, 180, 0);
                        break;
                    case 6:
                        $im1 = imagerotate($im1, -90, 0);
                        break;
                }
            }
            //}
            $info = @getimagesize($src);

            $width = $info[0];

            $w2 = ImageSx($im1);
            $h2 = ImageSy($im1);
            $w1 = ($thumbwidth <= $info[0]) ? $thumbwidth : $info[0];

            $h1  = floor($h2 * ($w1 / $w2));
            $im2 = imagecreatetruecolor($w1, $h1);

            imagecopyresampled($im2, $im1, 0, 0, 0, 0, $w1, $h1, $w2, $h2);
            $path = addslashes($dst);
            ImageJPEG($im2, $path, $quality);
            ImageDestroy($im1);
            ImageDestroy($im2);
            echo true;
        } else {
            echo ' == |' . $value;
        }

    }

}
