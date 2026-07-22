<?php

namespace App\Service;

use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\String\Slugger\SluggerInterface;

class PlatPhotoUploader
{
    public function __construct(
        private readonly string $platsUploadDirectory,
        private readonly SluggerInterface $slugger,
    ) {
    }

    public function upload(UploadedFile $photoFile): string
    {
        $originalFilename = pathinfo(
            $photoFile->getClientOriginalName(),
            PATHINFO_FILENAME
        );

        $safeFilename = $this->slugger->slug($originalFilename);
        $newFilename = $safeFilename.'-'.uniqid().'.'.$photoFile->guessExtension();

        try {
            $photoFile->move(
                $this->platsUploadDirectory,
                $newFilename
            );
        } catch (FileException $exception) {
            throw new FileException(
                "Erreur lors de l'envoi de la photo.",
                0,
                $exception
            );
        }

        return $newFilename;
    }
}