<?php

namespace Zarok13\DocSheetWriter;

use Zarok13\DocSheetWriter\Contracts\IDSW;
use Zarok13\DocSheetWriter\Creator\CSV\CSVWriter;
use Zarok13\DocSheetWriter\Creator\ODF\ODFWriter;
use Zarok13\DocSheetWriter\Creator\XLSX\Sheets\SheetCollection;
use Zarok13\DocSheetWriter\Creator\XLSX\XLSXWriter;

class DSW implements IDSW
{
    const TYPE_XLSX = 'xlsx';
    const TYPE_CSV = 'csv';
    const TYPE_ODF = 'odf';

    public $fileName;
    protected $sheetCollection;

    public function __construct(string $fileName = 'default', SheetCollection $sheetCollection = null) {
        $this->fileName = $fileName;
        $this->sheetCollection = $sheetCollection ?? SheetCollection::initSheets(['sheet1']);;
    }

    /**
     * init xlsx writer type
     *
     * @return void
     */
    public function initWriterXLSX(): object
    {
        return $this->initWriter(self::TYPE_XLSX);
    }

    /**
     * init csv writer type
     *
     * @return void
     */
    public function initWriterCSV()
    {
        return $this->initWriter(self::TYPE_CSV);
    }

    /**
     * init odf writer type
     *
     * @return void
     */
    public function initWriterODF()
    {
        return $this->initWriter(self::TYPE_ODF);
    }

    /**
     * init writer type manually
     *
     * @param string $writerType
     * @return void
     */
    public function initWriter(string $writerType): object
    {
        switch($writerType) {
            case $writerType == self::TYPE_XLSX:
                return new XLSXWriter($this->fileName, $this->sheetCollection);
            case $writerType == self::TYPE_CSV:
                return new CSVWriter();
            case $writerType == self::TYPE_ODF:
                return new ODFWriter();
            default:
                throw new \Exception('Unknonw type: '. $writerType);
        }
    }
}