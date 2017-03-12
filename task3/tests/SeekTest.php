<?php namespace Tests;


class SeekTest extends \PHPUnit_Framework_TestCase
{
    public $filename;

    public function setUp()
    {
        $this->filename = __DIR__ . "/filename.txt";

        if (file_exists($this->filename))
            unlink($this->filename);

        $f = fopen($this->filename, 'w');
        for($i = 0; $i<1001; $i++)
            fputs($f, $i . ": demo line \n");
        fclose($f);
    }

    /** @test */
    public function is_can_seek_by_line_number()
    {
        $seeker = new \Task\SeekLine($this->filename);
        $seeker->seek(39);
        $line = $seeker->current();

        $this->assertEquals("39: demo line", $line);
    }

    /** @test */
    public function is_can_seek_and_can_next()
    {
        $seeker = new \Task\SeekLine($this->filename);
        $seeker->seek(39);
        $seeker->next();
        $seeker->next();
        $seeker->next();
        $line = $seeker->current();

        $this->assertEquals("42: demo line", $line);
    }
    /** @test */
    public function is_can_get_current_key()
    {
        $seeker = new \Task\SeekLine($this->filename);
        $seeker->seek(1000);
        $this->assertEquals(1000,$seeker->key());
    }
    /** @test */
    public function is_can_move_to_begin_of_file()
    {
        $seeker = new \Task\SeekLine($this->filename);
        $seeker->seek(39);
        $seeker->rewind();
        $this->assertEquals(0,$seeker->key());
    }

    public function tearDown()
    {
        if (file_exists($this->filename))
            unlink($this->filename);
    }

}

