<?php

namespace Zver;

interface PaginationInterface
{

    public function getPaginationItems($offset, $length);

    public function getPaginationItemsCount();

}
