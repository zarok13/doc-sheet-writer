<?php

namespace Zarok13\SSWriter\Creator\XLSX\Sheets;

use Zarok13\SSWriter\Contracts\ISheetCollection;

class SheetCollection implements ISheetCollection
{
    // singleton instance 
    private static $instance = null;

    public $sheets = [];
    protected $currentSheet = 0;

    /**
     * prevented direct creation of objects from the class
     */
    private function __construct(array $sheetNames)
    {
        $this->createSheets($sheetNames);
    }

    /**
     * class should have only and only one instance of ofject
     *
     * @param array $sheetNames
     * @return void
     */
    public static function initSheets(array $sheetNames): SheetCollection
    {
        if (self::$instance == null) {
            self::$instance = new SheetCollection($sheetNames);
        }
        return self::$instance;
    }

    /**
     * create sheet collection with sheet objects
     *
     * @param array $sheetNames
     * @return void
     */
    public function createSheets(array $sheetNames): void
    {
        foreach ($sheetNames as $index => $name) {
            $this->sheets[] = new Sheet($name, $index + 1);
        }
    }

    /**
     * get sheet collections
     *
     * @return void
     */
    public function getSheets(): array
    {
        return $this->sheets;
    }

    public function getCurrentSheet(): int
    {
        return $this->currentSheet;
    }

    /**
     * manually select an active sheet
     *
     * @param string $sheetName
     * @return void
     */
    public function setCurrentSheet(string $sheetName): void
    {
        foreach ($this->sheets as $index => $sheet) {
            if ($sheet->getName() == $sheetName) {
                $this->currentSheet = $index;
            }
        }
    }

    /**
     * set next sheet as active sheet
     *
     * @return void
     */
    public function nextSheet(): void
    {
        if (count($this->sheets) > $this->currentSheet) {
            $this->currentSheet++;
        }
    }

    /**
     * set previous sheet as active sheet
     *
     * @return void
     */
    public function previousSheet(): void
    {
        if ($this->currentSheet > 0) {
            $this->currentSheet--;
        }
    }
}
