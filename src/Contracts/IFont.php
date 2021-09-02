<?php

namespace Zarok13\DocSheetWriter\Contracts;

interface IFont {
    public function setSize(int $size);
    public function setColor(string $color);
    public function setName(string $name);
    public function setBold();
    public function setItalic();
    public function setUnderline();
}