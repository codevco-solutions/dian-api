<?php

namespace App\Http\Controllers\API\Common;

use App\Http\Controllers\Controller;
use App\Http\Resources\Common\AddressResource;
use App\Models\Common\Address;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Validator;

class AddressController extends Controller
{
    protected $model;
    protected $modelType;

    public function __construct(Request $request)
    {
        $this->modelType = $request->route('type');
        $modelClass = "App\\Models\\" . ucfirst($this->modelType) . "\\" . ucfirst($this->modelType);
        $this->model = app($modelClass);
    }

    public function index(Request $request, $type, $id)
    {
        $parent = $this->model->findOrFail($id);
        
        $query = $parent->addresses()->with(['country', 'state', 'city']);

        if ($request->has('is_active')) {
            $query->where('is_active', $request->boolean('is_active'));
        }

        $addresses = $query->get();

        return AddressResource::collection($addresses);
    }

    public function store(Request $request, $type, $id)
    {
        $parent = $this->model->findOrFail($id);

        $validator = Validator::make($request->all(), [
            'country_id' => 'required|exists:countries,id',
            'state_id' => 'required|exists:states,id',
            'city_id' => 'required|exists:cities,id',
            'address_line1' => 'required|string|max:255',
            'address_line2' => 'nullable|string|max:255',
            'postal_code' => 'nullable|string|max:20',
            'phone' => 'nullable|string|max:20',
            'mobile' => 'nullable|string|max:20',
            'email' => 'nullable|email|max:255',
            'contact_person' => 'nullable|string|max:255',
            'is_main' => 'boolean',
            'is_billing' => 'boolean',
            'is_shipping' => 'boolean',
            'is_active' => 'boolean'
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], Response::HTTP_UNPROCESSABLE_ENTITY);
        }

        // Si es dirección principal, desmarcar las otras
        if ($request->boolean('is_main')) {
            $parent->addresses()->where('is_main', true)->update(['is_main' => false]);
        }

        // Si es dirección de facturación, desmarcar las otras
        if ($request->boolean('is_billing')) {
            $parent->addresses()->where('is_billing', true)->update(['is_billing' => false]);
        }

        // Si es dirección de envío, desmarcar las otras
        if ($request->boolean('is_shipping')) {
            $parent->addresses()->where('is_shipping', true)->update(['is_shipping' => false]);
        }

        $address = $parent->addresses()->create($validator->validated());

        return new AddressResource($address->load(['country', 'state', 'city']));
    }

    public function show($type, $parentId, $id)
    {
        $parent = $this->model->findOrFail($parentId);
        
        $address = $parent->addresses()
            ->with(['country', 'state', 'city'])
            ->findOrFail($id);

        return new AddressResource($address);
    }

    public function update(Request $request, $type, $parentId, $id)
    {
        $parent = $this->model->findOrFail($parentId);
        $address = $parent->addresses()->findOrFail($id);

        $validator = Validator::make($request->all(), [
            'country_id' => 'sometimes|required|exists:countries,id',
            'state_id' => 'sometimes|required|exists:states,id',
            'city_id' => 'sometimes|required|exists:cities,id',
            'address_line1' => 'sometimes|required|string|max:255',
            'address_line2' => 'nullable|string|max:255',
            'postal_code' => 'nullable|string|max:20',
            'phone' => 'nullable|string|max:20',
            'mobile' => 'nullable|string|max:20',
            'email' => 'nullable|email|max:255',
            'contact_person' => 'nullable|string|max:255',
            'is_main' => 'boolean',
            'is_billing' => 'boolean',
            'is_shipping' => 'boolean',
            'is_active' => 'boolean'
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], Response::HTTP_UNPROCESSABLE_ENTITY);
        }

        // Si es dirección principal, desmarcar las otras
        if ($request->boolean('is_main')) {
            $parent->addresses()->where('id', '!=', $id)->where('is_main', true)->update(['is_main' => false]);
        }

        // Si es dirección de facturación, desmarcar las otras
        if ($request->boolean('is_billing')) {
            $parent->addresses()->where('id', '!=', $id)->where('is_billing', true)->update(['is_billing' => false]);
        }

        // Si es dirección de envío, desmarcar las otras
        if ($request->boolean('is_shipping')) {
            $parent->addresses()->where('id', '!=', $id)->where('is_shipping', true)->update(['is_shipping' => false]);
        }

        $address->update($validator->validated());

        return new AddressResource($address->load(['country', 'state', 'city']));
    }

    public function destroy($type, $parentId, $id)
    {
        $parent = $this->model->findOrFail($parentId);
        $address = $parent->addresses()->findOrFail($id);

        $address->delete();

        return response()->json(null, Response::HTTP_NO_CONTENT);
    }
}
