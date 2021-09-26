<?php

namespace Tests;

use PHPUnit\Framework\TestCase;
use timatanga\Pipeline\Exceptions\PipeException;
use timatanga\Pipeline\Exceptions\RuntimeException;
use timatanga\Pipeline\Pipeline;

class PipelineTest extends TestCase
{

    public function test_create_pipeline_without_params()
    {
        $pipeline = new Pipeline;

        $this->assertSame( $pipeline->getPipes(), [] );
    }


    public function test_create_pipeline_with_params()
    {
        $fn = function($arg) { return $arg++; };

        $pipeline = new Pipeline([$fn]);

        $this->assertTrue( count($pipeline->getPipes()) == 1 );
    }


    public function test_process_pipeline_with_integers()
    {
        $fn = function($arg) { return ++$arg; };

        $pipeline = new Pipeline();

        $result = $pipeline->send(1)->through([$fn])->process();

        $this->assertTrue( $result == 2 );
    }


    public function test_process_pipeline_with_string()
    {
        $fn = function($arg) { return 'prefix-'.$arg; };

        $pipeline = new Pipeline();

        $result = $pipeline->send('test')->through([$fn])->process();

        $this->assertTrue( $result == 'prefix-test' );
    }


    public function test_process_pipeline_with_string_II()
    {
        $fn = function($arg) { return 'prefix-'.$arg; };

        $pipeline = new Pipeline();

        $result = $pipeline->send('test')->through([$fn, $fn])->process();

        $this->assertTrue( $result == 'prefix-prefix-test' );
    }


    public function test_process_pipeline_with_array()
    {
        $fn = function($arg) { $arg['arg'] += 10; return $arg; };

        $pipeline = new Pipeline();

        $result = $pipeline->send(['name' => 'test', 'arg' => 10])->through([$fn])->process();

        $this->assertTrue( $result['arg'] == 20 );
    }


    public function test_process_pipeline_with_invokeable_class()
    {
        $fn = new TestInvokeableClass(10);

        $pipeline = new Pipeline();

        $result = $pipeline->send(10)->through([$fn])->process();

        $this->assertTrue( $result == 20 );
    }


    public function test_process_pipeline_with_pipe_exception()
    {
        $this->expectException(PipeException::class);

        $fn = new TestCallableClass(10);

        $pipeline = new Pipeline();

        $result = $pipeline->send(10)->through([$fn])->process();
    }


    public function test_process_pipeline_with_runtime_exception()
    {
        $this->expectException(RuntimeException::class);

        $fn = new TestInvokeableClass('test');

        $pipeline = new Pipeline();

        $result = $pipeline->send(10)->through([$fn])->process();
    }


    public function test_process_pipeline_with_string_functions()
    {
        $object = ' Foo bar ';

        $fn_a = function($arg) { return trim($arg); };
        $fn_b = function($arg) { return str_replace('Foo', 'foo', $arg); };
        $fn_c = function($arg) { return str_replace('bar', 'foo', $arg); };

        $result = (new Pipeline())->send($object)->through([$fn_a, $fn_b, $fn_c])->process();

        $this->assertTrue( $result == 'foo foo' );
    }
}


class TestInvokeableClass
{
    protected $args;

    public function __construct($args)
    {
        $this->args = $args;
    }

    public function __invoke($args)
    {
        $this->args += $args; 

        return $this->args;
    }
}


class TestCallableClass
{
    protected $args;

    public function __construct($args)
    {
        $this->args = $args;
    }

    public function tester($args) {

        $this->args += $args; 

        return $this->args; 
    }

    public function __callable($name, $args)
    {
        call_user_func($name, $args);
    }
}