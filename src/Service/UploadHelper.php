<?php

namespace App\Service;

use Gedmo\Sluggable\Util\Urlizer;
use Symfony\Component\HttpFoundation\File\UploadedFile;

class UploadHelper
{

    const PROFILE_IMAGES = 'profile_images';

    private string $uploadsPath;

    public function __construct(string $uploadsPath){

        $this->uploadsPath = $uploadsPath;
    }

    public function uploadProfileImage(UploadedFile $uploadedFile):string
    {
        $destination = $this->uploadsPath.'/'.self::PROFILE_IMAGES;

        $originalFileName = pathinfo($uploadedFile->getClientOriginalName(), PATHINFO_FILENAME);
        $newFileName = Urlizer::urlize($originalFileName) . '-' . uniqid('', false) . '.' . $uploadedFile->guessExtension();

        $uploadedFile->move(
            $destination,
            $newFileName
        );

        return $newFileName;
    }

}