<?php

namespace Zarok13\SSWriter;

use Zarok13\SSWriter\Contracts\ISSWriter;
use Zarok13\SSWriter\Creator\CSVWriter;
use Zarok13\SSWriter\Creator\ODFWriter;
use Zarok13\SSWriter\Creator\XLSX\XLSXWriter;

class SSWriter implements ISSWriter
{
    const TYPE_XLSX = 'xlsx';
    const TYPE_CSV = 'csv';
    const TYPE_ODF = 'odf';

    public $fileName;

    public function __construct($fileName = 'default') {
        $this->fileName = $fileName;
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
                return new XLSXWriter($this->fileName);
            case $writerType == self::TYPE_CSV:
                return new CSVWriter();
            case $writerType == self::TYPE_ODF:
                return new ODFWriter();
            default:
                throw new \Exception('Unknonw type: '. $writerType);
        }
    }
}