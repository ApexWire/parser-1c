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

Before they like to use the library, you need to connect it: `use apexwire\parser\file1c\Parser;`

The library has two 1s contents of the file transfer options:

* Get the content directly from the file: `Parser::createFromFile('filename');`;
* Transfer the contents in text form: `Parser::create('text');`.

As a result of execution of the above functions, to obtain the object:

- a list of possible errors: `errors`
- the status of the implementation of parsing file: `success`
- a list of file properties: `properties`
- a list of available sections in the file: `sections`. It contains an array of objects sections. Each object contains the same: `errors`,` success`, `properties`.