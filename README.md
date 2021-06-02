# SSWriter or SpreadSheet Writer.

## lightweight extension for spreadsheet files(in development).

## How to use

```
composer require zarok13/sswriter
```

```
$data = [
    ['name1','type1'],
    ['name2', 'type2', 'ffsfsdf'],
    ['ttet', 'fadfsa']
];

$init = new SSWriter('out');
$writerXLSX = $init->initWriterXLSX();
$writerXLSX->write($data);
```
