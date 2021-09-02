<?php

namespace Zarok13\DocSheetWriter\Creator\XLSX;

use Zarok13\DocSheetWriter\Creator\XLSX\Sheets\SheetCollection;
use Zarok13\DocSheetWriter\Creator\XLSX\Traits\FileClosable;
use Zarok13\DocSheetWriter\Creator\XLSX\Traits\SheetWritable;
use Zarok13\DocSheetWriter\Creator\XLSX\Traits\SheetCompletable;
use Zarok13\DocSheetWriter\Helpers\FileActions;

class Core extends FileActions
{
    use SheetWritable;
    use FileClosable;
    use SheetCompletable;

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

    protected SheetCollection $sheetCollection;
    protected array $sheets;
    protected $stylesFileStream;
    protected $sharedStringsFile;


    public function __construct(SheetCollection $sheetCollection)
    {
        $this->sheetCollection = $sheetCollection;
        $this->sheets = $sheetCollection->getSheets();
        $this->baseDirectory = sys_get_temp_dir();
    }

    public function getWorkSheetDir()
    {
        return $this->xlWorksheetsDirectory;
    }

    public function addRootDirectory()
    {
        $this->rootDirectory = $this->createDirectory($this->baseDirectory, uniqid('xlsx', true));

        return $this;
    }

    public function addDocPropsDirectory()
    {
        $this->docPropsDirectory = $this->createDirectory($this->rootDirectory, self::DIR_DOC_PROPS);

        $this->addDocPropsFiles();

        return $this;
    }

    public function addRelsDirectory()
    {
        $this->relsDirectory = $this->createDirectory($this->rootDirectory, self::DIR_RELS);

        $this->addRelsFile();

        return $this;
    }

    public function addXlDirectory()
    {
        $this->xlDirectory = $this->createDirectory($this->rootDirectory, self::DIR_XL);

        $this->addXlSubDirectories();

        return $this;
    }

    public function addXlSubDirectories()
    {
        $this->xlRelsDirectory = $this->createDirectory($this->xlDirectory, self::DIR_RELS);
        $this->xlWorksheetsDirectory = $this->createDirectory($this->xlDirectory, self::DIR_WORKSHEETS);
    }

    public function addDocPropsFiles()
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

    public function addRelsFile()
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

    public function addContentTypeFile()
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

        foreach ($this->sheets as $sheet) {
            $content .= '<Override PartName="/xl/worksheets/' . $sheet->getName() . '.xml" ContentType="application/vnd.openxmlformats-officedocument.spreadsheetml.worksheet+xml"/>';
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

    public function addWorkbookFile()
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
        foreach ($this->sheets as $sheet) {
            $content .= '<sheet name="' . $sheet->getName() . '" sheetId="' . $sheet->getIndex() . '" state="visible" r:id="rId' . $sheet->getIndex() . '"/>';
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

    public function addStylesFile()
    {
        $content =
            '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>
            <styleSheet xmlns="http://schemas.openxmlformats.org/spreadsheetml/2006/main">';

        $stylesFile = fopen($this->xlDirectory . '/' . self::FILE_STYLES_XML, 'w+');
        fwrite($stylesFile, $content);

        $this->stylesFileStream = $stylesFile;

        return $this;
    }

    public function addSharedStringsFile()
    {
        $sharedStringsFile = fopen($this->xlDirectory . '/' . self::FILE_SHARED_STRINGS, 'w+');
        $content =
            '<?xml version="1.0" encoding="UTF-8" ?>
            <sst xmlns="http://schemas.openxmlformats.org/spreadsheetml/2006/main" count="8" uniqueCount="8">';
        fwrite($sharedStringsFile, $content);
        $this->sharedStringsFile = $sharedStringsFile;

        return $this;
    }

    public function addWorkbookRelsFile()
    {
        $content =
            '<?xml version="1.0" encoding="UTF-8"?>
            <Relationships xmlns="http://schemas.openxmlformats.org/package/2006/relationships">
            <Relationship Id="rIdStyles" Type="http://schemas.openxmlformats.org/officeDocument/2006/relationships/styles" Target="styles.xml"/>
            <Relationship Id="rIdSharedStrings" Type="http://schemas.openxmlformats.org/officeDocument/2006/relationships/sharedStrings" Target="sharedStrings.xml"/>';

        foreach ($this->sheets as $sheet) {
            $content .= '<Relationship Id="rId' . $sheet->getIndex() . '" Type="http://schemas.openxmlformats.org/officeDocument/2006/relationships/worksheet" Target="worksheets/' . $sheet->getName() . '.xml"/>';
        }

        $content .= '</Relationships>';

        $this->createFile($this->xlRelsDirectory, self::FILE_WORKBOOK_XML_RELS, $content);

        return $this;
    }

    public function addWorksheetFiles()
    {
        $content =
            '<?xml version="1.0" encoding="UTF-8"?>
            <worksheet xmlns="http://schemas.openxmlformats.org/spreadsheetml/2006/main" xmlns:r="http://schemas.openxmlformats.org/officeDocument/2006/relationships" xmlns:xdr="http://schemas.openxmlformats.org/drawingml/2006/spreadsheetDrawing" xmlns:x14="http://schemas.microsoft.com/office/spreadsheetml/2009/9/main" xmlns:mc="http://schemas.openxmlformats.org/markup-compatibility/2006">
                <sheetPr filterMode="false">
                    <pageSetUpPr fitToPage="false"/>
                </sheetPr>
                <sheetFormatPr defaultColWidth="11.53515625" defaultRowHeight="12.8" zeroHeight="false" outlineLevelRow="0" outlineLevelCol="0"/>
                <sheetData>';

        foreach ($this->sheets as $sheet) {
            $sheetStream = fopen($this->xlWorksheetsDirectory . '/' . $sheet->getName() . '.xml', 'w');
            fwrite($sheetStream, $content);
            $sheet->setSheetStream($sheetStream);
        }

        return $this;
    }
}
