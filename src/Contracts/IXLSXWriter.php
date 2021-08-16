<?php

namespace Zarok13\SSWriter\Contracts;

interface IXLSXWriter
{
    public function create(): void;
    public function write(array $data): void;
    public function complete(): void;
}
