<?php

class PaginationTestObject implements \Zver\PaginationInterface
{

    public $data = [];

    function __construct($i)
    {
        $this->data = range($i, $i * 2);
    }

    function getPaginationItems($offset, $length)
    {
        return array_slice($this->data, $offset, $length);
    }

    function getPaginationItemsCount()
    {
        return count($this->data);
    }

}