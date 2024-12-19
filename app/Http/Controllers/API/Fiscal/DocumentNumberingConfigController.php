<?php

namespace App\Http\Controllers\Api\Fiscal;

use App\Http\Controllers\Controller;
use App\Models\Fiscal\DocumentNumberingConfig;
use App\Services\Fiscal\DocumentNumberingConfigService;
use Illuminate\Http\Request;

class DocumentNumberingConfigController extends Controller
{
    protected $service;

    public function __construct(DocumentNumberingConfigService $service)
    {
        $this->service = $service;
    }

    public function index(Request $request)
    {
        $filters = $request->only(['company_id', 'branch_id', 'document_type', 'is_active', 'per_page']);
        $configs = $this->service->getAll($filters);
        return response()->json($configs);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'company_id' => 'required|exists:companies,id',
            'branch_id' => 'required|exists:branches,id',
            'document_type' => 'required|string',
            'prefix' => 'nullable|string|max:10',
            'padding' => 'integer|min:1|max:20',
            'format' => 'nullable|string',
            'reset_yearly' => 'boolean',
            'is_active' => 'boolean'
        ]);

        $config = $this->service->create($validated);
        return response()->json($config, 201);
    }

    public function show(DocumentNumberingConfig $config)
    {
        return response()->json($config);
    }

    public function update(Request $request, DocumentNumberingConfig $config)
    {
        $validated = $request->validate([
            'prefix' => 'nullable|string|max:10',
            'padding' => 'integer|min:1|max:20',
            'format' => 'nullable|string',
            'reset_yearly' => 'boolean',
            'is_active' => 'boolean'
        ]);

        $config = $this->service->update($config, $validated);
        return response()->json($config);
    }

    public function generateNextNumber(DocumentNumberingConfig $config)
    {
        $result = $this->service->generateNextNumber($config);
        return response()->json($result);
    }
}
