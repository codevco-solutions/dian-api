<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\API\Auth\AuthController;
use App\Http\Controllers\API\Auth\UserController;
use App\Http\Controllers\API\Company\CompanyController;
use App\Http\Controllers\API\Branch\BranchController;
use App\Http\Controllers\API\MasterTable\LocationController;
use App\Http\Controllers\API\MasterTable\CurrencyController;
use App\Http\Controllers\API\MasterTable\IdentificationTypeController;
use App\Http\Controllers\API\MasterTable\OrganizationTypeController;
use App\Http\Controllers\API\MasterTable\TaxRegimeController;
use App\Http\Controllers\API\MasterTable\TaxResponsibilityController;
use App\Http\Controllers\API\MasterTable\OperationTypeController;
use App\Http\Controllers\API\MasterTable\DocumentTypeController;
use App\Http\Controllers\API\MasterTable\PaymentMeansController;
use App\Http\Controllers\API\MasterTable\PaymentMethodController;
use App\Http\Controllers\API\MasterTable\MeasurementUnitController;
use App\Http\Controllers\API\MasterTable\TaxController;
use App\Http\Controllers\API\MasterTable\ReferencePriceController;
use App\Http\Controllers\API\MasterTable\DiscountTypeController;
use App\Http\Controllers\API\MasterTable\ChargeTypeController;
use App\Http\Controllers\API\MasterTable\EventTypeController;
use App\Http\Controllers\API\Product\ProductCategoryController;
use App\Http\Controllers\API\Product\ProductController;
use App\Http\Controllers\API\Product\PriceListController;
use App\Http\Controllers\API\Customer\CustomerController;
use App\Http\Controllers\API\Supplier\SupplierController;
use App\Http\Controllers\API\Common\AddressController;
use App\Http\Controllers\API\Common\ContactController;
use App\Http\Controllers\API\Document\Commercial\QuoteController;
use App\Http\Controllers\API\Document\Commercial\OrderController;
use App\Http\Controllers\API\Document\Commercial\InvoiceController;
use App\Http\Controllers\API\Document\Commercial\CreditNoteController;
use App\Http\Controllers\API\Document\Commercial\DebitNoteController;
use App\Http\Controllers\API\Document\Commercial\PaymentReceiptController;
use App\Http\Controllers\API\Product\ProductInventoryController;
use App\Http\Controllers\API\Product\PriceHistoryController;
use App\Http\Controllers\API\Product\ProductTaxController;
use App\Http\Controllers\API\Category\CategoryController;
use App\Http\Controllers\API\Credit\CreditController;
use App\Http\Controllers\API\Customer\PaymentTermController;
use App\Http\Controllers\API\Customer\CustomerClassificationController;
use App\Http\Controllers\API\Customer\TransactionHistoryController;
use App\Http\Controllers\API\Customer\CustomerDocumentController;
use App\Http\Controllers\API\Document\DocumentStateController;
use App\Http\Controllers\API\Document\DocumentTemplateController;
use App\Http\Controllers\API\Document\DocumentAttachmentController;
use App\Http\Controllers\API\Document\DocumentConversionController;
use App\Http\Controllers\API\Document\DocumentChangeController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

// Rutas específicas para subdominios
Route::domain('{subdomain}.dian-api.test')->group(function () {
    Route::middleware(['auth:sanctum'])->group(function () {
        Route::get('/', function ($subdomain) {
            $company = App\Models\Company\Company::where('subdomain', $subdomain)->first();
            if (!$company) {
                return response()->json(['message' => 'Company not found'], 404);
            }
            return response()->json([
                'message' => 'Company retrieved successfully',
                'data' => new App\Http\Resources\Company\CompanyResource($company)
            ]);
        });
    });
});

// Rutas de Autenticación
Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);

