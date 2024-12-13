<?php

namespace App\Models\Document\Commercial;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class DocumentTemplate extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'name',
        'description',
        'document_type',
        'content',
        'fields',
        'default_values',
        'validation_rules',
        'is_active',
        'is_default',
        'metadata'
    ];

    protected $casts = [
        'fields' => 'array',
        'default_values' => 'array',
        'validation_rules' => 'array',
        'is_active' => 'boolean',
        'is_default' => 'boolean',
        'metadata' => 'array'
    ];

    /**
     * Obtener los documentos que usan esta plantilla basado en el tipo
     */
    public function documents()
    {
        switch ($this->document_type) {
            case 'invoice':
                return $this->hasMany(Invoice::class, 'template_id');
            case 'credit_note':
                return $this->hasMany(CreditNote::class, 'template_id');
            case 'debit_note':
                return $this->hasMany(DebitNote::class, 'template_id');
            case 'order':
                return $this->hasMany(Order::class, 'template_id');
            case 'quote':
                return $this->hasMany(Quote::class, 'template_id');
            default:
                return null;
        }
    }

    /**
     * Obtener campos requeridos
     */
    public function getRequiredFields(): array
    {
        return collect($this->fields)
            ->filter(function ($field) {
                return $field['required'] ?? false;
            })
            ->pluck('name')
            ->toArray();
    }

    /**
     * Validar datos contra la plantilla
     */
    public function validateData(array $data): array
    {
        $errors = [];

        foreach ($this->fields as $field) {
            $name = $field['name'];
            $required = $field['required'] ?? false;
            $type = $field['type'] ?? 'string';
            $validation = $field['validation'] ?? null;

            if ($required && !isset($data[$name])) {
                $errors[$name][] = "El campo {$name} es requerido";
                continue;
            }

            if (!isset($data[$name])) {
                continue;
            }

            if (!$this->validateFieldType($data[$name], $type)) {
                $errors[$name][] = "El campo {$name} debe ser de tipo {$type}";
            }

            if ($validation) {
                $validationErrors = $this->validateFieldRules($data[$name], $validation);
                if (!empty($validationErrors)) {
                    $errors[$name] = array_merge($errors[$name] ?? [], $validationErrors);
                }
            }
        }

        return $errors;
    }

    /**
     * Aplicar valores por defecto
     */
    public function applyDefaultValues(array $data): array
    {
        foreach ($this->default_values as $field => $value) {
            if (!isset($data[$field])) {
                $data[$field] = $value;
            }
        }

        return $data;
    }

    protected function validateFieldType($value, string $type): bool
    {
        switch ($type) {
            case 'string':
                return is_string($value);
            case 'number':
                return is_numeric($value);
            case 'boolean':
                return is_bool($value);
            case 'array':
                return is_array($value);
            case 'date':
                return strtotime($value) !== false;
            default:
                return true;
        }
    }

    protected function validateFieldRules($value, array $rules): array
    {
        $errors = [];

        foreach ($rules as $rule => $ruleValue) {
            switch ($rule) {
                case 'min':
                    if (is_string($value) && strlen($value) < $ruleValue) {
                        $errors[] = "Debe tener al menos {$ruleValue} caracteres";
                    } elseif (is_numeric($value) && $value < $ruleValue) {
                        $errors[] = "Debe ser mayor o igual a {$ruleValue}";
                    }
                    break;

                case 'max':
                    if (is_string($value) && strlen($value) > $ruleValue) {
                        $errors[] = "Debe tener máximo {$ruleValue} caracteres";
                    } elseif (is_numeric($value) && $value > $ruleValue) {
                        $errors[] = "Debe ser menor o igual a {$ruleValue}";
                    }
                    break;

                case 'pattern':
                    if (!preg_match($ruleValue, $value)) {
                        $errors[] = "Formato inválido";
                    }
                    break;

                case 'enum':
                    if (!in_array($value, $ruleValue)) {
                        $errors[] = "Valor no permitido";
                    }
                    break;
            }
        }

        return $errors;
    }
}
