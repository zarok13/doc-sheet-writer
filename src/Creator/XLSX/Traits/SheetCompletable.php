<?php

namespace Zarok13\DocSheetWriter\Creator\XLSX\Traits;

use RecursiveDirectoryIterator;
use RecursiveIteratorIterator;
use ZipArchive;

trait SheetCompletable {
    public function zipData($fileName)
    {
        $rootPath = realpath($this->rootDirectory);
        $zip = new ZipArchive();
        $zip->open($fileName . '.zip', ZipArchive::CREATE | ZipArchive::OVERWRITE);

        $files = new RecursiveIteratorIterator(
            new RecursiveDirectoryIterator($rootPath),
            RecursiveIteratorIterator::LEAVES_ONLY
        );

        foreach ($files as $name => $file) {
            if (!$file->isDir()) {
                // Get real and relative path for current file
                $filePath = $file->getRealPath();
                $relativePath = substr($filePath, strlen($rootPath) + 1);

                // Add current file to archive
                $zip->addFile($filePath, $relativePath);
            }
        }

        $zip->close();

        rename($fileName . '.zip', $fileName . '.xlsx');

        return $this;
    }

    public function cleanUp()
    {
        $rootPath = realpath($this->rootDirectory);
        $it = new RecursiveDirectoryIterator($rootPath, RecursiveDirectoryIterator::SKIP_DOTS);
        $files = new RecursiveIteratorIterator(
            $it,
            RecursiveIteratorIterator::CHILD_FIRST
        );
        foreach ($files as $file) {
            if ($file->isDir()) {
                rmdir($file->getRealPath());
            } else {
                unlink($file->getRealPath());
            }
        }
        rmdir($rootPath);
    }
}