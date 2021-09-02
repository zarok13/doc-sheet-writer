<?php

namespace Zarok13\DocSheetWriter\Contracts;


interface IXLSXWriter
{
    public function create(): void;
    public function write(array $data, int $styleIndex = 0): void;
    public function complete(): void;
}
