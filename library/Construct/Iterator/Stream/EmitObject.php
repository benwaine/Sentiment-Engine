<?php
namespace Construct\Iterator\Stream;
/**
 * An extension of the stream iterator.
 * Takes a lamda function that creates an object.
 *
 * @package    Construct
 * @subpackage Iterator
 * @author     Ben Waine <ben@ben-waine.co.uk>
 */
class EmitObject extends Iterator
{
    /**
     * A lambda function that returns an object.
     *
     * @var function
     */
    protected $emitFunction;

    /**
     * Initialises an instance of the EmitObject iterator.
     *
     * @param resource $stream       An open stream resource.
     * @param function $emitFunction A function that returns an object.
     *
     * @return void
     */
    public function __construct($stream, $emitFunction)
    {
        $this->emitFunction = $emitFunction;
        parent::__construct($stream);
    }

    /**
     * Gets the next line from pointer and uses the emit function to
     * return an object.
     *
     * @return object
     */
    public function current()
    {
       if(!isset($this->emitFunction))
       {
           throw new \RuntimeException('No Emit function set');
       }

       $data = fgets($this->stream);
       $fn = $this->emitFunction;
       return $fn($data);
    }


}

