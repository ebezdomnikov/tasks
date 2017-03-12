<?php namespace Task;

/**
 * Class UserCounter
 * Counting users by domain and collect into array
 * @package Task
 */
class UserCounter
{

    /**
     * array [domain] = count
     * @var
     */
    private $arCount;

    /**
     * Reset counting
     */
    public function reset()
    {
        $this->arCount = [];
    }

    /**
     * get result
     * @return mixed
     */
    public function result()
    {
        return $this->arCount;
    }

    /**
     * Check record structure
     * @param $user
     * @return bool
     */
    public function check($user)
    {
        return key_exists('email',$user) && key_exists('id',$user);
    }

    /**
     * Counting users by domain
     * @param $user
     * @param $items
     */
    private function counting($user, $items)
    {
        if ( ! isset($items[1]))
            return;

        $items = $items[1];

        foreach ($items as $domain)
        {
            if ( ! isset($this->arCount[$domain][$user['id']]))
                $this->arCount[$domain][$user['id']]=1;
        }
    }

    /**
     * Push new data
     * @param array $users
     * @throws \Exception
     */
    public function push(array $users)
    {
        foreach ($users as $user)
        {
            if ( ! $this->check($user))
                throw new \Exception("Structure of item is wrong. Need id and email keys");

            if (empty($user['email']))
                continue;

            if (preg_match_all("/@([а-яa-z-.]+)/ui", $user['email'], $items) !== FALSE)
            {
                $this->counting($user, $items);
            }
        }
    }
}