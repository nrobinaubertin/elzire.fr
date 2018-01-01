<?php

namespace AppBundle\Utils;

class ImageWorker
{
    public function getPlaceholder($source)
    {
        $size = 16;
        $im = new \Imagick();
        $filename = sys_get_temp_dir()."/".self::getThumbnailHash($source, "placeholder", $size);
        if (file_exists($filename)) {
            try {
                if(!$im->readImage($filename)) {
                    throw new Exception("Impossible to read source image");
                }
                return $im;
            } catch(Exception $e) {
                return false;
            }
        }

        try {
            if(!$im->readImage($source)) {
                throw new Exception("Impossible to read source image");
            }
        } catch(Exception $e) {
            return false;
        }

        $im->thumbnailImage($size, $size);
        $im->blurImage(3, 1);
        $im->setImageFormat('jpeg');
        $im->setImageCompression(\Imagick::COMPRESSION_JPEG);
        $im->setSamplingFactors(['2x2', '1x1', '1x1']);
        $im->setImageCompressionQuality(50);
        $im->writeImage($filename);
        return $im;
    }

    public function applyWatermark($input, $watermark)
    {
        $water = new \Imagick();
        try {
            if(!$water->readImage($watermark)) {
                throw new Exception("");
            }
        } catch(Exception $e) {
            return $source;
        }

        $iw = $input->getImageWidth();
        $ih = $input->getImageHeight();
        $ww = $water->getImageWidth();
        $wh = $water->getImageHeight();

        // calculate the new watermark size (max 10% width or height)
        $nww = $iw * 0.8;
        $nwh = min($wh * $nww / $ww, $ih * 0.8);
        $nww = $ww * $nwh / $wh;

        $water->evaluateImage(\Imagick::EVALUATE_DIVIDE, 20, \Imagick::CHANNEL_ALPHA);
        $water->thumbnailImage($nww, $nwh);

        // watermark the fuck out of it
        $input->compositeImage($water, \Imagick::COMPOSITE_OVER, $iw/2 - $nww/2, $ih/2 - $nwh/2);

        $water->destroy();
        return $input;
    }

    public function displayMiniature($source, $size)
    {
        $filename = sys_get_temp_dir()."/".self::getThumbnailHash($source, "miniature", $size, "");
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

        $im->cropThumbnailImage($size, $size);
        $im->setImageFormat('jpeg');
        $im->setImageCompression(\Imagick::COMPRESSION_JPEG);
        $im->setSamplingFactors(['2x2', '1x1', '1x1']);
        $im->setImageCompressionQuality(85);

        header("Content-Type: image/jpeg");
        echo $im;

        $im->writeImage($filename);
        $im->destroy();
        return true;
    }

    public function displayImage($source, $width, $height, $watermark)
    {
        $filename = sys_get_temp_dir()."/".self::getThumbnailHash($source, "image", $width*$height, $watermark);
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

        if (!empty($watermark)) {
            $im = ImageWorker::applyWatermark($im, $watermark);
        }

        $im->setImageFormat('jpeg');
        $im->setImageCompression(\Imagick::COMPRESSION_JPEG);
        $im->setSamplingFactors(['2x2', '1x1', '1x1']);
        $im->setImageCompressionQuality(85);

        header("Content-Type: image/jpeg");
        echo $im;

        $im->writeImage($filename);
        $im->destroy();
        return true;
    }

    private function getThumbnailHash($source, $salt = "", $size = "", $watermark = "")
    {
        try {
            if ($source) {
                $filehash = sha1_file($source);
            } else {
                return "";
            }
        } catch(Exception $e) {
            return "";
        }
        try {
            if ($watermark) {
                $watermarkhash = sha1_file($watermark);
            } else {
                $watermarkhash = "";
            }
        } catch(Exception $e) {
            $watermarkhash = "";
        }
        return sha1($filehash.$size.$watermarkhash.$salt);
    }
} 
