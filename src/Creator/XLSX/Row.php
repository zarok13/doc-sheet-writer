<?php

namespace Zarok13\SSWriter\Creator\XLSX;

class Row
{
    protected $rows;
    protected $rowIndex;

    public function setRows($cells, int $rowIndex)
    {
        $this->rows = $cells;
        $this->rowIndex = $rowIndex;

        return $this;
    }

    public function getRows()
    {
        return $this->rows;
    }

    public function getRowIndex()
    {
        return $this->rowIndex;
    }
}