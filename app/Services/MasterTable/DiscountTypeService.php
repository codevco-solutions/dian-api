<?php

namespace App\Services\MasterTable;

use App\Repositories\Contracts\MasterTable\DiscountTypeRepositoryInterface;

class DiscountTypeService extends BaseMasterTableService
{
    public function __construct(DiscountTypeRepositoryInterface $repository)
    {
        parent::__construct($repository);
    }
}