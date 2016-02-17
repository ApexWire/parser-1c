#parser-1c
PHP parser library for 1s files.


## Installation
The preferred way to install the extension through [Composer] (http://getcomposer.org/).
Start

    php composer.phar require apexwire / parser-1c "dev-master"

or add

	"apexwire/parser-1c": "dev-master"

in the section "require" your composer.json

## Requirements
In order to run you must meet the following requirements:

* version of php >=5.4.0

## Description
There are two options for the data source file and text. Each option provides a use case.
As a result of the creation of the object to obtain:

- A list of possible errors: `errors`
- The status of the implementation of parsing file: `success`
- A list of file properties: `properties`
- A list of available sections in the file: `sections`. It contains an array of objects sections. Each object contains the same: `errors`,` success`, `properties`.

### File
In this embodiment, a file is used as a data source. In order to process the file you need to create an object to pass the full path to the file.
Example of use:
`Use apexwire\parser1c\File;
$ Doc = new File ( 'file-path'); `

### Text
In this embodiment, a file is used as a data source. In order to process the file you need to create an object to pass the full path to the file.
Example of use:
`Use apexwire\parser1c\Text;
$ Doc = new Text ( 'file-path'); `