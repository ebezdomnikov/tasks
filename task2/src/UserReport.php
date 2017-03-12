<?php namespace Task;

class UserReport
{
    private $arCount;

    public function __construct(array $arCount)
    {
        $this->arCount = $arCount;
    }

    public function report()
    {
        foreach ($this->arCount as $domain => $arIds)
        {
            echo $domain . ": " . count($arIds) . "\n";
        }
    }
}