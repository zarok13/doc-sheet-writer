<?php

namespace Zarok13\DocSheetWriter\Creator\XLSX\Styles;

class Borders
{
    private static $bordersInstances = [];
    private static int $bordersInstancesCount = 1;

    public int $index;

    /**
     * Borders Properties
     *
     * @param integer $size
     * @return void
     */
    const TYPE_THIN = 'thin';
    const TYPE_DASHED = 'dashed';
    const TYPE_HAIR = 'hair';
    const TYPE_DASH_DOT_DOT = 'dashDotDot';
    const TYPE_MEDIUM_DASHED = 'mediumDashed';
    const TYPE_DOTTED = 'dotted';
    const TYPE_DOUBLE = 'double';
    const TYPE_MEDIUM = 'medium';

    public string $left;
    public string $right;
    public string $top;
    public string $bottom;
    private string $color = '000000';

    public function __construct() {
        self::$bordersInstances[] = $this;
        $this->index = self::$bordersInstancesCount++;
    }

    public static function getBorders()
    {
        return self::$bordersInstances;
    }

    public static function getBordersCount()
    {
        return self::$bordersInstancesCount;
    }

    public function setLeftBorder(string $type)
    {
        $this->left = '<left style="'.$type.'">
                <color rgb="FF' . $this->color . '"/>
            </left>';
    }

    public function setRightBorder(string $type)
    {
        $this->right =  '<right style="'.$type.'">
                <color rgb="FF' . $this->color . '"/>
            </right>';
    }

    public function setTopBorder(string $type)
    {
        $this->top = '<top style="'.$type.'">
                <color rgb="FF' . $this->color . '"/>
            </top>';
    }

    public function setBottomBorder(string $type)
    {
        $this->bottom = '<bottom style="'.$type.'">
                <color rgb="FF' . $this->color . '"/>
            </bottom>';
    }

    public function setColor(string $color)
    {
        $this->color = $color;
    }
}
