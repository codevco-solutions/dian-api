<?php

namespace App\Repositories\Contracts;

interface DocumentReferenceRepositoryInterface
{
    public function create(array $data);
    public function update($id, array $data);
    public function find($id);
    public function findByDocument($documentType, $documentId);
    public function delete($id);
}
