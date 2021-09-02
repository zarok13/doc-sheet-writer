<?php

namespace Zarok13\DocSheetWriter\Creator\XLSX\Traits;

use Zarok13\DocSheetWriter\Creator\XLSX\Styles\Styles;

trait FileClosable {
    public function closeWorksheetFiles()
    {
        $content = '</sheetData>
            <printOptions headings="false" gridLines="false" gridLinesSet="true" horizontalCentered="false" verticalCentered="false"/>
            <pageMargins left="0.7875" right="0.7875" top="1.05277777777778" bottom="1.05277777777778" header="0.7875" footer="0.7875"/>
            <pageSetup paperSize="1" scale="100" firstPageNumber="1" fitToWidth="1" fitToHeight="1" pageOrder="downThenOver" orientation="portrait" blackAndWhite="false" draft="false" cellComments="none" useFirstPageNumber="true" horizontalDpi="300" verticalDpi="300" copies="1"/>
            <headerFooter differentFirst="false" differentOddEven="false">
            <oddHeader>&amp;C&amp;&quot;Times New Roman,Regular&quot;&amp;12&amp;A</oddHeader>
            <oddFooter>&amp;C&amp;&quot;Times New Roman,Regular&quot;&amp;12Page &amp;P</oddFooter>
            </headerFooter>
        </worksheet>';

        foreach ($this->sheets as $sheet) {
            $sheetStream = $sheet->getSheetStream();
            fwrite($sheetStream, $content);
            fclose($sheetStream);
        }

        return $this;
    }

    public function closeSharedStringsFile()
    {
        fwrite($this->sharedStringsFile, '</sst>');
        fclose($this->sharedStringsFile);

        return $this;
    }

    public function closeStylesFile()
    {
        $styles = Styles::initStyle();
        fwrite($this->stylesFileStream, $styles::getStylesContent());
        fclose($this->stylesFileStream);

        return $this;
    }
}