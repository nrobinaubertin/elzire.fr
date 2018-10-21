<?php

namespace AppBundle\Command;

use AppBundle\Utils\ImageWorker;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Finder\Finder;

class GenThumbnailsCommand extends ContainerAwareCommand
{
    protected function configure()
    {
        $this->setName("elzire:gen-thumbs")
            ->setDescription("Generate thumbnails of all images")
            ->setHelp("");
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $watermark = $this->getContainer()->get('kernel')->getRootDir()."/../web/assets/watermark.png";
        $rootDir = realpath($this->getContainer()->get('kernel')->getRootDir()."/..");
        $finder = new Finder();
        $imageWorker = new ImageWorker($rootDir."/data/cache/thumbs");
        if (!file_exists($rootDir."/data")) {
            $output->writeln("No data folder !");
            return;
        }
        $finder->files()->in($rootDir."/data")->name("/.(jpg|png)$/");
        $i = 0;
        foreach ($finder as $file) {
            $i++;
            $output->writeln("<info>[".$i."/".$finder->count()."]</info> ".$file->getRelativePathname());
            $imageWorker->displayImage($file->getRealPath(), 1200, 1200, $watermark);
            $imageWorker->displayImage($file->getRealPath(), 1024, 1024, $watermark);
            $imageWorker->displayMiniature($file->getRealPath(), 400);
            $imageWorker->getPlaceholder($file->getRealPath());
        }
    }
}
