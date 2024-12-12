<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\API\Auth\AuthController;
use App\Http\Controllers\API\Company\CompanyController;
use App\Http\Controllers\API\Branch\BranchController;
use App\Http\Controllers\API\User\UserController;
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
use App\Http\Controllers\API\Address\AddressController;
use App\Http\Controllers\API\Contact\ContactController;
use App\Http\Controllers\API\Document\Commercial\QuoteController;
use App\Http\Controllers\API\Document\Commercial\OrderController;
use App\Http\Controllers\API\Document\Commercial\InvoiceController;
use App\Http\Controllers\API\Document\Commercial\CreditNoteController;
use App\Http\Controllers\API\Document\Commercial\DebitNoteController;

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

// Auth Routes
Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);

// Protected Routes
Route::middleware(['auth:sanctum'])->group(function () {
    // Auth
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::get('/profile', [AuthController::class, 'profile']);
    
    // Companies
    Route::apiResource('companies', CompanyController::class);
    Route::get('companies/{company}/branches', [CompanyController::class, 'getBranches']);
    Route::get('companies/search/subdomain/{subdomain}', [CompanyController::class, 'findBySubdomain']);
    Route::get('companies/search/tax-id/{taxId}', [CompanyController::class, 'findByTaxId']);
    
    // Branches
    Route::apiResource('branches', BranchController::class);
    Route::get('companies/{company}/branches', [BranchController::class, 'getByCompany']);
    
    // Users
    Route::apiResource('users', UserController::class);
    Route::get('companies/{company}/users', [UserController::class, 'getByCompany']);
    Route::get('branches/{branch}/users', [UserController::class, 'getByBranch']);

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
            Route::get('states/active', [LocationController::class, 'activeStates']);
            Route::get('countries/{country}/states', [LocationController::class, 'statesByCountry']);

            // Cities
            Route::get('cities', [LocationController::class, 'indexCities']);
            Route::get('cities/{id}', [LocationController::class, 'showCity']);
            Route::get('cities/active', [LocationController::class, 'activeCities']);
            Route::get('states/{state}/cities', [LocationController::class, 'citiesByState']);
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
        Route::controller(MeasurementUnitController::class)->group(function () {
            Route::get('measurement-units', 'index');
            Route::post('measurement-units', 'store');
            Route::get('measurement-units/{id}', 'show');
            Route::put('measurement-units/{id}', 'update');
            Route::delete('measurement-units/{id}', 'destroy');
            Route::get('measurement-units/active', 'active');
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
    });
});
