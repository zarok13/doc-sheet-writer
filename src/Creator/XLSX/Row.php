<?php

namespace Zarok13\SSWriter\Creator\XLSX;

class Row
{
    protected $rows;
    protected $currentRowIndex = 0;

    public function setRows($cells)
    {
        $this->rows = $cells;
        $this->changeRowIndex();

        return $this;
    }

    public function changeRowIndex()
    {
        $this->currentRowIndex++;
    }

    public function getRows()
    {
        return $this->rows;
    }
}