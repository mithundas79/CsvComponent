CsvComponent
============
 
Allows the importing and exporting of a standard $this->data formatted array to and from csv files.

How to use
==========

You can call it in your controller like a normal component

```php
public $components = array(
        'Csv'
    );
```

Import
======

Simply upload the Csv file and pass the file onto the Csv component like following:-
```php
$file = $full_file_path.$filename;
$this->data = $this->Csv->import($file);
//save data to model
$this->Model->saveAll($this->data);
``` 

Export
======

Read the data onto a variable and export it to a csv file like following:-
```php
$data = $this->Model->find('all', array('recursive' => -1));
//store the file path
$filepath = "files/csv/exports/model.csv";
//pass the filepath and data onto the Csv export method
$this->Csv->export($filepath, $data);
```
