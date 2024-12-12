<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

// Interfaces
use App\Repositories\Contracts\Company\CompanyRepositoryInterface;
use App\Repositories\Contracts\Branch\BranchRepositoryInterface;
use App\Repositories\Contracts\User\UserRepositoryInterface;
use App\Repositories\Contracts\MasterTable\MasterTableRepositoryInterface;
use App\Repositories\Contracts\MasterTable\LocationRepositoryInterface;
use App\Repositories\Contracts\Customer\CustomerRepositoryInterface;
use App\Repositories\Contracts\Product\ProductRepositoryInterface;
use App\Repositories\Contracts\Supplier\SupplierRepositoryInterface;
use App\Repositories\Contracts\Document\DocumentRepositoryInterface;
use App\Repositories\Contracts\Common\AddressRepositoryInterface;
use App\Repositories\Contracts\Common\ContactRepositoryInterface;
use App\Repositories\Contracts\Product\PriceListRepositoryInterface;
use App\Repositories\Contracts\Product\ProductCategoryRepositoryInterface;
use App\Repositories\Contracts\Location\CityRepositoryInterface;
use App\Repositories\Contracts\Location\StateRepositoryInterface;
use App\Repositories\Contracts\Location\CountryRepositoryInterface;
use App\Repositories\Contracts\Auth\RoleRepositoryInterface;
use App\Repositories\Contracts\AllowanceChargeRepositoryInterface;
use App\Repositories\Contracts\PaymentMeansRepositoryInterface;
use App\Repositories\Contracts\DocumentReferenceRepositoryInterface;

// Master Table Interfaces
use App\Repositories\Contracts\MasterTable\ChargeTypeRepositoryInterface;
use App\Repositories\Contracts\MasterTable\CurrencyRepositoryInterface;
use App\Repositories\Contracts\MasterTable\DiscountTypeRepositoryInterface;
use App\Repositories\Contracts\MasterTable\DocumentTypeRepositoryInterface;
use App\Repositories\Contracts\MasterTable\EventTypeRepositoryInterface;
use App\Repositories\Contracts\MasterTable\IdentificationTypeRepositoryInterface;
use App\Repositories\Contracts\MasterTable\MeasurementUnitRepositoryInterface;
use App\Repositories\Contracts\MasterTable\OperationTypeRepositoryInterface;
use App\Repositories\Contracts\MasterTable\OrganizationTypeRepositoryInterface;
use App\Repositories\Contracts\MasterTable\PaymentMethodRepositoryInterface;
use App\Repositories\Contracts\MasterTable\ReferencePriceRepositoryInterface;
use App\Repositories\Contracts\MasterTable\TaxRepositoryInterface;
use App\Repositories\Contracts\MasterTable\TaxRegimeRepositoryInterface;
use App\Repositories\Contracts\MasterTable\TaxResponsibilityRepositoryInterface;

// Payroll Interfaces
use App\Repositories\Contracts\Payroll\EmployeeRepositoryInterface;
use App\Repositories\Contracts\Payroll\EmployeeContractRepositoryInterface;
use App\Repositories\Contracts\Payroll\PayrollAdjustmentRepositoryInterface;
use App\Repositories\Contracts\Payroll\PayrollDeductionRepositoryInterface;
use App\Repositories\Contracts\Payroll\PayrollDocumentRepositoryInterface;
use App\Repositories\Contracts\Payroll\PayrollEarningRepositoryInterface;
use App\Repositories\Contracts\Payroll\PayrollPeriodRepositoryInterface;

// Document Interfaces
use App\Repositories\Contracts\Document\DianLogRepositoryInterface;
use App\Repositories\Contracts\Document\DigitalCertificateRepositoryInterface;
use App\Repositories\Contracts\Document\DocLogRepositoryInterface;
use App\Repositories\Contracts\Document\DocResolutionRepositoryInterface;
use App\Repositories\Contracts\Document\DocSequenceRepositoryInterface;
use App\Repositories\Contracts\Document\DocTemplateRepositoryInterface;
use App\Repositories\Contracts\Document\ErrorLogRepositoryInterface;

// Document Commercial Interfaces
use App\Repositories\Contracts\Document\Commercial\CreditNoteRepositoryInterface;
use App\Repositories\Contracts\Document\Commercial\DebitNoteRepositoryInterface;
use App\Repositories\Contracts\Document\Commercial\InvoiceRepositoryInterface;
use App\Repositories\Contracts\Document\Commercial\OrderRepositoryInterface;
use App\Repositories\Contracts\Document\Commercial\PaymentReceiptRepositoryInterface;
use App\Repositories\Contracts\Document\Commercial\QuoteRepositoryInterface;

