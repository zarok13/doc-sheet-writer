<?php

namespace Zarok13\SSWriter\Creator\XLSX;

use RecursiveDirectoryIterator;
use RecursiveIteratorIterator;
use Zarok13\SSWriter\Helpers\FileActions;
use ZipArchive;

class FileStructure extends FileActions
{
    const APP_NAME = 'SSWriter';

    const DIR_RELS = '_rels';
    const DIR_DOC_PROPS = 'docProps';
    const DIR_XL = 'xl';
    const DIR_WORKSHEETS = 'worksheets';

    const FILE_RELS = '.rels';
    const FILE_APP_XML = 'app.xml';
    const FILE_CORE_XML = 'core.xml';
    const FILE_CONTENT_TYPES_XML = '[Content_Types].xml';
    const FILE_WORKBOOK_XML = 'workbook.xml';
    const FILE_WORKBOOK_XML_RELS = 'workbook.xml.rels';
    const FILE_STYLES_XML = 'styles.xml';
    const FILE_SHARED_STRINGS = 'sharedStrings.xml';

    private $baseDirectory;
    private $rootDirectory;
    private $relsDirectory;
    private $docPropsDirectory;
    private $xlDirectory;
    private $xlRelsDirectory;
    private $xlWorksheetsDirectory;


    public function __construct()
    {
        $this->baseDirectory = sys_get_temp_dir();
        // $this->baseDirectory = 'public/tmp';
    }

    public function getWorkSheetDir()
    {
        return $this->xlWorksheetsDirectory;
    }

    protected function addRootDirectory()
    {
        $this->rootDirectory = $this->createDirectory($this->baseDirectory, uniqid('xlsx', true));

        return $this;
    }

    protected function addDocPropsDirectory()
    {
        $this->docPropsDirectory = $this->createDirectory($this->rootDirectory, self::DIR_DOC_PROPS);

        $this->addDocPropsFiles();

        return $this;
    }

    protected function addRelsDirectory()
    {
        $this->relsDirectory = $this->createDirectory($this->rootDirectory, self::DIR_RELS);

        $this->addRelsFile();

        return $this;
    }

    protected function addXlDirectory()
    {
        $this->xlDirectory = $this->createDirectory($this->rootDirectory, self::DIR_XL);

        $this->addXlSubDirectories();

        return $this;
    }

    protected function addXlSubDirectories()
    {
        $this->xlRelsDirectory = $this->createDirectory($this->xlDirectory, self::DIR_RELS);
        $this->xlWorksheetsDirectory = $this->createDirectory($this->xlDirectory, self::DIR_WORKSHEETS);
    }

    protected function addDocPropsFiles()
    {
        $application = self::APP_NAME;
        $createdDate = now()->format(\DateTime::W3C);
        $appXmlContent =
            '<?xml version="1.0" encoding="UTF-8" ?>
            <Properties xmlns="http://schemas.openxmlformats.org/officeDocument/2006/extended-properties" xmlns:vt="http://schemas.openxmlformats.org/officeDocument/2006/docPropsVTypes">
                <Template/>
                <TotalTime>0</TotalTime>
                <Application>' . $application . '</Application>
            </Properties>';
        $coreXmlContent =
            '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>
            <cp:coreProperties xmlns:cp="http://schemas.openxmlformats.org/package/2006/metadata/core-properties" xmlns:dc="http://purl.org/dc/elements/1.1/" xmlns:dcterms="http://purl.org/dc/terms/" xmlns:dcmitype="http://purl.org/dc/dcmitype/" xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance">
                <dcterms:created xsi:type="dcterms:W3CDTF">' . $createdDate . '</dcterms:created>
                <dc:creator/>
                <dc:description/>
                <dc:language>en-US</dc:language>
                <cp:lastModifiedBy/>
                <dcterms:modified xsi:type="dcterms:W3CDTF">' . $createdDate . '</dcterms:modified>
                <cp:revision>1</cp:revision>
                <dc:subject/>
                <dc:title/>
            </cp:coreProperties>';

        $this->createFile($this->docPropsDirectory, self::FILE_APP_XML, $appXmlContent);
        $this->createFile($this->docPropsDirectory, self::FILE_CORE_XML, $coreXmlContent);
    }

