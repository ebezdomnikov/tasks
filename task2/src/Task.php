<?php namespace Task;

class Task
{
    /**
     * Db config
     * @var
     */
    private $dbConfig;

    /**
     * fetch limit
     * @var int
     */
    private $fetchLimit;

    /**
     * Task constructor.
     * @param $dbConfig
     * @param int $fetchLimit
     */
    public function __construct($dbConfig, $fetchLimit = 50000)
    {
        $this->dbConfig = $dbConfig;

        $this->fetchLimit = $fetchLimit;
    }

    /**
     * Execute task
     */
    public function execute()
    {
        // connect to db
        $db = new Db($this->dbConfig);
        $db->connect();

        // model for get user data
        $objUsers       = new Users($db);
        // model for calculating data
        $objUserCounter = new UserCounter();

        $offset = 0;
        $limit  = $this->fetchLimit; //limit, by default 50000

        while ($users = $objUsers->get($offset, $limit))
        {
            $objUserCounter->push($users); //push and calculate
            $offset += $limit;
        }

        // report class
        $objReport = new UserReport($objUserCounter->result());
        // print report to std out
        $objReport->report();
    }
}