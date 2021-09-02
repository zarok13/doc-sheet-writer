<?php

namespace Zarok13\DocSheetWriter\Creator\XLSX;

use Zarok13\DocSheetWriter\Contracts\IXLSXWriter;
use Zarok13\DocSheetWriter\Creator\XLSX\Core;
use Zarok13\DocSheetWriter\Creator\XLSX\Sheets\SheetCollection;

class XLSXWriter implements IXLSXWriter
{
    private $core;
    public $fileName;

    public function __construct(string $fileName, SheetCollection $sheetCollection)
    {
        if (!extension_loaded('zip')) {
            throw new \Exception('zip extension not loaded.');
        }
        $this->fileName = $fileName;
        $this->core = new Core($sheetCollection);
        $this->create();
    }

    /**
     * create xlsx file structure
     *
     * @return void
     */
    public function create(): void
    {
        $this->core
            ->addRootDirectory()
            ->addDocPropsDirectory()
            ->addRelsDirectory()
            ->addXlDirectory()
            ->addContentTypeFile()
            ->addStylesFile()
            ->addWorkbookFile()
            ->addWorkbookRelsFile()
            ->addSharedStringsFile()
            ->addWorksheetFiles();
    }

    /**
     * close files, zip data, and clear temporarly files
     *
     * @return void
     */
    public function complete(): void
    {
        $this->core
            ->closeStylesFile()
            ->closeSharedStringsFile()
            ->closeWorksheetFiles()
            ->zipData($this->fileName)
            ->cleanUp();
    }

    /**
     * can be used multiple times
     *
     * @param array $data
     * @return void
     */
    public function write(array $data, int $styleIndex = 0): void
    {
        $rows = $this->createRowFromArray($data);
        $this->core->writeData($rows, $styleIndex);
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
