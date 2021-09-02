<?php

namespace Zarok13\DocSheetWriter\Creator\XLSX\Sheets;


class Sheet
{
    private $name;

    // non zero based
    private $index;

    private $sheetStream;

    private $lastRowIndex = -1;

    public function __construct($name, $index)
    {
        $this->name = $name;
        $this->index = $index;
    }

    public function setSheetStream($sheetStream)
    {
        $this->sheetStream = $sheetStream;
    }

    public function setLastRowIndex($lastRowIndex)
    {
        $this->lastRowIndex = $lastRowIndex;
    }

    public function incrementLastRowIndex()
    {
        return ++$this->lastRowIndex;
    }

    public function getName()
    {
        return $this->name;
    }

    public function getIndex()
    {
        return $this->index;
    }

    public function getSheetStream()
    {
        return $this->sheetStream;
    }

    public function getLastRowIndex()
    {
        return $this->lastRowIndex;
    }
}
