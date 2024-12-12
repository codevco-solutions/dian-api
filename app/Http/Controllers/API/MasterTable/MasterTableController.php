<?php

namespace App\Http\Controllers\API\MasterTable;

use App\Http\Controllers\Controller;
use App\Http\Resources\MasterTable\MasterTableResource;
use App\Services\MasterTable\MasterTableService;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Validator;

class MasterTableController extends Controller
{
    protected $service;
    protected $tableName;

    public function __construct(MasterTableService $service)
    {
        $this->service = $service;
    }

    protected function setTable(string $table)
    {
        $this->tableName = $table;
        return $this;
    }

    public function index()
    {
        $records = $this->service->getAll($this->tableName);
        return MasterTableResource::collection($records);
    }

    public function show($id)
    {
        $record = $this->service->getById($this->tableName, $id);
        return new MasterTableResource($record);
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'code' => 'required|string|max:50|unique:' . $this->tableName,
            'description' => 'nullable|string',
            'is_active' => 'boolean'
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], Response::HTTP_UNPROCESSABLE_ENTITY);
        }

        $record = $this->service->create($this->tableName, $validator->validated());
        return new MasterTableResource($record);
    }

    public function update(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'sometimes|required|string|max:255',
            'code' => 'sometimes|required|string|max:50|unique:' . $this->tableName . ',code,' . $id,
            'description' => 'nullable|string',
            'is_active' => 'boolean'
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], Response::HTTP_UNPROCESSABLE_ENTITY);
        }

        $record = $this->service->update($this->tableName, $id, $validator->validated());
        return new MasterTableResource($record);
    }

    public function destroy($id)
    {
        $this->service->delete($this->tableName, $id);
        return response()->json(null, Response::HTTP_NO_CONTENT);
    }

    public function active()
    {
        $records = $this->service->getActive($this->tableName);
        return MasterTableResource::collection($records);
    }
}
