<?php

namespace App\Services\Fiscal;

use App\Services\BaseService;
use App\Repositories\Fiscal\Interfaces\FiscalCalendarRepositoryInterface;
use Illuminate\Support\Facades\Validator;
use App\Exceptions\ValidationException;

class FiscalCalendarService extends BaseService
{
    protected $repository;

    public function __construct(FiscalCalendarRepositoryInterface $repository)
    {
        parent::__construct($repository);
    }

    protected function validateData(array $data, $id = null): array
    {
        $rules = [
            'company_id' => 'required|exists:companies,id',
            'name' => 'required|string|max:255',
            'type' => 'required|string|in:mensual,bimestral,trimestral,cuatrimestral,semestral,anual',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after:start_date',
            'due_dates' => 'required|array',
            'due_dates.*' => 'required|date|after_or_equal:start_date|before_or_equal:end_date',
            'is_active' => 'boolean'
        ];

        $validator = Validator::make($data, $rules);

        if ($validator->fails()) {
            throw new ValidationException($validator);
        }

        return $validator->validated();
    }

    public function getCurrentPeriods()
    {
        return $this->repository->getCurrentPeriods();
    }

    public function getUpcomingDueDates($limit = 5)
    {
        return $this->repository->getUpcomingDueDates($limit);
    }
}
