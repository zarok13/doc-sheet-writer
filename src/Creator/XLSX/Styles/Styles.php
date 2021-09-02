<?php

namespace Zarok13\DocSheetWriter\Creator\XLSX\Styles;

class Styles
{
    private static $instance;
    private static string $generatedStyles;
    private static array $styleConbinations = [[0, 0, 0]];

    /**
     * Forground styles
     */
    const FONT_SIZE = 11;
    const FONT_COLOR = '000000';
    const FONT_NAME = 'Calibri';

    // Colors
    // const BLACK = '000000';
    // const WHITE = 'FFFFFF';
    // const RED = 'FF0000';
    // const DARK_RED = 'C00000';
    // const ORANGE = 'FFC000';
    // const YELLOW = 'FFFF00';
    // const LIGHT_GREEN = '92D040';
    // const GREEN = '00B050';
    // const LIGHT_BLUE = '00B0E0';
    // const BLUE = '0070C0';
    // const DARK_BLUE = '002060';
    // const PURPLE = '7030A0';

    private function __construct()
    {
        self::$generatedStyles = $this->create();
    }

    public static function initStyle()
    {
        if (self::$instance == null) {
            self::$instance = new Styles();
        }
        return self::$instance;
    }

    public static function getStylesContent()
    {
        return self::$generatedStyles;
    }

    public static function generateStyle(Font $font = null, Background $background = null, Borders $borders = null)
    {

        if (empty($font) && empty($background) && empty($borders)) {
            throw new \Exception('At least one of three components must be specified to generate style.');
        }

        $combination = [$font->index ?? 0, $background->index ?? 0, $borders->index ?? 0];

        if (in_array($combination, self::$styleConbinations)) {
            throw new \Exception('This style already exists.');
        }

        self::$styleConbinations[] = $combination;

        return count(self::$styleConbinations) - 1;
    }

    /**
     * create font partials
     *
     * @return void
     */
    private function create()
    {
        $content = '<numFmts count="0"></numFmts>';

        $fontCount = Font::getFontsCount();
        $backgroundCount = Background::getBackgroundsCount();
        $bordersCount = Borders::getBordersCount();

        $this->setFonts(Font::getFonts(), $content, $fontCount);
        $this->setBackground(Background::getBackgrounds(), $content, $backgroundCount);
        $this->setBorders(Borders::getBorders(), $content, $bordersCount);

        $content .= '<cellStyleXfs count="1">
                <xf borderId="0" fillId="0" fontId="0" numFmtId="0"/>
            </cellStyleXfs>';

        $this->setCellXfs($content);

        $content .= '<cellStyles count="1">
                <cellStyle builtinId="0" name="Normal" xfId="0"/>
            </cellStyles>';

        $content .= '</styleSheet>';

        return $content;
    }

    public function setFonts(array $fonts, string &$content, int $count)
    {
        $fontContent = '<fonts count="' . $count . '">
            <font>
                <sz val="' . self::FONT_SIZE . '" />
                <color rgb="FF' . self::FONT_COLOR . '" />
                <name val="' . self::FONT_NAME . '"/>
            </font>';
        foreach ($fonts as $font) {
            $fontContent .= '<font>';
            foreach (get_object_vars($font) as $index => $property) {
                if ($index == 'index') {
                    continue;
                }
                $fontContent .= $property;
            }
            $fontContent .= '</font>';
        }
        $content .= $fontContent .= '</fonts>';
    }

    public function setBackground(array $backgrounds, &$content, $count)
    {
        $backgroundContent = '<fills count="' . $count . '">
            <fill>
                <patternFill patternType="none"/>
            </fill>
            <fill>
                <patternFill patternType="gray125"/>
            </fill>';
        foreach ($backgrounds as  $background) {
            $backgroundContent .= '<fill>';

            foreach (get_object_vars($background) as $index => $property) {
                if ($index == 'index') {
                    continue;
                }
                $backgroundContent .= $property;
            }
            $backgroundContent .= '</fill>';
        }

        $content .= $backgroundContent .= '</fills>';
    }

    public function setBorders(array $borders, &$content, $count)
    {
        $bordersContent = '<borders count="' . $count . '">
            <border>
                <left style="none"/>
                <right style="none"/>
                <top style="none"/>
                <bottom style="none"/>
            </border>';

        foreach ($borders as $border) {
            $bordersContent .= '<border>';
            foreach (get_object_vars($border) as  $index => $property) {
                if ($index == 'index') {
                    continue;
                }
                $bordersContent .= $property;
            }
            $bordersContent .= '</border>';
        }

        $content .= $bordersContent .= '</borders>';
    }

    public function setCellXfs(string &$content)
    {

        $cellXfsContent = '<cellXfs count="' . count(self::$styleConbinations) . '">
        <xf numFmtId="0" fontId="0" fillId="0" borderId="0" xfId="0" applyFont="1" applyBorder="0"/>';
        foreach (self::$styleConbinations as $index => $combination) {
            if ($index == 0) {
                continue;
            }
            $cellXfsContent .= '<xf numFmtId="0" fontId="' . $combination[0] . '" fillId="' . $combination[1] . '" borderId="' . $combination[2] . '" xfId="0" applyFont="1" applyBorder="1" applyAlignment="1">
                    <alignment horizontal="left" wrapText="0"/>
                </xf>';
        }

        $content .= $cellXfsContent .= '</cellXfs>';
    }
}
