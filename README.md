# SSWriter or Spread Sheet Writer.

## lightweight extension for spread sheet files.

## How to use
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
