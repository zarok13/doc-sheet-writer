<?php

namespace Zarok13\SSWriter\Creator\XLSX;


class Sheet
{
    public $currentSheetIndex = 0;
    public $sheetNames = [];

    public function __construct($sheetNames)
    {
        $this->setSheetName($sheetNames);
        $this->setCurrentSheet();
    }

    public function setCurrentSheet()
    {
        $this->currentSheetIndex++;
    }

    public function getCurrentIndex()
    {
        return $this->currentSheetIndex;
    }

    public function setSheetName($name)
    {
        $this->sheetNames[] = $name;
    }

    public function getNames()
    {
        return $this->sheetNames;
    }
}
