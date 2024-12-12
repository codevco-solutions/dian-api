<?php

namespace App\Http\Controllers\API\Location;

use App\Http\Controllers\Controller;
use App\Services\Location\StateService;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class StateController extends Controller
{
    protected $stateService;

    public function __construct(StateService $stateService)
    {
        $this->stateService = $stateService;
    }

    public function index()
    {
        return response()->json($this->stateService->all(), Response::HTTP_OK);
    }

    public function show($id)
    {
        return response()->json($this->stateService->find($id), Response::HTTP_OK);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'country_id' => 'required|exists:countries,id',
            'name' => 'required|string|max:255',
            'code' => 'required|string|max:50',
            'is_active' => 'boolean'
        ]);

        return response()->json($this->stateService->create($data), Response::HTTP_CREATED);
    }

    public function update(Request $request, $id)
    {
        $data = $request->validate([
            'country_id' => 'sometimes|exists:countries,id',
            'name' => 'sometimes|string|max:255',
            'code' => 'sometimes|string|max:50',
            'is_active' => 'boolean'
        ]);

        return response()->json($this->stateService->update($id, $data), Response::HTTP_OK);
    }

    public function destroy($id)
    {
        $this->stateService->delete($id);
        return response()->json(null, Response::HTTP_NO_CONTENT);
    }
}