    protected function addRelsFile()
    {
        $content =
            '<?xml version="1.0" encoding="UTF-8"?>
            <Relationships xmlns="http://schemas.openxmlformats.org/package/2006/relationships">
                <Relationship Id="rId1" Type="http://schemas.openxmlformats.org/officeDocument/2006/relationships/officeDocument" Target="xl/workbook.xml"/>
                <Relationship Id="rId2" Type="http://schemas.openxmlformats.org/package/2006/relationships/metadata/core-properties" Target="docProps/core.xml"/>
                <Relationship Id="rId3" Type="http://schemas.openxmlformats.org/officeDocument/2006/relationships/extended-properties" Target="docProps/app.xml"/>
            </Relationships>';

        $this->createFile($this->relsDirectory, self::FILE_RELS, $content);
    }

    protected function addContentTypeFile(Sheet $sheet)
    {
        $content =
            '<?xml version="1.0" encoding="UTF-8"?>
            <Types xmlns="http://schemas.openxmlformats.org/package/2006/content-types">
                <Default Extension="xml" ContentType="application/xml"/>
                <Default Extension="rels" ContentType="application/vnd.openxmlformats-package.relationships+xml"/>
                <Default Extension="png" ContentType="image/png"/>
                <Default Extension="jpeg" ContentType="image/jpeg"/>
                <Override PartName="/_rels/.rels" ContentType="application/vnd.openxmlformats-package.relationships+xml"/>
                <Override PartName="/xl/workbook.xml" ContentType="application/vnd.openxmlformats-officedocument.spreadsheetml.sheet.main+xml"/>
                <Override PartName="/xl/styles.xml" ContentType="application/vnd.openxmlformats-officedocument.spreadsheetml.styles+xml"/>';

        foreach ($sheet->getNames() as $name) {
            $content .= '<Override PartName="/xl/worksheets/' . $name . '.xml" ContentType="application/vnd.openxmlformats-officedocument.spreadsheetml.worksheet+xml"/>';
        }

        $content .=
            '<Override PartName="/xl/_rels/workbook.xml.rels" ContentType="application/vnd.openxmlformats-package.relationships+xml"/>
            <Override PartName="/xl/sharedStrings.xml" ContentType="application/vnd.openxmlformats-officedocument.spreadsheetml.sharedStrings+xml"/>
            <Override PartName="/docProps/core.xml" ContentType="application/vnd.openxmlformats-package.core-properties+xml"/>
            <Override PartName="/docProps/app.xml" ContentType="application/vnd.openxmlformats-officedocument.extended-properties+xml"/>
        </Types>';

        $this->createFile($this->rootDirectory, self::FILE_CONTENT_TYPES_XML, $content);

        return $this;
    }

    protected function addWorkbookFile(Sheet $sheet)
    {
        $content =
            '<?xml version="1.0" encoding="UTF-8"?>
            <workbook xmlns="http://schemas.openxmlformats.org/spreadsheetml/2006/main" xmlns:r="http://schemas.openxmlformats.org/officeDocument/2006/relationships">
                <fileVersion appName="Calc"/>
                <workbookPr backupFile="false" showObjects="all" date1904="false"/>
                <workbookProtection/>
                <bookViews>
            <workbookView showHorizontalScroll="true" showVerticalScroll="true" showSheetTabs="true" xWindow="0" yWindow="0" windowWidth="16384" windowHeight="8192" tabRatio="500" firstSheet="0" activeTab="0"/>
                </bookViews>
                <sheets>';
        foreach ($sheet->getNames() as $index => $name) {
            $content .= '<sheet name="' . $name . '" sheetId="' . ($index + 1) . '" state="visible" r:id="rId' . ($index + 1) . '"/>';
        }


        $content .=
            '</sheets>
                <calcPr iterateCount="100" refMode="A1" iterate="false" iterateDelta="0.001"/>
                <extLst>
                    <ext xmlns:loext="http://schemas.libreoffice.org/" uri="{7626C862-2A13-11E5-B345-FEFF819CDC9F}">
                        <loext:extCalcPr stringRefSyntax="CalcA1"/>
                    </ext>
                </extLst>
            </workbook>';

        $this->createFile($this->xlDirectory, self::FILE_WORKBOOK_XML, $content);

        return $this;
    }

