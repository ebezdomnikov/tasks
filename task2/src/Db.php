<?php namespace Task;

use Exception;

class Db {

    /**
     * Const for Query, if no result needed
     */
    const NO_RESULT = 1;

    /**
     * Const for Query, if result needed.
     */
    const WITH_RESULT = 2;

    /**
     * Db handle
     * @var
     */
    private $db;

    /**
     * Config for connection
     * @var
     */
    private $config;

    /**
     * For fetching result
     * @var
     */
    private $result;

    /**
     * current query
     * @var
     */
    private $sQuery;

    /**
     * Db constructor.
     * @param $config
     */
    public function __construct( $config )
    {
        $this->config = $config;
    }

    /**
     * Check is base alive
     * @return bool
     */
    public function isLive()
    {
        $this->setQuery("SELECT 1 as cn from dual");
        $result = $this->getAssocList();

        if (isset($result["cn"]))
        {
            return true;
        }

        return false;
    }

    /**
     * set current query, need for getAssocList
     * @param $sQuery
     */
    public function setQuery( $sQuery )
    {
        $this->sQuery = $sQuery;
        $this->result = null;
    }

    /**
     * Fetching result as associative array
     * @return array|null
     */
    public function getAssocList()
    {
        if (is_null($this->result))
        {
            $this->result = $this->query($this->sQuery);
        }
        return mysqli_fetch_assoc($this->result);
    }

    /**
     * Run query
     * @param $sQuery
     * @param int $mode
     * @return bool|\mysqli_result|null
     * @throws Exception
     */
    public function query( $sQuery, $mode = Db::WITH_RESULT )
    {
        $this->result = null;

        $this->check(); //check alive

        // replace some chars
        $filteredQuery = preg_replace("/[\n\r\t]/", " ", $sQuery);

        // always escape
        $escQuery = mysqli_escape_string($this->db, $filteredQuery);

        $result = mysqli_query($this->db, $escQuery);

        if ($result === false)
        {
            throw new Exception("Error while query {$sQuery} " . mysqli_error($this->db));
        }

        // if need result
        if ($mode == Db::WITH_RESULT)
        {
            return $result;
        }

        return null;
    }

    /**
     * Check db connection
     * @throws Exception
     */
    private function check()
    {
        if ( ! $this->db)
        {
            throw new Exception("No connection to database ");
        }
    }

    /**
     * class Destructor for close Db connection
     */
    public function __destruct()
    {
        $this->close();
    }

    /**
     * Close db connection
     */
    public function close()
    {
        if ($this->db)
        {
            mysqli_close($this->db);
        }
    }

    /**
     * Open db connection
     * @return \mysqli
     * @throws Exception
     */
    public function connect()
    {
        if (function_exists("mysqli_connect"))
        {
            try
            {
                $this->db =
                    mysqli_connect($this->getHost(), $this->getUserName(), $this->getPassword(), $this->getDb(), $this->getPort());

                if ( ! $this->db)
                {
                    throw new Exception("Error while connect to database, check if server is working and your conditionals.");
                }

                return $this->db;
            }
            catch (Exception $e)
            {
                throw new Exception($e->getMessage() . "\n" . mysqli_connect_error());
            }
        }

        throw new Exception("Extension MySQLi not installed.");
    }

    /**
     * Get host from config
     * @return null
     */
    private function getHost()
    {
        return $this->getParamOrFail('password');
    }

    /**
     * Get parameter from config by name with default
     * @param $name
     * @param null $default
     * @return null
     * @throws Exception
     */
    private function getParamOrFail( $name, $default = null )
    {
        if (isset($this->config[ $name ]))
        {
            return $this->config[ $name ];
        }

        if ( ! is_null($default))
        {
            return $default;
        }

        throw new Exception("Config parameter {$name} not set");
    }

    /**
     * Get username from config
     * @return null
     */
    private function getUserName()
    {
        return $this->getParamOrFail('username');
    }

    /**
     * Get Password from config
     * @return null
     */
    private function getPassword()
    {
        return $this->getParamOrFail('password');
    }

    /**
     * get db name from config
     * @return null
     */
    private function getDb()
    {
        return $this->getParamOrFail('db');
    }

    /**
     * get port from config
     * @return null
     */
    private function getPort()
    {
        return $this->getParamOrFail('port', 3306);
    }

    /**
     * Run statement without result
     * @param $sQuery
     */
    public function statement( $sQuery )
    {
        $this->query($sQuery, Db::NO_RESULT);
    }
}

