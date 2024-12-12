<?php

namespace App\Http\Controllers\API\Supplier;

use App\Http\Controllers\Controller;
use App\Http\Resources\Supplier\SupplierResource;
use App\Models\Supplier\Supplier;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class SupplierController extends Controller
{
    public function index(Request $request)
    {
        $query = Supplier::query()
            ->where('company_id', $request->user()->company_id)
            ->with([
                'identificationType',
                'taxRegime',
                'taxResponsibilities',
                'mainAddress.country',
                'mainAddress.state',
                'mainAddress.city',
                'primaryContact'
            ]);

        if ($request->has('search')) {
            $search = $request->get('search');
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('commercial_name', 'like', "%{$search}%")
                    ->orWhere('identification_number', 'like', "%{$search}%");
            });
        }

        if ($request->has('identification_type_id')) {
            $query->where('identification_type_id', $request->get('identification_type_id'));
        }

        if ($request->has('tax_regime_id')) {
            $query->where('tax_regime_id', $request->get('tax_regime_id'));
        }

        if ($request->has('tax_responsibility_id')) {
            $query->whereHas('taxResponsibilities', function ($q) use ($request) {
                $q->where('tax_responsibility_id', $request->get('tax_responsibility_id'));
            });
        }

        if ($request->has('is_active')) {
            $query->where('is_active', $request->boolean('is_active'));
        }

        $suppliers = $query->paginate($request->get('per_page', 10));

        return SupplierResource::collection($suppliers);
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'identification_type_id' => 'required|exists:identification_types,id',
            'tax_regime_id' => 'nullable|exists:tax_regimes,id',
            'identification_number' => 'required|string|max:20|unique:suppliers,identification_number,NULL,id,company_id,' . $request->user()->company_id,
            'verification_digit' => 'nullable|string|max:1',
            'name' => 'required|string|max:255',
            'commercial_name' => 'nullable|string|max:255',
            'email' => 'nullable|email|max:255',
            'phone' => 'nullable|string|max:20',
            'mobile' => 'nullable|string|max:20',
            'website' => 'nullable|string|max:255',
            'notes' => 'nullable|string',
            'credit_limit' => 'nullable|numeric|min:0',
            'payment_term_days' => 'nullable|integer|min:0',
            'is_active' => 'boolean',
            'metadata' => 'nullable|json',
            'tax_responsibilities' => 'array',
            'tax_responsibilities.*' => 'exists:tax_responsibilities,id'
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], Response::HTTP_UNPROCESSABLE_ENTITY);
        }

        try {
            DB::beginTransaction();

            $supplier = new Supplier($validator->validated());
            $supplier->company_id = $request->user()->company_id;
            $supplier->save();

            if ($request->has('tax_responsibilities')) {
                $supplier->taxResponsibilities()->attach($request->get('tax_responsibilities'));
            }

            DB::commit();

            return new SupplierResource($supplier->load([
                'identificationType',
                'taxRegime',
                'taxResponsibilities'
            ]));
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['message' => 'Error al crear el proveedor'], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function show($id)
    {
        $supplier = Supplier::where('company_id', auth()->user()->company_id)
            ->with([
                'identificationType',
                'taxRegime',
                'taxResponsibilities',
                'addresses.country',
                'addresses.state',
                'addresses.city',
                'contacts',
                'mainAddress.country',
                'mainAddress.state',
                'mainAddress.city',
                'billingAddress.country',
                'billingAddress.state',
                'billingAddress.city',
                'shippingAddress.country',
                'shippingAddress.state',
                'shippingAddress.city',
                'primaryContact'
            ])
            ->findOrFail($id);

        return new SupplierResource($supplier);
    }

    public function update(Request $request, $id)
    {
        $supplier = Supplier::where('company_id', $request->user()->company_id)
            ->findOrFail($id);

        $validator = Validator::make($request->all(), [
            'identification_type_id' => 'sometimes|required|exists:identification_types,id',
            'tax_regime_id' => 'nullable|exists:tax_regimes,id',
            'identification_number' => 'sometimes|required|string|max:20|unique:suppliers,identification_number,' . $id . ',id,company_id,' . $request->user()->company_id,
            'verification_digit' => 'nullable|string|max:1',
            'name' => 'sometimes|required|string|max:255',
            'commercial_name' => 'nullable|string|max:255',
            'email' => 'nullable|email|max:255',
            'phone' => 'nullable|string|max:20',
            'mobile' => 'nullable|string|max:20',
            'website' => 'nullable|string|max:255',
            'notes' => 'nullable|string',
            'credit_limit' => 'nullable|numeric|min:0',
            'payment_term_days' => 'nullable|integer|min:0',
            'is_active' => 'boolean',
            'metadata' => 'nullable|json',
            'tax_responsibilities' => 'array',
            'tax_responsibilities.*' => 'exists:tax_responsibilities,id'
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], Response::HTTP_UNPROCESSABLE_ENTITY);
        }

        try {
            DB::beginTransaction();

            $supplier->update($validator->validated());

            if ($request->has('tax_responsibilities')) {
                $supplier->taxResponsibilities()->sync($request->get('tax_responsibilities'));
            }

            DB::commit();

            return new SupplierResource($supplier->load([
                'identificationType',
                'taxRegime',
                'taxResponsibilities'
            ]));
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['message' => 'Error al actualizar el proveedor'], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function destroy($id)
    {
        $supplier = Supplier::where('company_id', auth()->user()->company_id)
            ->findOrFail($id);

        try {
            DB::beginTransaction();

            // Eliminar relaciones
            $supplier->taxResponsibilities()->detach();
            $supplier->addresses()->delete();
            $supplier->contacts()->delete();
            $supplier->delete();

            DB::commit();

            return response()->json(null, Response::HTTP_NO_CONTENT);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['message' => 'Error al eliminar el proveedor'], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function active()
    {
        $suppliers = Supplier::where('company_id', auth()->user()->company_id)
            ->where('is_active', true)
            ->with([
                'identificationType',
                'taxRegime',
                'taxResponsibilities',
                'mainAddress.country',
                'mainAddress.state',
                'mainAddress.city',
                'primaryContact'
            ])
            ->get();

        return SupplierResource::collection($suppliers);
    }
}