    protected function addSharedStringsFile()
    {
        $sharedStringsFile = fopen($this->xlDirectory . '/' . self::FILE_SHARED_STRINGS, 'w+');
        $content =
            '<?xml version="1.0" encoding="UTF-8" ?>
            <sst xmlns="http://schemas.openxmlformats.org/spreadsheetml/2006/main" count="8" uniqueCount="8">';
        fwrite($sharedStringsFile, $content);

        return $sharedStringsFile;
    }

    protected function closeSharedStringsFile($sharedStringsFile)
    {
        fwrite($sharedStringsFile, '</sst>');
        fclose($sharedStringsFile);

        return $this;
    }

    protected function addWorkbookRelsFile(Sheet $sheet)
    {
        $content =
            '<?xml version="1.0" encoding="UTF-8"?>
            <Relationships xmlns="http://schemas.openxmlformats.org/package/2006/relationships">
            <Relationship Id="rIdStyles" Type="http://schemas.openxmlformats.org/officeDocument/2006/relationships/styles" Target="styles.xml"/>
            <Relationship Id="rIdSharedStrings" Type="http://schemas.openxmlformats.org/officeDocument/2006/relationships/sharedStrings" Target="sharedStrings.xml"/>';

        foreach ($sheet->getNames() as $index => $name) {
            $content .= '<Relationship Id="rId' . ($index + 1) . '" Type="http://schemas.openxmlformats.org/officeDocument/2006/relationships/worksheet" Target="worksheets/' . $name . '.xml"/>';
        }

        $content .= '</Relationships>';

        $this->createFile($this->xlRelsDirectory, self::FILE_WORKBOOK_XML_RELS, $content);

        return $this;
    }

    public function addWorksheetFiles(array $data)
    {

        $content =
            '<?xml version="1.0" encoding="UTF-8"?>
            <worksheet xmlns="http://schemas.openxmlformats.org/spreadsheetml/2006/main" xmlns:r="http://schemas.openxmlformats.org/officeDocument/2006/relationships" xmlns:xdr="http://schemas.openxmlformats.org/drawingml/2006/spreadsheetDrawing" xmlns:x14="http://schemas.microsoft.com/office/spreadsheetml/2009/9/main" xmlns:mc="http://schemas.openxmlformats.org/markup-compatibility/2006">
                <sheetPr filterMode="false">
                    <pageSetUpPr fitToPage="false"/>
                </sheetPr>
                <sheetFormatPr defaultColWidth="11.53515625" defaultRowHeight="12.8" zeroHeight="false" outlineLevelRow="0" outlineLevelCol="0"/>
                <sheetData>';

        $this->addData($data, $content);

        $content .= '</sheetData>
                <printOptions headings="false" gridLines="false" gridLinesSet="true" horizontalCentered="false" verticalCentered="false"/>
                <pageMargins left="0.7875" right="0.7875" top="1.05277777777778" bottom="1.05277777777778" header="0.7875" footer="0.7875"/>
                <pageSetup paperSize="1" scale="100" firstPageNumber="1" fitToWidth="1" fitToHeight="1" pageOrder="downThenOver" orientation="portrait" blackAndWhite="false" draft="false" cellComments="none" useFirstPageNumber="true" horizontalDpi="300" verticalDpi="300" copies="1"/>
                <headerFooter differentFirst="false" differentOddEven="false">
                <oddHeader>&amp;C&amp;&quot;Times New Roman,Regular&quot;&amp;12&amp;A</oddHeader>
                <oddFooter>&amp;C&amp;&quot;Times New Roman,Regular&quot;&amp;12Page &amp;P</oddFooter>
                </headerFooter>
            </worksheet>';

        $this->createFile($this->xlWorksheetsDirectory, 'sheet1.xml', $content);

        return $this;
    }

