<?php

$path = "upload";

if(!file_exists($path)){
    mkdir($path);
}
if($_SERVER['REQUEST_METHOD'] == 'POST') {


    $types = array("image/png", "image/jpeg", "image/jpg");

    if (isset($_FILES['image'])) {
        $image = $_FILES['image'];
    } elseif (isset($_FILES['watermark'])) {
        $image = $_FILES['watermark'];
    }
    if ($image['size'] == 0 || $image['size'] > 1024 * 1024 * 2) {
        $_SESSION['message'] = "Файл не выбран или превышает 2МБ";
        header("HTTP/1.1 302 Moved Temporarily");
        header("Location: index.php");
    } elseif (!in_array($image['type'], $types)) {
        $_SESSION['message'] = "Файл не соответствует";
        header("HTTP/1.1 302 Moved Temporarily");
        header("Location: index.php");
    }
        else  {
        $filename = basename($image['name']);
        if (move_uploaded_file($image['tmp_name'], "$path/$filename")) {
            list($width, $height) = getimagesize("$path/$filename");
            if (preg_match('/[.](GIF)|(gif)$/', $filename)) {
                $im = imagecreatefromgif("$path/$filename"); //если оригинал был в формате gif, то создаем изображение в этом же формате. Необходимо для последующего сжатия
            }
            if (preg_match('/[.](PNG)|(png)$/', $filename)) {
                $im = imagecreatefrompng("$path/$filename");//если оригинал был в формате png, то создаем изображение в этом же формате. Необходимо для последующего сжатия
            }

            if (preg_match('/[.](JPG)|(jpg)|(jpeg)|(JPEG)$/', $filename)) {
                $im = imagecreatefromjpeg("$path/$filename"); //если оригинал был в формате jpg, то создаем изображение в этом же формате. Необходимо для последующего сжатия
            }

        }


                $filename = $_FILES['watermark']['name'];
                $source = $_FILES['watermark']['tmp_name'];
                move_uploaded_file($source,"$path/$filename");//загрузка оригинала в папку $path

                if (preg_match('/[.](GIF)|(gif)$/', $filename)) {
                    $pattern = imagecreatefromgif("$path/$filename"); //если оригинал был в формате gif, то создаем изображение в этом же формате. Необходимо для последующего сжатия
                }
                if (preg_match('/[.](PNG)|(png)$/', $filename)) {
                    $pattern = imagecreatefrompng("$path/$filename");//если оригинал был в формате png, то создаем изображение в этом же формате. Необходимо для последующего сжатия
                }

                if (preg_match('/[.](JPG)|(jpg)|(jpeg)|(JPEG)$/', $filename)) {
                    $pattern = imagecreatefromjpeg("$path/$filename"); //если оригинал был в формате jpg, то создаем изображение в этом же формате. Необходимо для последующего сжатия
                }
            $imWidth = imagesx($im);
            $imHeight = imagesy($im);

                // 4. Create background pattern from image

                $patternWidth = imagesx($pattern);
                $patternHeight = imagesy($pattern);

                // 5. Repeatedly copy pattern to fill target image
                if ($patternWidth < $imWidth || $patternHeight < $imHeight) {
                    for ($patternX = 0; $patternX < $imWidth; $patternX += $patternWidth) {
                        for ($patternY = 0; $patternY < $imHeight; $patternY += $patternHeight) {
                            imagecopymerge($im, $pattern, $patternX, $patternY, 30, 30, $patternWidth, $patternHeight, 60);
                        }
                    }
                } else imagecopymerge($im, $pattern, 0, 0, 0, 0, $patternWidth, $patternHeight, 60);


            imagepng($im, 'wat.png');
            imagedestroy($im);
            echo '<img src="http://watermark/app/tests/Sergey/wat.png">';

            }
        }

?>


