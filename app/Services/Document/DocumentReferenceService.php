<?php

namespace App\Services\Document;

use App\Repositories\Contracts\DocumentReferenceRepositoryInterface;

class DocumentReferenceService
{
    protected $repository;

    public function __construct(DocumentReferenceRepositoryInterface $repository)
    {
        $this->repository = $repository;
    }

    public function create(array $data)
    {
        // Validar tipo de documento y UUID
        $this->validateDocumentType($data['document_type_code']);
        if (isset($data['uuid'])) {
            $this->validateUUID($data['uuid']);
        }

        return $this->repository->create($data);
    }

    public function update($id, array $data)
    {
        if (isset($data['document_type_code'])) {
            $this->validateDocumentType($data['document_type_code']);
        }
        if (isset($data['uuid'])) {
            $this->validateUUID($data['uuid']);
        }

        return $this->repository->update($id, $data);
    }

    public function getByDocument($documentType, $documentId)
    {
        return $this->repository->findByDocument($documentType, $documentId);
    }

    public function delete($id)
    {
        return $this->repository->delete($id);
    }

    protected function validateDocumentType($type)
    {
        // Validar que el tipo de documento sea válido según DIAN
        $validTypes = ['01', '02', '03', '91', '92']; // Agregar tipos válidos según DIAN
        if (!in_array($type, $validTypes)) {
            throw new \InvalidArgumentException('Invalid document type code');
        }
    }

    protected function validateUUID($uuid)
    {
        // Validar formato CUFE/CUDE
        if (!preg_match('/^[0-9a-f]{8}-[0-9a-f]{4}-4[0-9a-f]{3}-[89ab][0-9a-f]{3}-[0-9a-f]{12}$/i', $uuid)) {
            throw new \InvalidArgumentException('Invalid UUID format');
        }
    }
}
