<?php

namespace Zarok13\SSWriter\Creator\XLSX;

class Styles
{
    /**
     * Dafault style
     */
    const DEFAULT_FONT_SIZE = 11;
    // const DEFAULT_FONT_COLOR = Color::BLACK;
    const DEFAULT_FONT_NAME = 'Arial';
   
    public function build()
    {
        $content =
            '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>
            <styleSheet xmlns="http://schemas.openxmlformats.org/spreadsheetml/2006/main">';
        $content .= '</styleSheet>';

        return $content;
    }
}
