<?php

namespace App\Services\MasterTable;

use App\Repositories\Contracts\MasterTable\DocumentTypeRepositoryInterface;

class DocumentTypeService extends BaseMasterTableService
{
    public function __construct(DocumentTypeRepositoryInterface $repository)
    {
        parent::__construct($repository);
    }
}