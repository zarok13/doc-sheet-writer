<?php

namespace Zarok13\DocSheetWriter\Creator\XLSX\Styles;

use Zarok13\DocSheetWriter\Contracts\IFont;

class Font implements IFont
{
    private static $fontInstances = [];
    private static int $fontInstancesCount = 1;

    public int $index;

    /**
     * Fonts Properties
     *
     * @var integer
     */
    public string $size;
    public string $color;
    public string $name;
    public string $bold;
    public string $italic;
    public string $underline;

    public function __construct() {
        self::$fontInstances[] = $this;
        $this->index = self::$fontInstancesCount++;
    }

    public static function getFonts()
    {
        return self::$fontInstances;
    }

    public static function getFontsCount()
    {
        return self::$fontInstancesCount;
    }

    /**
     * set font size
     *
     * @param integer $size
     * @return void
     */
    public function setSize(int $size)
    {
        $this->size = '<sz val="' . $size . '" />';
        return $this;
    }

    /**
     * set font color
     *
     * @param string $color
     * @return void
     */
    public function setColor(string $color)
    {
        $this->color = '<color rgb="FF' . $color . '" />';
        return $this;
    }

    /**
     * set font family name
     *
     * @param string $name
     * @return void
     */
    public function setName(string $name)
    {
        $this->name = '<name val="' . $name . '"/>';
        return $this;
    }

    /**
     * enable bold font
     *
     * @return void
     */
    public function setBold()
    {
        $this->bold = '<b/>';
        return $this;
    }

    /**
     * enable italic font
     *
     * @return void
     */
    public function setItalic()
    {
        $this->italic = '<i/>';
        return $this;
    }

    /**
     * enable underline font
     *
     * @return void
     */
    public function setUnderline()
    {
        $this->underline = '<u/>';
        return $this;
    }
}