// Rutas Protegidas
Route::middleware(['auth:sanctum'])->group(function () {
    // Información del usuario autenticado
    Route::get('/me', [AuthController::class, 'me']);
    Route::post('/logout', [AuthController::class, 'logout']);
    
    // Gestión de Usuarios
    Route::prefix('users')->group(function () {
        Route::get('/', [UserController::class, 'index']);
        Route::post('/', [UserController::class, 'store']);
        Route::get('/{id}', [UserController::class, 'show']);
        Route::put('/{id}', [UserController::class, 'update']);
        Route::delete('/{id}', [UserController::class, 'destroy']);
    });
    
    // Usuarios por Compañía y Sucursal
    Route::get('companies/{company}/users', [UserController::class, 'getByCompany']);
    Route::get('branches/{branch}/users', [UserController::class, 'getByBranch']);

    // Companies
    Route::apiResource('companies', CompanyController::class);
    Route::get('companies/{company}/branches', [CompanyController::class, 'getBranches']);
    Route::get('companies/search/subdomain/{subdomain}', [CompanyController::class, 'findBySubdomain']);
    Route::get('companies/search/tax-id/{taxId}', [CompanyController::class, 'findByTaxId']);
    
    // Branches
    Route::apiResource('branches', BranchController::class);
    Route::get('companies/{company}/branches', [BranchController::class, 'getByCompany']);
    
    // Master Tables
    Route::prefix('master')->group(function () {
        // Locations
        Route::prefix('locations')->group(function () {
            // Countries
            Route::get('countries', [LocationController::class, 'indexCountries']);
            Route::get('countries/{id}', [LocationController::class, 'showCountry']);
            Route::get('countries/active', [LocationController::class, 'activeCountries']);

            // States
            Route::get('states', [LocationController::class, 'indexStates']);
            Route::get('states/{id}', [LocationController::class, 'showState']);
            Route::get('countries/{country}/states', [LocationController::class, 'statesByCountry']);
            Route::get('states/active', [LocationController::class, 'activeStates']);

            // Cities
            Route::get('cities', [LocationController::class, 'indexCities']);
            Route::get('cities/{id}', [LocationController::class, 'showCity']);
            Route::get('states/{state}/cities', [LocationController::class, 'citiesByState']);
            Route::get('cities/active', [LocationController::class, 'activeCities']);
        });

        // Currencies
        Route::controller(CurrencyController::class)->group(function () {
            Route::get('currencies', 'index');
            Route::post('currencies', 'store');
            Route::get('currencies/{id}', 'show');
            Route::put('currencies/{id}', 'update');
            Route::delete('currencies/{id}', 'destroy');
            Route::get('currencies/active', 'active');
        });

        // Identification Types
        Route::controller(IdentificationTypeController::class)->group(function () {
            Route::get('identification-types', 'index');
            Route::post('identification-types', 'store');
            Route::get('identification-types/{id}', 'show');
            Route::put('identification-types/{id}', 'update');
            Route::delete('identification-types/{id}', 'destroy');
            Route::get('identification-types/active', 'active');
        });

        // Organization Types
        Route::controller(OrganizationTypeController::class)->group(function () {
            Route::get('organization-types', 'index');
            Route::post('organization-types', 'store');
            Route::get('organization-types/{id}', 'show');
            Route::put('organization-types/{id}', 'update');
            Route::delete('organization-types/{id}', 'destroy');
            Route::get('organization-types/active', 'active');
        });

        // Tax Regimes
        Route::controller(TaxRegimeController::class)->group(function () {
            Route::get('tax-regimes', 'index');
            Route::post('tax-regimes', 'store');
            Route::get('tax-regimes/{id}', 'show');
            Route::put('tax-regimes/{id}', 'update');
            Route::delete('tax-regimes/{id}', 'destroy');
            Route::get('tax-regimes/active', 'active');
        });

        // Tax Responsibilities
        Route::controller(TaxResponsibilityController::class)->group(function () {
            Route::get('tax-responsibilities', 'index');
            Route::post('tax-responsibilities', 'store');
            Route::get('tax-responsibilities/{id}', 'show');
            Route::put('tax-responsibilities/{id}', 'update');
            Route::delete('tax-responsibilities/{id}', 'destroy');
            Route::get('tax-responsibilities/active', 'active');
        });

        // Operation Types
        Route::controller(OperationTypeController::class)->group(function () {
            Route::get('operation-types', 'index');
            Route::post('operation-types', 'store');
            Route::get('operation-types/{id}', 'show');
            Route::put('operation-types/{id}', 'update');
            Route::delete('operation-types/{id}', 'destroy');
            Route::get('operation-types/active', 'active');
        });

        // Document Types
        Route::controller(DocumentTypeController::class)->group(function () {
            Route::get('document-types', 'index');
            Route::post('document-types', 'store');
            Route::get('document-types/{id}', 'show');
            Route::put('document-types/{id}', 'update');
            Route::delete('document-types/{id}', 'destroy');
            Route::get('document-types/active', 'active');
        });

        // Payment Means
        Route::controller(PaymentMeansController::class)->group(function () {
            Route::get('payment-means', 'index');
            Route::post('payment-means', 'store');
            Route::get('payment-means/{id}', 'show');
            Route::put('payment-means/{id}', 'update');
            Route::delete('payment-means/{id}', 'destroy');
            Route::get('payment-means/active', 'active');
        });

        // Payment Methods
        Route::controller(PaymentMethodController::class)->group(function () {
            Route::get('payment-methods', 'index');
            Route::post('payment-methods', 'store');
            Route::get('payment-methods/{id}', 'show');
            Route::put('payment-methods/{id}', 'update');
            Route::delete('payment-methods/{id}', 'destroy');
            Route::get('payment-methods/active', 'active');
        });

        // Measurement Units
        Route::prefix('measurement-units')->group(function () {
            Route::get('base', [MeasurementUnitController::class, 'getBaseUnits']);
            Route::get('composite', [MeasurementUnitController::class, 'getCompositeUnits']);
            Route::post('composite', [MeasurementUnitController::class, 'createCompositeUnit']);
            Route::put('composite/{unit}', [MeasurementUnitController::class, 'updateCompositeUnit']);
            Route::post('conversions', [MeasurementUnitController::class, 'addUnitConversion']);
            Route::put('conversions/{conversion}', [MeasurementUnitController::class, 'updateUnitConversion']);
            Route::delete('conversions/{conversion}', [MeasurementUnitController::class, 'removeUnitConversion']);
            Route::post('convert', [MeasurementUnitController::class, 'convertValue']);
            Route::get('{unit}/conversions', [MeasurementUnitController::class, 'getUnitConversions']);
            Route::get('{unit}/components', [MeasurementUnitController::class, 'getUnitComponents']);
            Route::post('{unit}/calculate', [MeasurementUnitController::class, 'calculateCompositeValue']);
            Route::post('validate-conversion', [MeasurementUnitController::class, 'validateConversion']);
        });

        // Taxes
        Route::controller(TaxController::class)->group(function () {
            Route::get('taxes', 'index');
            Route::post('taxes', 'store');
            Route::get('taxes/{id}', 'show');
            Route::put('taxes/{id}', 'update');
            Route::delete('taxes/{id}', 'destroy');
            Route::get('taxes/active', 'active');
        });

        // Reference Prices
        Route::controller(ReferencePriceController::class)->group(function () {
            Route::get('reference-prices', 'index');
            Route::post('reference-prices', 'store');
            Route::get('reference-prices/{id}', 'show');
            Route::put('reference-prices/{id}', 'update');
            Route::delete('reference-prices/{id}', 'destroy');
            Route::get('reference-prices/active', 'active');
        });

        // Discount Types
        Route::controller(DiscountTypeController::class)->group(function () {
            Route::get('discount-types', 'index');
            Route::post('discount-types', 'store');
            Route::get('discount-types/{id}', 'show');
            Route::put('discount-types/{id}', 'update');
            Route::delete('discount-types/{id}', 'destroy');
            Route::get('discount-types/active', 'active');
        });

        // Charge Types
        Route::controller(ChargeTypeController::class)->group(function () {
            Route::get('charge-types', 'index');
            Route::post('charge-types', 'store');
            Route::get('charge-types/{id}', 'show');
            Route::put('charge-types/{id}', 'update');
            Route::delete('charge-types/{id}', 'destroy');
            Route::get('charge-types/active', 'active');
        });

        // Event Types
        Route::controller(EventTypeController::class)->group(function () {
            Route::get('event-types', 'index');
            Route::post('event-types', 'store');
            Route::get('event-types/{id}', 'show');
            Route::put('event-types/{id}', 'update');
            Route::delete('event-types/{id}', 'destroy');
            Route::get('event-types/active', 'active');
        });

        // Product Categories
        Route::controller(ProductCategoryController::class)->group(function () {
            Route::get('product-categories', 'index');
            Route::post('product-categories', 'store');
            Route::get('product-categories/{id}', 'show');
            Route::put('product-categories/{id}', 'update');
            Route::delete('product-categories/{id}', 'destroy');
            Route::get('product-categories/active', 'active');
        });

        // Products
        Route::controller(ProductController::class)->group(function () {
            Route::get('products', 'index');
            Route::post('products', 'store');
            Route::get('products/{id}', 'show');
            Route::put('products/{id}', 'update');
            Route::delete('products/{id}', 'destroy');
            Route::get('products/active', 'active');
        });

        // Price Lists
        Route::controller(PriceListController::class)->group(function () {
            Route::get('price-lists', 'index');
            Route::post('price-lists', 'store');
            Route::get('price-lists/{id}', 'show');
            Route::put('price-lists/{id}', 'update');
            Route::delete('price-lists/{id}', 'destroy');
            Route::get('price-lists/active', 'active');
        });

        // Price History
        Route::prefix('price-history')->group(function () {
            Route::get('products/{product}', [PriceHistoryController::class, 'getProductHistory']);
            Route::post('price-lists/{priceList}', [PriceHistoryController::class, 'recordPriceChange']);
            Route::get('price-lists/{priceList}/evolution', [PriceHistoryController::class, 'getPriceEvolution']);
            Route::get('price-lists/{priceList}/trends', [PriceHistoryController::class, 'getPriceTrends']);
        });

        // Customers
        Route::controller(CustomerController::class)->group(function () {
            Route::get('customers', 'index');
            Route::post('customers', 'store');
            Route::get('customers/{id}', 'show');
            Route::put('customers/{id}', 'update');
            Route::delete('customers/{id}', 'destroy');
            Route::get('customers/active', 'active');
        });

        // Suppliers
        Route::controller(SupplierController::class)->group(function () {
            Route::get('suppliers', 'index');
            Route::post('suppliers', 'store');
            Route::get('suppliers/{id}', 'show');
            Route::put('suppliers/{id}', 'update');
            Route::delete('suppliers/{id}', 'destroy');
            Route::get('suppliers/active', 'active');
        });

        // Addresses
        Route::controller(AddressController::class)->group(function () {
            Route::get('{type}/{id}/addresses', 'index');
            Route::post('{type}/{id}/addresses', 'store');
            Route::get('{type}/{id}/addresses/{addressId}', 'show');
            Route::put('{type}/{id}/addresses/{addressId}', 'update');
            Route::delete('{type}/{id}/addresses/{addressId}', 'destroy');
        });

        // Contacts
        Route::controller(ContactController::class)->group(function () {
            Route::get('{type}/{id}/contacts', 'index');
            Route::post('{type}/{id}/contacts', 'store');
            Route::get('{type}/{id}/contacts/{contactId}', 'show');
            Route::put('{type}/{id}/contacts/{contactId}', 'update');
            Route::delete('{type}/{id}/contacts/{contactId}', 'destroy');
        });
    });

    // Category Routes
    Route::prefix('categories')->group(function () {
        Route::post('/', [CategoryController::class, 'store']);
        Route::put('{category}', [CategoryController::class, 'update']);
        Route::delete('{category}', [CategoryController::class, 'destroy']);
        Route::get('{category}/hierarchy', [CategoryController::class, 'getHierarchy']);
        Route::get('{category}/attributes', [CategoryController::class, 'getAttributes']);
        Route::post('{category}/attributes', [CategoryController::class, 'addAttribute']);
        Route::put('{category}/attributes/{attribute}', [CategoryController::class, 'updateAttribute']);
        Route::delete('{category}/attributes/{attribute}', [CategoryController::class, 'removeAttribute']);
        Route::get('{category}/products', [CategoryController::class, 'getProducts']);
        Route::get('{category}/products/{product}/attributes', [CategoryController::class, 'getProductAttributes']);
        Route::post('{category}/products/{product}/attributes', [CategoryController::class, 'setProductAttributes']);
        Route::post('{category}/move', [CategoryController::class, 'moveCategory']);
        Route::post('{category}/reorder', [CategoryController::class, 'reorderCategories']);
        Route::get('tree', [CategoryController::class, 'getCategoryTree']);
        Route::get('{category}/validate', [CategoryController::class, 'validateStructure']);
    });

    // Product Tax Routes
    Route::prefix('products/{product}/taxes')->group(function () {
        Route::get('/', [ProductTaxController::class, 'getProductTaxes']);
        Route::post('/', [ProductTaxController::class, 'assignTax']);
        Route::put('{tax}', [ProductTaxController::class, 'updateTax']);
        Route::delete('{tax}', [ProductTaxController::class, 'removeTax']);
        Route::get('history', [ProductTaxController::class, 'getTaxHistory']);
        Route::post('calculate', [ProductTaxController::class, 'calculateTaxes']);
        Route::get('summary', [ProductTaxController::class, 'getTaxSummary']);
    });

    // Product Inventory Routes
    Route::prefix('products/{product}/inventory')->group(function () {
        Route::get('branches/{branch}', [ProductInventoryController::class, 'getInventory']);
        Route::put('branches/{branch}/stock', [ProductInventoryController::class, 'updateStock']);
        Route::post('branches/{branch}/add', [ProductInventoryController::class, 'addStock']);
        Route::post('branches/{branch}/remove', [ProductInventoryController::class, 'removeStock']);
        Route::post('transfer', [ProductInventoryController::class, 'transferStock']);
        Route::get('branches/{branch}/movements', [ProductInventoryController::class, 'getMovements']);
    });

    Route::get('branches/{branch}/stock-alerts', [ProductInventoryController::class, 'getStockAlerts']);

    // Customer Credit Routes
    Route::prefix('customers/{customer}/credit')->group(function () {
        Route::get('/', [CreditController::class, 'getCustomerCredit']);
        Route::put('limit', [CreditController::class, 'updateCreditLimit']);
        Route::post('movements', [CreditController::class, 'addCreditMovement']);
        Route::get('movements', [CreditController::class, 'getCreditMovements']);
        Route::get('status', [CreditController::class, 'getCreditStatus']);
        Route::get('overdue', [CreditController::class, 'getOverduePayments']);
        Route::get('metrics', [CreditController::class, 'getCreditMetrics']);
        Route::get('risk', [CreditController::class, 'evaluateCreditRisk']);
        Route::get('report', [CreditController::class, 'generateCreditReport']);
    });

    Route::put('credit-movements/{movement}/status', [CreditController::class, 'updatePaymentStatus']);

    // Customer Payment Terms Routes
    Route::prefix('customers/{customer}/payment-terms')->group(function () {
        Route::get('/', [PaymentTermController::class, 'index']);
        Route::post('/', [PaymentTermController::class, 'store']);
        Route::put('{term}', [PaymentTermController::class, 'update']);
        Route::delete('{term}', [PaymentTermController::class, 'destroy']);
        Route::put('{term}/default', [PaymentTermController::class, 'setDefault']);
        Route::post('{term}/calculate-discount', [PaymentTermController::class, 'calculateDiscount']);
    });

    // Customer Classifications Routes
    Route::prefix('customer-classifications')->group(function () {
        Route::get('/', [CustomerClassificationController::class, 'index']);
        Route::post('/', [CustomerClassificationController::class, 'store']);
        Route::put('{classification}', [CustomerClassificationController::class, 'update']);
        Route::delete('{classification}', [CustomerClassificationController::class, 'destroy']);
        Route::get('{classification}/metrics', [CustomerClassificationController::class, 'getMetrics']);
        Route::post('evaluate-all', [CustomerClassificationController::class, 'evaluateAllCustomers']);
    });

    Route::prefix('customers/{customer}')->group(function () {
        Route::post('classification', [CustomerClassificationController::class, 'assignToCustomer']);
        Route::post('evaluate-classification', [CustomerClassificationController::class, 'evaluateCustomer']);
    });

    // Customer Transaction History Routes
    Route::prefix('customers/{customer}/transactions')->group(function () {
        Route::get('/', [TransactionHistoryController::class, 'index']);
        Route::post('/', [TransactionHistoryController::class, 'store']);
        Route::get('summary', [TransactionHistoryController::class, 'getSummary']);
        Route::get('overdue', [TransactionHistoryController::class, 'getOverdue']);
        Route::get('metrics', [TransactionHistoryController::class, 'getMetrics']);
        Route::get('balance', [TransactionHistoryController::class, 'getCurrentBalance']);
        Route::get('report', [TransactionHistoryController::class, 'generateReport']);
    });

    Route::put('transactions/{transaction}/status', [TransactionHistoryController::class, 'updateStatus']);

    // Customer Documents Routes
    Route::prefix('customers/{customer}/documents')->group(function () {
        Route::get('/', [CustomerDocumentController::class, 'index']);
        Route::post('/', [CustomerDocumentController::class, 'store']);
        Route::put('{document}', [CustomerDocumentController::class, 'update']);
        Route::post('{document}/file', [CustomerDocumentController::class, 'updateFile']);
        Route::delete('{document}', [CustomerDocumentController::class, 'destroy']);
        Route::get('type/{type}', [CustomerDocumentController::class, 'getByType']);
        Route::get('expired', [CustomerDocumentController::class, 'getExpired']);
        Route::get('about-to-expire', [CustomerDocumentController::class, 'getAboutToExpire']);
        Route::get('missing-required', [CustomerDocumentController::class, 'getMissingRequired']);
        Route::get('status', [CustomerDocumentController::class, 'checkStatus']);
        Route::get('report', [CustomerDocumentController::class, 'generateReport']);
    });

    // Document States
    Route::prefix('documents/states')->group(function () {
        Route::get('/', [DocumentStateController::class, 'index']);
        Route::post('/', [DocumentStateController::class, 'store']);
        Route::put('/{id}', [DocumentStateController::class, 'update']);
        Route::delete('/{id}', [DocumentStateController::class, 'destroy']);
        Route::get('/type/{documentType}', [DocumentStateController::class, 'getAvailableStates']);
        Route::get('/{stateId}/transitions', [DocumentStateController::class, 'getAvailableTransitions']);
        Route::post('/transition', [DocumentStateController::class, 'transition']);
    });

    // Document Templates
    Route::prefix('documents/templates')->group(function () {
        Route::get('/', [DocumentTemplateController::class, 'index']);
        Route::post('/', [DocumentTemplateController::class, 'store']);
        Route::put('/{id}', [DocumentTemplateController::class, 'update']);
        Route::delete('/{id}', [DocumentTemplateController::class, 'destroy']);
        Route::get('/type/{documentType}/default', [DocumentTemplateController::class, 'getDefaultTemplate']);
        Route::post('/{id}/default', [DocumentTemplateController::class, 'setDefaultTemplate']);
        Route::post('/{id}/duplicate', [DocumentTemplateController::class, 'duplicate']);
        Route::post('/{id}/validate', [DocumentTemplateController::class, 'validateData']);
        Route::post('/{id}/toggle-active', [DocumentTemplateController::class, 'toggleActive']);
    });

    // Document Attachments
    Route::prefix('documents/attachments')->group(function () {
        Route::post('/upload', [DocumentAttachmentController::class, 'upload']);
        Route::delete('/{id}', [DocumentAttachmentController::class, 'destroy']);
        Route::get('/document/{documentType}/{documentId}', [DocumentAttachmentController::class, 'getDocumentAttachments']);
        Route::put('/{id}/metadata', [DocumentAttachmentController::class, 'updateMetadata']);
        Route::get('/document/{documentType}/{documentId}/by-type', [DocumentAttachmentController::class, 'getByFileType']);
        Route::get('/user/{userId}', [DocumentAttachmentController::class, 'getByUser']);
    });

    // Document Conversion Routes
    Route::prefix('documents')->group(function () {
        Route::post('{sourceType}/{documentId}/convert', [DocumentConversionController::class, 'convert']);
        Route::get('{sourceType}/{documentId}/allowed-conversions', [DocumentConversionController::class, 'getAllowedConversions']);
        Route::get('{sourceType}/{documentId}/related-conversions', [DocumentConversionController::class, 'getRelatedConversions']);
    });

    // Document Change History Routes
    Route::prefix('documents')->group(function () {
        Route::get('{documentType}/{documentId}/history', [DocumentChangeController::class, 'getHistory']);
        Route::get('changes/{changeId}', [DocumentChangeController::class, 'getChangeDetails']);
        Route::get('{documentType}/{documentId}/changes-summary', [DocumentChangeController::class, 'getChangeSummary']);
    });

    // Document Commercial Routes
    Route::prefix('documents/commercial')->group(function () {
        // Quotes
        Route::apiResource('quotes', QuoteController::class);
        Route::post('quotes/{quote}/approve', [QuoteController::class, 'approve']);
        Route::post('quotes/{quote}/reject', [QuoteController::class, 'reject']);
        Route::post('quotes/{quote}/create-order', [QuoteController::class, 'createOrder']);

        // Orders
        Route::apiResource('orders', OrderController::class);
        Route::post('orders/{order}/approve', [OrderController::class, 'approve']);
        Route::post('orders/{order}/reject', [OrderController::class, 'reject']);
        Route::post('orders/{order}/create-invoice', [OrderController::class, 'createInvoice']);

        // Invoices
        Route::apiResource('invoices', InvoiceController::class);
        Route::post('invoices/{invoice}/approve', [InvoiceController::class, 'approve']);
        Route::post('invoices/{invoice}/cancel', [InvoiceController::class, 'cancel']);
        Route::post('invoices/{invoice}/credit-notes', [InvoiceController::class, 'createCreditNote']);
        Route::post('invoices/{invoice}/debit-notes', [InvoiceController::class, 'createDebitNote']);
        Route::get('invoices/{invoice}/pdf', [InvoiceController::class, 'downloadPdf']);
        Route::get('invoices/{invoice}/xml', [InvoiceController::class, 'downloadXml']);

        // Credit Notes
        Route::apiResource('credit-notes', CreditNoteController::class);
        Route::post('credit-notes/{creditNote}/approve', [CreditNoteController::class, 'approve']);
        Route::post('credit-notes/{creditNote}/cancel', [CreditNoteController::class, 'cancel']);

        // Debit Notes
        Route::apiResource('debit-notes', DebitNoteController::class);
        Route::post('debit-notes/{debitNote}/approve', [DebitNoteController::class, 'approve']);
        Route::post('debit-notes/{debitNote}/cancel', [DebitNoteController::class, 'cancel']);

        // Payment Receipts
        Route::apiResource('payment-receipts', PaymentReceiptController::class);
        Route::post('payment-receipts/{paymentReceipt}/approve', [PaymentReceiptController::class, 'approve']);
        Route::post('payment-receipts/{paymentReceipt}/cancel', [PaymentReceiptController::class, 'cancel']);
    });
});
