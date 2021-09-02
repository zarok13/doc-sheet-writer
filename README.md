## DocSheetWriter
DocSheetWriter is the lightweight php library for writing spreadsheets.

## How to use

### Installation

```
composer require zarok13/sswriter
```
### Basic Usage

```
$data = [
    ['name2', 'name2', 'name2'],
    ['data2', 'data2']
];


$writerXLSX = (new DSW('out'))->initWriterXLSX();
$writerXLSX->write($data);
$writerXLSX->complete();
```
### Styling
For styling could be used at least one of the three components Font, Background, Borders.

```
$data = [
    ['name2', 'name2', 'name2'],
    ['data2', 'data2']
];

$font1 = new Font();
$font1->setColor('ff0000');

$style1 = Styles::generateStyle($font1);

$writerXLSX = (new DSW('out'))->initWriterXLSX();
$writerXLSX->write($data, $style1);
$writerXLSX->complete();
```
### Advanced Usage
```
$data = [
    ['name1','name1'],
    ['data1', 'data1', 'data1'],
    ['data1', 'data1']
];
$data2 = [
    ['name2', 'name2', 'name2'],
    ['data2', 'data2']
];

$dataExtra = [
    ['extra']
];

$font1 = new Font();
$font1->setColor('ff0000');

$border1 = new Borders();
$border1->setColor('ff0000');
$border1->setLeftBorder(Borders::TYPE_THIN);
$border1->setRightBorder(Borders::TYPE_THIN);
$border1->setColor('0000ff');
$border1->setTopBorder(Borders::TYPE_THIN);
$border1->setBottomBorder(Borders::TYPE_THIN);

$background1 = new Background();
$background1->setColor('ff0000');

$style1 = Styles::generateStyle($font1);
$style2 = Styles::generateStyle($font1, null, $border1);
$style3 = Styles::generateStyle(null, $background1, $border1);

$sheets = SheetCollection::initSheets(['sh1', 'sh2', 'sh3']);
$writerXLSX = (new DSW('out', $sheets))->initWriterXLSX();

$writerXLSX->write($data);
$writerXLSX->write($data, $style2);
$sheets->setCurrentSheet('sh2');
$writerXLSX->write($data2, $style3);
$sheets->setCurrentSheet('sh3');
$writerXLSX->write($dataExtra, $style1);
$sheets->previousSheet();
$sheets->previousSheet();
$writerXLSX->write($dataExtra);
$sheets->nextSheet();
$writerXLSX->write($data);
$writerXLSX->complete();
```
### Licence MIT License

Copyright (c) 2021 zarok13

Permission is hereby granted, free of charge, to any person obtaining a copy
of this software and associated documentation files (the "Software"), to deal
in the Software without restriction, including without limitation the rights
to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
copies of the Software, and to permit persons to whom the Software is
furnished to do so, subject to the following conditions:

The above copyright notice and this permission notice shall be included in all
copies or substantial portions of the Software.

THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE
SOFTWARE.