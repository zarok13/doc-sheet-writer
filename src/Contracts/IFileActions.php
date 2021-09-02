<?php

namespace Zarok13\DocSheetWriter\Contracts;

interface IFileActions
{
    /**
     * creates directory with the given directory path
     *
     * @param string $directoryPath
     * @param string $directory
     * @return string
     */
    public function createDirectory(string $directoryPath, string $directory): string;

    /**
     * creates file with content in the given directory
     *
     * @param string $directoryPath
     * @param string $fileName
     * @param string $fileContent
     * @return string
     */
    public function createFile(string $directoryPath, string $fileName, string $fileContent): string;
}