// Repositories
use App\Repositories\Eloquent\Company\CompanyRepository;
use App\Repositories\Eloquent\Branch\BranchRepository;
use App\Repositories\Eloquent\User\UserRepository;
use App\Repositories\Eloquent\MasterTable\MasterTableRepository;
use App\Repositories\Eloquent\MasterTable\LocationRepository;
use App\Repositories\Eloquent\Customer\CustomerRepository;
use App\Repositories\Eloquent\Product\ProductRepository;
use App\Repositories\Eloquent\Supplier\SupplierRepository;
use App\Repositories\Eloquent\Document\DocumentRepository;
use App\Repositories\Eloquent\Common\AddressRepository;
use App\Repositories\Eloquent\Common\ContactRepository;
use App\Repositories\Eloquent\Product\PriceListRepository;
use App\Repositories\Eloquent\Product\ProductCategoryRepository;
use App\Repositories\Eloquent\Location\CityRepository;
use App\Repositories\Eloquent\Location\StateRepository;
use App\Repositories\Eloquent\Location\CountryRepository;
use App\Repositories\Eloquent\Auth\RoleRepository;
use App\Repositories\Eloquent\AllowanceChargeRepository;
use App\Repositories\Eloquent\PaymentMeansRepository;
use App\Repositories\Eloquent\DocumentReferenceRepository;

// Master Table Repositories
use App\Repositories\Eloquent\MasterTable\ChargeTypeRepository;
use App\Repositories\Eloquent\MasterTable\CurrencyRepository;
use App\Repositories\Eloquent\MasterTable\DiscountTypeRepository;
use App\Repositories\Eloquent\MasterTable\DocumentTypeRepository;
use App\Repositories\Eloquent\MasterTable\EventTypeRepository;
use App\Repositories\Eloquent\MasterTable\IdentificationTypeRepository;
use App\Repositories\Eloquent\MasterTable\MeasurementUnitRepository;
use App\Repositories\Eloquent\MasterTable\OperationTypeRepository;
use App\Repositories\Eloquent\MasterTable\OrganizationTypeRepository;
use App\Repositories\Eloquent\MasterTable\PaymentMethodRepository;
use App\Repositories\Eloquent\MasterTable\ReferencePriceRepository;
use App\Repositories\Eloquent\MasterTable\TaxRepository;
use App\Repositories\Eloquent\MasterTable\TaxRegimeRepository;
use App\Repositories\Eloquent\MasterTable\TaxResponsibilityRepository;

// Payroll Repositories
use App\Repositories\Eloquent\Payroll\EmployeeRepository;
use App\Repositories\Eloquent\Payroll\EmployeeContractRepository;
use App\Repositories\Eloquent\Payroll\PayrollAdjustmentRepository;
use App\Repositories\Eloquent\Payroll\PayrollDeductionRepository;
use App\Repositories\Eloquent\Payroll\PayrollDocumentRepository;
use App\Repositories\Eloquent\Payroll\PayrollEarningRepository;
use App\Repositories\Eloquent\Payroll\PayrollPeriodRepository;

// Document Repositories
use App\Repositories\Eloquent\Document\DianLogRepository;
use App\Repositories\Eloquent\Document\DigitalCertificateRepository;
use App\Repositories\Eloquent\Document\DocLogRepository;
use App\Repositories\Eloquent\Document\DocResolutionRepository;
use App\Repositories\Eloquent\Document\DocSequenceRepository;
use App\Repositories\Eloquent\Document\DocTemplateRepository;
use App\Repositories\Eloquent\Document\ErrorLogRepository;

// Document Commercial Repositories
use App\Repositories\Eloquent\Document\Commercial\CreditNoteRepository;
use App\Repositories\Eloquent\Document\Commercial\DebitNoteRepository;
use App\Repositories\Eloquent\Document\Commercial\InvoiceRepository;
use App\Repositories\Eloquent\Document\Commercial\OrderRepository;
use App\Repositories\Eloquent\Document\Commercial\PaymentReceiptRepository;
use App\Repositories\Eloquent\Document\Commercial\QuoteRepository;

class RepositoryServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        // Main Bindings
        $this->app->bind(CompanyRepositoryInterface::class, CompanyRepository::class);
        $this->app->bind(BranchRepositoryInterface::class, BranchRepository::class);
        $this->app->bind(UserRepositoryInterface::class, UserRepository::class);
        $this->app->bind(MasterTableRepositoryInterface::class, MasterTableRepository::class);
        $this->app->bind(LocationRepositoryInterface::class, LocationRepository::class);
        $this->app->bind(CustomerRepositoryInterface::class, CustomerRepository::class);
        $this->app->bind(ProductRepositoryInterface::class, ProductRepository::class);
        $this->app->bind(SupplierRepositoryInterface::class, SupplierRepository::class);
        $this->app->bind(DocumentRepositoryInterface::class, DocumentRepository::class);
        $this->app->bind(AddressRepositoryInterface::class, AddressRepository::class);
        $this->app->bind(ContactRepositoryInterface::class, ContactRepository::class);
        $this->app->bind(PriceListRepositoryInterface::class, PriceListRepository::class);
        $this->app->bind(ProductCategoryRepositoryInterface::class, ProductCategoryRepository::class);

        // Master Table Bindings
        $this->app->bind(ChargeTypeRepositoryInterface::class, ChargeTypeRepository::class);
        $this->app->bind(CurrencyRepositoryInterface::class, CurrencyRepository::class);
        $this->app->bind(DiscountTypeRepositoryInterface::class, DiscountTypeRepository::class);
        $this->app->bind(DocumentTypeRepositoryInterface::class, DocumentTypeRepository::class);
        $this->app->bind(EventTypeRepositoryInterface::class, EventTypeRepository::class);
        $this->app->bind(IdentificationTypeRepositoryInterface::class, IdentificationTypeRepository::class);
        $this->app->bind(MeasurementUnitRepositoryInterface::class, MeasurementUnitRepository::class);
        $this->app->bind(OperationTypeRepositoryInterface::class, OperationTypeRepository::class);
        $this->app->bind(OrganizationTypeRepositoryInterface::class, OrganizationTypeRepository::class);
        $this->app->bind(PaymentMeansRepositoryInterface::class, PaymentMeansRepository::class);
        $this->app->bind(PaymentMethodRepositoryInterface::class, PaymentMethodRepository::class);
        $this->app->bind(ReferencePriceRepositoryInterface::class, ReferencePriceRepository::class);
        $this->app->bind(TaxRepositoryInterface::class, TaxRepository::class);
        $this->app->bind(TaxRegimeRepositoryInterface::class, TaxRegimeRepository::class);
        $this->app->bind(TaxResponsibilityRepositoryInterface::class, TaxResponsibilityRepository::class);

        // Payroll Bindings
        $this->app->bind(EmployeeRepositoryInterface::class, EmployeeRepository::class);
        $this->app->bind(EmployeeContractRepositoryInterface::class, EmployeeContractRepository::class);
        $this->app->bind(PayrollAdjustmentRepositoryInterface::class, PayrollAdjustmentRepository::class);
        $this->app->bind(PayrollDeductionRepositoryInterface::class, PayrollDeductionRepository::class);
        $this->app->bind(PayrollDocumentRepositoryInterface::class, PayrollDocumentRepository::class);
        $this->app->bind(PayrollEarningRepositoryInterface::class, PayrollEarningRepository::class);
        $this->app->bind(PayrollPeriodRepositoryInterface::class, PayrollPeriodRepository::class);

        // Document Bindings
        $this->app->bind(DianLogRepositoryInterface::class, DianLogRepository::class);
        $this->app->bind(DigitalCertificateRepositoryInterface::class, DigitalCertificateRepository::class);
        $this->app->bind(DocLogRepositoryInterface::class, DocLogRepository::class);
        $this->app->bind(DocResolutionRepositoryInterface::class, DocResolutionRepository::class);
        $this->app->bind(DocSequenceRepositoryInterface::class, DocSequenceRepository::class);
        $this->app->bind(DocTemplateRepositoryInterface::class, DocTemplateRepository::class);
        $this->app->bind(ErrorLogRepositoryInterface::class, ErrorLogRepository::class);

        // Document Commercial Bindings
        $this->app->bind(CreditNoteRepositoryInterface::class, CreditNoteRepository::class);
        $this->app->bind(DebitNoteRepositoryInterface::class, DebitNoteRepository::class);
        $this->app->bind(InvoiceRepositoryInterface::class, InvoiceRepository::class);
        $this->app->bind(OrderRepositoryInterface::class, OrderRepository::class);
        $this->app->bind(PaymentReceiptRepositoryInterface::class, PaymentReceiptRepository::class);
        $this->app->bind(QuoteRepositoryInterface::class, QuoteRepository::class);

        // Nuevos bindings para documentos comerciales
        $this->app->bind(
            AllowanceChargeRepositoryInterface::class,
            AllowanceChargeRepository::class
        );

        $this->app->bind(
            PaymentMeansRepositoryInterface::class,
            PaymentMeansRepository::class
        );

        $this->app->bind(
            DocumentReferenceRepositoryInterface::class,
            DocumentReferenceRepository::class
        );

        // Location Repositories
        $this->app->bind(CityRepositoryInterface::class, CityRepository::class);
        $this->app->bind(StateRepositoryInterface::class, StateRepository::class);
        $this->app->bind(CountryRepositoryInterface::class, CountryRepository::class);

        // Auth Repositories
        $this->app->bind(RoleRepositoryInterface::class, RoleRepository::class);
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        //
    }
}
