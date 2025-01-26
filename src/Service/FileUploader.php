<?php

namespace App\Service;

use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\String\Slugger\SluggerInterface;

class FileUploader
{
    private const API_KEY = 'YOUR_API_KEY';
    private const MAX_WIDTH = 400;
    private const MAX_HEIGHT = 300;

    public function __construct(
        private string $uploadsDirectory,
        private SluggerInterface $slugger,
    ) {}

    public function upload(UploadedFile $file): string
    {
        $originalFilename = pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME);
        $safeFilename = $this->slugger->slug($originalFilename);
        $fileName = $safeFilename . '.' . $file->guessExtension();

        try {
            $file->move($this->getUploadsDirectory(), $fileName);
        } catch (FileException $e) {
            // ... handle exception if something happens during file upload
            throw new FileException($e->getMessage());
        }

        return $fileName;
    }

    public function uploadResize(File $file): string
    {
        $originalFilename = pathinfo($file->getFilename(), PATHINFO_FILENAME);
        $safeFilename = $this->slugger->slug($originalFilename);
        $fileName = $this->defineFileName($safeFilename, $file->guessExtension());

        $imageWidth = getimagesize($file)[0];
        $imageHeight = getimagesize($file)[1];
        $ratio = $imageWidth / $imageHeight;
        $width = self::MAX_WIDTH;
        $height = self::MAX_HEIGHT;

        // Resize image
        if ($imageHeight > self::MAX_HEIGHT || $imageWidth > self::MAX_WIDTH) {
            if ($width / $height > $ratio) {
                $width = round($height * $ratio);
            } else {
                $height = round($width / $ratio);
            }

            try {
                \Tinify\setKey(self::API_KEY);
                $source = \Tinify\fromFile($file);
                $resizedSource = $source->resize([
                    'method' => 'fit',
                    'width' => $width,
                    'height' => $height
                ]);
                $resizedSource->toFile($this->getUploadsDirectory() . '/' . $fileName);
            } catch (FileException $e) {
                throw new FileException($e->getMessage());
            }
        } else {
            // Just move the image with no resize
            try {
                $file->move($this->getUploadsDirectory(), $fileName);
            } catch (FileException $e) {
                throw new FileException($e->getMessage());
            }
        }

        return $fileName;
    }

    public function getUploadsDirectory(): string
    {
        return $this->uploadsDirectory;
    }

    private function defineFileName(string $fileName, string $extension): string
    {
        // Return file name : Filename . Datetime . Uuid . Extension
        return $fileName . '-' . (new \DateTime('now', new \DateTimeZone('Europe/Paris')))->format('YmdHis') . '-' . uniqid() . '.' . $extension;
    }
}
