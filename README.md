# Pipeline
A pipeline consists of a chain of processing elements, arranged so that the output of each element is the input of the next. 
The Pipeline package provides an easy to use, fluent interface to process an object (can be of any type) through a series of pipes (callables). 


## Installation
composer require timatanga/pipeline


## Usage
The most basic option to create a pipeline instance does not require any arguments;

    use timatanga\Pipeline\Pipeline;

    $pipeline = new Pipeline;


Having an instance of a pipeline class you can fluently add further steps

    $result = $pipeline->send($object)->through($pipes)->process();


An object can be of any type as you like. Please take notice that the chain of pipes must be able to handle objects type, else a RuntimeException will be thrown.
The argument of the through method accepts an array of callables. An object is always callable if it implements the magic invoke method, and that method is visible in the current scope. A class name is callable if it implements the callStatic method.
A function on the other hand is always handled as callable.


## Example
Trim and replace string through a pipeline

    use timatanga\Pipeline\Pipeline;

    $object = ' Foo bar ';
    $fn_a = function($arg) { return trim($arg); };
    $fn_b = function($arg) { return str_replace('Foo', 'foo', $arg); };
    $fn_c = function($arg) { return str_replace('bar', 'foo', $arg); };

    $result = (new Pipeline())->send($object)->through([$fn_a, $fn_b, $fn_c])->process();

    // results in 'foo foo'