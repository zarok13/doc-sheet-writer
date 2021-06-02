<?php

namespace Zarok13\SSWriter\Creator\XLSX;

use Zarok13\SSWriter\Creator\XLSX\FileStructure;

class XLSXWriter extends FileStructure
{
    private $fileStructure;

    private $sharedStringsFile;

    public $fileName;

    public function __construct($fileName)
    {
        if (!extension_loaded('zip')) {
            throw new \Exception('zip extension not loaded.');
        }
        $this->fileName = $fileName;
        $this->fileStructure = new FileStructure();
        $this->create();
    }

    public function create()
    {
        $this->fileStructure
            ->addRootDirectory()
            ->addDocPropsDirectory()
            ->addRelsDirectory()
            ->addXlDirectory();

        $this->sharedStringsFile = $this->fileStructure->addSharedStringsFile();
    }

    public function write(array $data, string $sheet = 'sheet1')
    {
        $sheet = new Sheet($sheet);
        $rows = $this->createRowFromArray($data);

        $this->fileStructure
            ->addContentTypeFile($sheet)
            ->addWorkbookFile($sheet)
            ->addWorkbookRelsFile($sheet)
            ->addWorksheetFiles($rows);

        $this->finalize();
    }

    public function finalize()
    {
        $this->fileStructure
            ->closeSharedStringsFile($this->sharedStringsFile)
            ->zipData($this->fileName)
            ->cleanUp();
    }

    /**
     * creates rows with own cells from array
     *
     * @param array $data
     * @return void
     */
    public function createRowFromArray(array $data = []): array
    {
        $rows = [];
        $cells = [];
        foreach ($data as $row) {
            foreach ($row as $cell) {
                $cells[] = (new Cell())->setCell($cell);
            }
            $rows[] = (new Row())->setRows($cells);
            $cells = [];
        }

        return $rows;
    }
}
