<?php

namespace Zarok13\SSWriter\Creator\XLSX;

use Zarok13\SSWriter\Contracts\IXLSXWriter;
use Zarok13\SSWriter\Creator\XLSX\FileStructure;
use Zarok13\SSWriter\Creator\XLSX\Sheets\SheetCollection;

class XLSXWriter implements IXLSXWriter
{
    private $fileStructure;
    private $sharedStringsFile;
    public $fileName;

    public function __construct(string $fileName, SheetCollection $sheetCollection)
    {
        if (!extension_loaded('zip')) {
            throw new \Exception('zip extension not loaded.');
        }
        $this->fileName = $fileName;
        $this->fileStructure = new FileStructure($sheetCollection);
        $this->create();
    }

    /**
     * create xlsx file structure
     *
     * @return void
     */
    public function create(): void
    {
        $this->fileStructure
            ->addRootDirectory()
            ->addDocPropsDirectory()
            ->addRelsDirectory()
            ->addXlDirectory()
            ->addContentTypeFile()
            ->addWorkbookFile()
            ->addWorkbookRelsFile()
            ->addWorksheetFiles();
            // ->addStylesFile($style)

        $this->sharedStringsFile = $this->fileStructure->addSharedStringsFile();
    }

    /**
     * can be used multiple times
     *
     * @param array $data
     * @return void
     */
    public function write(array $data): void
    {
        $rows = $this->createRowFromArray($data);
        $this->fileStructure->writeData($rows);
    }

    /**
     * close files, zip data, and clear temporarly files
     *
     * @return void
     */
    public function complete(): void
    {
        $this->fileStructure
            ->closeWorksheetFiles()
            ->closeSharedStringsFile($this->sharedStringsFile)
            ->zipData($this->fileName)
            ->cleanUp();
    }

    /**
     * creates rows with cell's objects from array
     *
     * @param array $data
     * @return void
     */
    public function createRowFromArray(array $data = []): array
    {
        $rows = [];
        $cells = [];
        foreach ($data as $rowIndex => $row) {
            foreach ($row as $cell) {
                $cells[] = (new Cell())->setCell($cell);
            }
            $rows[] = (new Row())->setRows($cells, $rowIndex);
            $cells = [];
        }

        return $rows;
    }
}
