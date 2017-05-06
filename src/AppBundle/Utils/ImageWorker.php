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

        $im->setImageBackgroundColor(new \ImagickPixel('white'));
        $im = $im->mergeImageLayers(\Imagick::LAYERMETHOD_FLATTEN);
        $im->cropThumbnailImage(16, 16);
        $im->blurImage(0.3, 0);
        $im->setImageFormat('jpeg');
        $im->stripImage();
        $im->setImageCompression(\Imagick::COMPRESSION_JPEG);
        $im->setImageCompressionQuality(50);
        //$ret = $im->writeImage($to);
        //$im->destroy();
        return $im;
        //return file_exists($to);
    }
} 
