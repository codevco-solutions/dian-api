<?php

namespace App\Http\Controllers\API\Location;

use App\Http\Controllers\Controller;
use App\Services\Location\CountryService;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class CountryController extends Controller
{
    protected $countryService;

    public function __construct(CountryService $countryService)
    {
        $this->countryService = $countryService;
    }

    public function index()
    {
        return response()->json($this->countryService->all(), Response::HTTP_OK);
    }

    public function show($id)
    {
        return response()->json($this->countryService->find($id), Response::HTTP_OK);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'code_2' => 'required|string|size:2',
            'code_3' => 'required|string|size:3',
            'numeric_code' => 'required|string|max:10',
            'is_active' => 'boolean'
        ]);

        return response()->json($this->countryService->create($data), Response::HTTP_CREATED);
    }

    public function update(Request $request, $id)
    {
        $data = $request->validate([
            'name' => 'sometimes|string|max:255',
            'code_2' => 'sometimes|string|size:2',
            'code_3' => 'sometimes|string|size:3',
            'numeric_code' => 'sometimes|string|max:10',
            'is_active' => 'boolean'
        ]);

        return response()->json($this->countryService->update($id, $data), Response::HTTP_OK);
    }

    public function destroy($id)
    {
        $this->countryService->delete($id);
        return response()->json(null, Response::HTTP_NO_CONTENT);
    }
}
