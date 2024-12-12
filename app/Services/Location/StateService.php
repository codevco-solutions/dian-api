<?php

namespace App\Services\Location;

use App\Models\State;
use App\Repositories\Contracts\Location\StateRepositoryInterface;

class StateService
{
    protected $stateRepository;

    public function __construct(StateRepositoryInterface $stateRepository)
    {
        $this->stateRepository = $stateRepository;
    }

    public function all()
    {
        return $this->stateRepository->all();
    }

    public function find($id)
    {
        return $this->stateRepository->find($id);
    }

    public function create(array $data)
    {
        return $this->stateRepository->create($data);
    }

    public function update($id, array $data)
    {
        return $this->stateRepository->update($id, $data);
    }

    public function delete($id)
    {
        return $this->stateRepository->delete($id);
    }
}
