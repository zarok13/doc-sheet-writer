<?php

namespace Zarok13\SSWriter\Contracts;

use Zarok13\SSWriter\Creator\XLSX\Sheets\SheetCollection;

interface ISheetCollection
{
    public static function initSheets(array $sheetNames): SheetCollection;
    public function createSheets(array $sheetNames): void;
    public function getSheets(): array;
    public function getCurrentSheet(): int;
    public function setCurrentSheet(string $sheetName): void;
    public function nextSheet(): void;
    public function previousSheet(): void;
}
