<?php

namespace Zarok13\SSWriter\Creator\XLSX\Sheets;


class Sheet
{
    public $name;

    // non zero based
    public $index;

    public function __construct($name, $index)
    {
        $this->name = $name;
        $this->index = $index;
    }

    public function getName()
    {
        return $this->name;
    }

    public function getIndex()
    {
        return $this->index;
    }
}