    public function zipData($fileName)
    {
        $rootPath = realpath($this->rootDirectory);
        $zip = new ZipArchive();
        // dd(base_path('/'));
        $zip->open($fileName . '.zip', ZipArchive::CREATE | ZipArchive::OVERWRITE);

        $files = new RecursiveIteratorIterator(
            new RecursiveDirectoryIterator($rootPath),
            RecursiveIteratorIterator::LEAVES_ONLY
        );

        foreach ($files as $name => $file) {
            if (!$file->isDir()) {
                // Get real and relative path for current file
                $filePath = $file->getRealPath();
                $relativePath = substr($filePath, strlen($rootPath) + 1);

                // Add current file to archive
                $zip->addFile($filePath, $relativePath);
            }
        }

        $zip->close();

        rename($fileName . '.zip', $fileName . '.xlsx');

        return $this;
    }

    public function cleanUp()
    {
        $rootPath = realpath($this->rootDirectory);
        $it = new RecursiveDirectoryIterator($rootPath, RecursiveDirectoryIterator::SKIP_DOTS);
        $files = new RecursiveIteratorIterator(
            $it,
            RecursiveIteratorIterator::CHILD_FIRST
        );
        foreach ($files as $file) {
            if ($file->isDir()) {
                rmdir($file->getRealPath());
            } else {
                unlink($file->getRealPath());
            }
        }
        rmdir($rootPath);
    }

    private function addData(array $rows, &$content)
    {
        foreach ($rows as $rowIndex => $row) {
            $this->writeRow($row, $rowIndex, $content);
        }
    }

    private function writeRow(Row $row, $rowIndex, &$content)
    {
        $content .= '<row r="' . ($rowIndex + 1) . '" customFormat="false" ht="12.8" hidden="false" customHeight="false" outlineLevel="0" collapsed="false">';
        foreach ($row->getRows() as $columnIndex => $cell) {
            $this->writeCell($cell, $rowIndex, $columnIndex, $content);
        }
        $content .= '</row>';
    }

    private function writeCell(Cell $cell, $rowIndex, $columnIndex, &$content)
    {
        $entry = $this->getEntry($rowIndex, $columnIndex);

        $content .= '<c r="' . $entry . '" s="0"';

        if ($cell->getStringType()) {
            // if (!$this->useSharedStrings) {
            $content .= ' t="inlineStr"><is><t>' . $cell->getValue() . '</t></is></c>';
            // } else {
            // $content = ' t="s"><v>' . $sharedStringId . '</v></c>';
            // }
        } elseif ($cell->getNumericType()) {
            $content .= ' t="n"><v>' . $cell->getValue() . '</v></c>';
        } elseif ($cell->getBooleanType()) {
            $content .= ' t="b"><v>' . (int) ($cell->getValue()) . '</v></c>';
        } elseif ($cell->getEmptyType()) {
            $content .= '';
        } else {
            throw new \Exception('data type unknown: ' . gettype($cell->getValue()));
        }
    }

    private function getEntry($row_number, $column_number)
    {
        $n = $column_number;
        for ($r = ""; $n >= 0; $n = intval($n / 26) - 1) {
            $r = chr($n % 26 + 0x41) . $r;
        }

        return $r . ($row_number + 1);
    }
}
