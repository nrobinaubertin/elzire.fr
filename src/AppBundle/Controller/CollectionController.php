<?php

namespace AppBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;

class CollectionController extends Controller
{
    public function indexAction($dir)
    {
        // first we find the root directory of the collection
        $collectionDir = $this->get('kernel')->getRootDir() . '/../data/mariages/';
        foreach(scandir($collectionDir) as $directory) {
            if(
                is_dir($collectionDir.$directory)
                && preg_match("/".$dir."/i", $directory)
            ) {
                $dir = $directory;
                break;
            }
        }

        // now we need to find all the elements of the collection
        $elements = [];
        foreach(scandir($collectionDir.$dir) as $elementDir) {
            if($elementDir == "." || $elementDir == ".." || is_file($elementDir)) {
                continue;
            }
            $url = strtolower(preg_replace("/^\d+[_-]+/","", $elementDir));
            $element = array(
                "name" => preg_replace("/[_-]/", " ", $url),
                "url" => $url
            );
            foreach(scandir($collectionDir.$dir."/".$elementDir) as $file) {
                if(
                    is_file($collectionDir.$dir."/".$elementDir."/".$file)
                    && preg_match("/image/", mime_content_type($collectionDir.$dir."/".$elementDir."/".$file))
                    && preg_match("/^V/", $file)
                ) {
                    $element["miniature"] = $file;
                    break;
                }
            }
            $elements[] = $element;
        }

        return New Response(var_export($elements));
            
    }
}
