<?php namespace Tests;


use Task\UserCounter;

class CounterTest extends \PHPUnit_Framework_TestCase
{
    public $arCountTestData;

    public $arCountTestDataFail;

    public function setUp()
    {
        $this->arCountTestData = [
            [
                'id' => 1,
                'email' => 'test@mail.ru, test2@mail.ru, test3@gmail.com'
            ],
            [
                'id' => 2,
                'email' => 'test5@mail.ru, test3@yahoo.com'
            ],
            [
                'id' => 3,
                'email' => 'test5@mail.ru'
            ],
            [
                'id' => 4,
                'email' => ''
            ]
        ];

        $this->arCountTestDataFail = [
            [
                'id' => 1,
                'email' => 'test@mail.ru, test2@mail.ru, test3@gmail.com'
            ],
            [
                'wrong_id' => 2,
                'email' => 'test5@mail.ru, test3@yahoo.com'
            ],
            [
                'id' => 3,
                'email' => 'test5@mail.ru'
            ],
            [
                'id' => 4,
                'email' => ''
            ]
        ];
    }

    /** @test */
    public function is_can_calculate_users_by_domain()
    {
        $obj = new UserCounter();
        $obj->push($this->arCountTestData);
        $result = $obj->result();

        $check = count($result["mail.ru"]) == 3 &&
            count($result["gmail.com"]) == 1 &&
            count($result["yahoo.com"]) == 1;

        $this->assertTrue($check);
    }
    /** @test */
    public function is_can_throw_exception_on_wrong_users_format()
    {
        $this->expectException(\Exception::class);

        $obj = new UserCounter();
        $obj->push($this->arCountTestDataFail);
    }

    /** @test */
    public function is_can_reset()
    {
        $obj = new UserCounter();
        $obj->push($this->arCountTestData);

        $obj->reset();

        $this->assertTrue(count($obj->result())==0);
    }
}