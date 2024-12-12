<?php

namespace App\Http\Controllers\API\Location;

use App\Http\Controllers\Controller;
use App\Services\Location\CityService;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class CityController extends Controller
{
    protected $cityService;

    public function __construct(CityService $cityService)
    {
        $this->cityService = $cityService;
    }

    public function index()
    {
        return response()->json($this->cityService->all(), Response::HTTP_OK);
    }

    public function show($id)
    {
        return response()->json($this->cityService->find($id), Response::HTTP_OK);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'state_id' => 'required|exists:states,id',
            'name' => 'required|string|max:255',
            'code' => 'required|string|max:50',
            'is_active' => 'boolean'
        ]);

        return response()->json($this->cityService->create($data), Response::HTTP_CREATED);
    }

    public function update(Request $request, $id)
    {
        $data = $request->validate([
            'state_id' => 'sometimes|exists:states,id',
            'name' => 'sometimes|string|max:255',
            'code' => 'sometimes|string|max:50',
            'is_active' => 'boolean'
        ]);

        return response()->json($this->cityService->update($id, $data), Response::HTTP_OK);
    }

    public function destroy($id)
    {
        $this->cityService->delete($id);
        return response()->json(null, Response::HTTP_NO_CONTENT);
    }
}
