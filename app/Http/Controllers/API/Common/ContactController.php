<?php

namespace App\Http\Controllers\API\Common;

use App\Http\Controllers\Controller;
use App\Http\Resources\Common\ContactResource;
use App\Models\Common\Contact;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Validator;

class ContactController extends Controller
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
        
        $query = $parent->contacts();

        if ($request->has('is_active')) {
            $query->where('is_active', $request->boolean('is_active'));
        }

        $contacts = $query->get();

        return ContactResource::collection($contacts);
    }

    public function store(Request $request, $type, $id)
    {
        $parent = $this->model->findOrFail($id);

        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'position' => 'nullable|string|max:255',
            'department' => 'nullable|string|max:255',
            'email' => 'nullable|email|max:255',
            'phone' => 'nullable|string|max:20',
            'mobile' => 'nullable|string|max:20',
            'notes' => 'nullable|string',
            'is_primary' => 'boolean',
            'is_active' => 'boolean'
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], Response::HTTP_UNPROCESSABLE_ENTITY);
        }

        // Si es contacto primario, desmarcar los otros
        if ($request->boolean('is_primary')) {
            $parent->contacts()->where('is_primary', true)->update(['is_primary' => false]);
        }

        $contact = $parent->contacts()->create($validator->validated());

        return new ContactResource($contact);
    }

    public function show($type, $parentId, $id)
    {
        $parent = $this->model->findOrFail($parentId);
        $contact = $parent->contacts()->findOrFail($id);

        return new ContactResource($contact);
    }

    public function update(Request $request, $type, $parentId, $id)
    {
        $parent = $this->model->findOrFail($parentId);
        $contact = $parent->contacts()->findOrFail($id);

        $validator = Validator::make($request->all(), [
            'name' => 'sometimes|required|string|max:255',
            'position' => 'nullable|string|max:255',
            'department' => 'nullable|string|max:255',
            'email' => 'nullable|email|max:255',
            'phone' => 'nullable|string|max:20',
            'mobile' => 'nullable|string|max:20',
            'notes' => 'nullable|string',
            'is_primary' => 'boolean',
            'is_active' => 'boolean'
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], Response::HTTP_UNPROCESSABLE_ENTITY);
        }

        // Si es contacto primario, desmarcar los otros
        if ($request->boolean('is_primary')) {
            $parent->contacts()->where('id', '!=', $id)->where('is_primary', true)->update(['is_primary' => false]);
        }

        $contact->update($validator->validated());

        return new ContactResource($contact);
    }

    public function destroy($type, $parentId, $id)
    {
        $parent = $this->model->findOrFail($parentId);
        $contact = $parent->contacts()->findOrFail($id);

        $contact->delete();

        return response()->json(null, Response::HTTP_NO_CONTENT);
    }
}
