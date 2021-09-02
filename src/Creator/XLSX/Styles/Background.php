<?php

namespace Zarok13\DocSheetWriter\Creator\XLSX\Styles;

class Background
{
    private static $backgroundInstances = [];
    private static int $backgroundInstancesCount = 2;

    public int $index;

    /**
     * Background Properties
     *
     * @var string
     */
    private string $patternType = 'solid';
    public string $color = 'ffffff';

    public function __construct()
    {
        self::$backgroundInstances[] = $this;
        $this->index = self::$backgroundInstancesCount++;
    }


    public static function getBackgrounds()
    {
        return self::$backgroundInstances;
    }

    public static function getBackgroundsCount()
    {
        return self::$backgroundInstancesCount;
    }

    /**
     * set font color
     *
     * @param string $color
     * @return void
     */
    public function setColor(string $color)
    {
        $this->color = '<patternFill patternType="' . $this->patternType . '">
                <fgColor rgb="' . $color . '" />
            </patternFill>';
        return $this;
    }
}
