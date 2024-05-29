<?php

namespace App\Service;

use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Imagine\Gd\Imagine;
use Imagine\Image\Box;
use Symfony\Component\Filesystem\Filesystem;

class ImageManager
{
    private $imageRecipeDirectory;
    private $imagine;

    private const MAX_WIDTH  = 1024;
    private const MAX_HEIGHT = 768;

    public function __construct($imageRecipeDirectory) {
        $this->imageRecipeDirectory = $imageRecipeDirectory;
        $this->imagine     = new Imagine();
    }

    public function upload(UploadedFile $file)
    {
        $imageDirectory = null;
        $fileName       = 'assets/images/' . uniqid() . '.' . $file->getClientOriginalExtension();
        $imageDirectory = $this->getImageRecipeDirectory();

        try {
            $file->move($imageDirectory, $fileName);
        } catch (FileException $e) {
            echo($e);
        }

        return $fileName;
    }

    public function resize(string $filename): void
    {
        list($iwidth, $iheight) = getimagesize($filename);
             $ratio             = $iwidth / $iheight;
             $width             = self::MAX_WIDTH;
             $height            = self::MAX_HEIGHT;
        if ($width / $height > $ratio) {
            $width = $height * $ratio;
        } else {
            $height = $width / $ratio;
        }

        $photo = $this->imagine->open($filename);
        $photo->resize(new Box($width, $height))->save($filename);
    }

    public function getImageRecipeDirectory()
    {
        return $this->imageRecipeDirectory;
    }

    public function deleteImage($fileName): void
    {
        if ($fileName !==null) {
            $fileSystem = new Filesystem();
            $fileSystem->remove($fileName);
        }
    }
}