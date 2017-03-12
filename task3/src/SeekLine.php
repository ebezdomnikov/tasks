<?php namespace Task;

use Exception;

/**
 * Class SeekLine
 *
 * @package Task
 */
class SeekLine implements \SeekableIterator {

    /**
     * File handle
     * @var resource
     */
    protected $fHandle;

    /**
     * current position in file
     * @var int
     */
    protected $position = 0;

    /**
     * current line
     * @var null
     */
    protected $currentLine = null;

    /**
     * SeekRaw constructor.
     * @param $filename
     * @param string $mode
     * @throws Exception
     */
    function __construct( $filename, $mode = "rb" )
    {
        if (file_exists($filename))
        {
            try
            {
                $this->fHandle = fopen($filename, $mode);

                if (is_null($this->fHandle))
                {
                    throw new Exception("Null handler");
                }
            }
            catch (Exception $e)
            {
                throw new Exception("Error while open file {$filename} : " . $e->getMessage());
            }
        }
        else
        {
            throw new Exception("File {$filename} does not exists!");
        }
    }

    /**
     * Current line
     * @return string
     */
    public function current()
    {
        if (is_null($this->currentLine))
        {
            $this->currentLine = $this->getLine();
        }

        return trim($this->currentLine);
    }

    /**
     * Get line content
     * @return null|string
     * @throws Exception
     */
    protected function getLine()
    {
        if ( ! $this->valid())
        {
            throw new Exception("Cannot read from file");
        }

        return $this->readLine();
    }

    /**
     * Validation of file
     * @return bool
     */
    public function valid()
    {
        return ! feof($this->fHandle);
    }

    /**
     * Read line from file
     * @return null|string
     * @throws Exception
     */
    protected function readLine()
    {
        if ( ! $this->valid())
        {
            $this->freeLine();
            throw new Exception("Cannot read from file");
        }

        if ($this->currentLine)
        {
            $this->position ++;
        }

        $this->freeLine();

        $this->currentLine = fgets($this->fHandle);

        return $this->currentLine;
    }

    /**
     * reset current line
     */
    protected function freeLine()
    {
        if ($this->currentLine)
        {
            $this->currentLine = null;
        }
    }

    /**
     * Move to next line
     */
    public function next()
    {
        if (is_null($this->currentLine)) //if we have sequence of next
        {
            $this->readLine();
        }

        $this->freeLine();
    }

    /**
     * Current line number
     * @return int
     */
    public function key()
    {
        return $this->position;
    }

    /**
     * move to line number in file
     * @param int $position
     */
    public function seek( $position )
    {
        $this->rewind();

        while ($this->position < $position && $this->valid())
        {
            $this->getLine();
        }
    }

    /**
     * Rewind to begin file
     */
    public function rewind()
    {
        $this->position = 0;
        fseek($this->fHandle, 0);
    }

    /**
     * Destructir for close file
     */
    public function __destruct()
    {
        $this->close();
    }

    /**
     * Close handler
     */
    public function close()
    {
        if ($this->fHandle)
        {
            fclose($this->fHandle);
        }
    }
}