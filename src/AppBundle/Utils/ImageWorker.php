<?php

namespace AppBundle\Utils;

class ImageWorker
{
    public function getPlaceholder($source)
    {
        $im = new \Imagick();
        try {
            if(!$im->readImage($source)) {
                throw new Exception("Impossible to read source image");
            }
        } catch(Exception $e) {
            return false;
        }

        $im->thumbnailImage(32, 32);
        $im->blurImage(3, 1);
        $im->setImageFormat('jpeg');
        $im->setImageCompression(\Imagick::COMPRESSION_JPEG);
        $im->setImageCompressionQuality(50);
        return $im;
    }

    public function displayImage($source, $width, $height)
    {
        $filename = sys_get_temp_dir()."/".sha1($source.$width.$height);
        if (file_exists($filename)) {
            header("Content-Type: image/jpeg");
            readfile($filename);
            return true;
        }

		$im = new \Imagick();
		try {
			if(!$im->readImage($source)) {
                throw new Exception("Impossible to read source image");
			}
		} catch(Exception $e) {
			return false;
		}

		$im->thumbnailImage($width, $height, true);
		$im->setImageFormat('jpeg');
		$im->setImageCompression(\Imagick::COMPRESSION_JPEG);
		$im->setImageCompressionQuality(90);

        header("Content-Type: image/jpeg");
        echo $im;

		$im->writeImage($filename);
		$im->destroy();
		return true;
    }
} 
