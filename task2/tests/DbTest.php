<?php namespace Tests;


use Task\Db;

class DbTest extends \PHPUnit_Framework_TestCase
{
    public $config = [];

    public function setupOkDb()
    {
        $this->config['db'] = '100m';
        $this->config['username'] = 'root';
        $this->config['password'] = '';
        $this->config['host'] = '127.0.0.1';

        return $this->config;
    }

    public function setupFailDb()
    {
        $this->config['db'] = 'not_existed_db';
        $this->config['username'] = 'root';
        $this->config['password'] = '';
        $this->config['host'] = '127.0.0.1';

        return $this->config;
    }

    /** @test */
    public function is_can_connect_to_db()
    {
        $db = new \Task\Db($this->setupOkDb());
        $db->connect();
        $this->assertTrue($db->isLive());
    }

    /** @test */
    public function is_throw_exception_on_fail_connect()
    {
        $this->expectException(\Exception::class);

        $db = new \Task\Db($this->setupFailDb());
        $db->connect();
    }
    /** @test */
    public function is_can_select_data()
    {
        $db = new Db($this->setupOkDb());
        $db->connect();
        $db->setQuery("SELECT 1 as cn FROM dual");
        $result = $db->getAssocList();
        $this->assertTrue($result['cn'] == 1);
    }
}
?>