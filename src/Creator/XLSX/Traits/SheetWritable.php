<?php

namespace Zarok13\DocSheetWriter\Creator\XLSX\Traits;

use Zarok13\DocSheetWriter\Creator\XLSX\Cell;
use Zarok13\DocSheetWriter\Creator\XLSX\Row;

trait SheetWritable
{
    private $currentStyleIndex;

    public function writeData(array $rows, int $styleIndex)
    {
        $content = '';
        $lastRow = end($rows);
        $currentSheet = $this->sheets[$this->sheetCollection->getCurrentSheet()];
        $i = 0;
        $currentRowIndex = 0;
        $this->currentStyleIndex = $styleIndex;

        while (count($rows) > $i) {
            $currentRowIndex = $currentSheet->incrementLastRowIndex();
            $this->writeRow($rows[$i], $currentRowIndex, $content);

            // remember the last row
            // if ($i == $lastRow->getRowIndex()) {
            //     // $currentSheet->setLastRowIndex($i);
            //     dump($currentSheet);
            // }

            $i++;
        }

        if (!empty($stream = $currentSheet->getSheetStream())) {
            \fwrite($stream, $content);
        }
    }

    private function writeRow(Row $row, $rowIndex, &$content)
    {
        $content .= '<row r="' . $rowIndex . '" customFormat="false" ht="12.8" hidden="false" customHeight="false" outlineLevel="0" collapsed="false">';
        foreach ($row->getRows() as $columnIndex => $cell) {
            $this->writeCell($cell, $rowIndex, $columnIndex, $content);
        }
        $content .= '</row>';
    }

    private function writeCell(Cell $cell, $rowIndex, $columnIndex, &$content)
    {
        $entry = $this->getEntry($rowIndex, $columnIndex);

        $content .= '<c r="' . $entry . '" s="' . $this->currentStyleIndex . '"';

        if ($cell->getStringType()) {
            // if (!$this->useSharedStrings) {
            $content .= ' t="inlineStr"><is><t>' . $cell->getValue() . '</t></is></c>';
            // } else {
            // $content = ' t="s"><v>' . $sharedStringId . '</v></c>';
            // }
        } elseif ($cell->getNumericType()) {
            $content .= ' t="n"><v>' . $cell->getValue() . '</v></c>';
        } elseif ($cell->getBooleanType()) {
            $content .= ' t="b"><v>' . (int) ($cell->getValue()) . '</v></c>';
        } elseif ($cell->getEmptyType()) {
            $content .= '';
        } else {
            throw new \Exception('data type unknown: ' . gettype($cell->getValue()));
        }
    }

    private function getEntry($row_number, $column_number)
    {
        $n = $column_number;
        for ($r = ""; $n >= 0; $n = intval($n / 26) - 1) {
            $r = chr($n % 26 + 0x41) . $r;
        }

        return $r . ($row_number + 1);
    }
}
