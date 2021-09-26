<?php

/*
 * This file is part of the Pipeline package.
 *
 * (c) Mark Fluehmann dbiz.apps@gmail.com
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace timatanga\Pipeline;

use timatanga\Pipeline\Exceptions\PipeException;
use timatanga\Pipeline\Exceptions\RuntimeException;

class Pipeline
{

    /**
     * The object being passed through the pipeline
     * 
     * @var mixed
     */
    protected $object;

    /**
     * Array of Callables
     *
     * @var array
     */
    protected $pipes = [];


    /**
     * Create a new class instance.
     *
     * @param array  $pipes
     * @return void
     */
    public function __construct( array $pipes = [] )
    { 
        if ( empty($pipes) )
            $this->pipes = [];

        foreach ($pipes as $pipe) {
            
            if (! is_callable($pipe) )
                throw new PipeException('Invalide pipe, must be a callable');

            $this->pipes[] = $pipe;
        }
    }  


    /**
     * Set the object being sent through the pipeline.
     *
     * @param  mixed  $object
     * @return $this
     */
    public function send( $object )
    {
        $this->object = $object;

        return $this;
    }


    /**
     * Add pipes to pipeline
     *
     * @param array  $pipes
     * @return $this
     */
    public function through( array $pipes = [] )
    {
        foreach ($pipes as $pipe) {
            
            // in case given pipe is not callable throw exception
            if (! is_callable($pipe) )
                throw new PipeException('Invalide pipe, must be a callable');

            $this->pipes[] = $pipe;
        }

        return $this;
    }


    /**
     * Process pipeline 
     *
     * @param mixed  $object
     * @return $this
     */
    public function process()
    {
        // pipeline without pipes returns object untouched
        if ( empty($this->pipes) )
            return $this->object;

        try {

            $result = $this->object;

            // process object through all pipes
            foreach ($this->pipes as $pipe) {
                $result = $pipe($result);
            }

        } catch (\Throwable $e) {
            throw new RuntimeException($e->getMessage());
        }

        return $result;
    }


    /**
     * Get Pipes
     *
     * @return array
     */
    public function getPipes()
    {
        return $this->pipes;
    }
}