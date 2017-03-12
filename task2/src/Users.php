<?php namespace Task;

/**
 * Class Users
 * @package Task
 */
class Users
{
    private $db;

    const TEMP_TABLE_NAME = 'temp_users';

    const USERS_TABLE_NAME = 'users';

    public function __construct(Db $db)
    {
        $this->db = $db;
        $this->refresh(); // Load new Data in Temp
    }

    /**
     *  Drop temporary table
     */
    private function clearTemp()
    {
        $this->db->statement("DROP TABLE IF EXISTS `". self::TEMP_TABLE_NAME . "`");
    }

    /**
     *  Copy Users Into Temp table
     */
    private function copyToTemp()
    {
        $this->db->query("CREATE TEMPORARY TABLE IF NOT EXISTS `" . self::TEMP_TABLE_NAME . "` AS (SELECT `id`, `email` FROM `" . self::USERS_TABLE_NAME . "`)");
    }

    /**
     * Refresh data
     */
    public function refresh()
    {
        $this->clearTemp();
        $this->copyToTemp();
    }

    /**
     * Get data from Temp table
     * @param null $offset table select offset
     * @param null $limit table select limit
     * @return array
     */
    public function get($offset = null, $limit = null)
    {
        $sLimit = "";

        if (! is_null($offset) && ! is_null($limit))
        {
            $sLimit = " LIMIT {$offset}, ${limit}";
        }

        $sQuery = "
              SELECT 
                `id`, `email` 
              FROM 
                `" . self::TEMP_TABLE_NAME . "`
              {$sLimit}
        ";

        $rUsers = [];

        $this->db->setQuery($sQuery);

        while($user = $this->db->getAssocList())
        {
            $rUsers[] = $user;
        }

        return $rUsers;
    }
}