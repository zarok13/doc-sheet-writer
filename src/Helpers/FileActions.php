<?php

namespace Zarok13\DocSheetWriter\Helpers;

use Exception;
use Zarok13\DocSheetWriter\Contracts\IFileActions;

class FileActions implements IFileActions
{
    /**
     * creates directory with the given directory path
     *
     * @param string $directories
     * @param string $subDirectory
     * @return string
     */
    public function createDirectory(string $directoryPath, string $directory): string
    {
        $path = $directoryPath . '/' . $directory;
        if (!mkdir($path, 0777, true)) {
            throw new Exception("Unable to create folder: $path");
        }

        return $path;
    }

    /**
     * creates file with content in the given directory
     *
     * @param string $directoryPath
     * @param string $fileName
     * @param string $fileContent
     * @return string
     */
    public function createFile(string $directoryPath, string $fileName, string $fileContent): string
    {
        $path = $directoryPath . '/' . $fileName;
        if (!file_put_contents($path, $fileContent)) {
            throw new Exception("Unable to create file: $path");
        }

        return $path;
    }
}