<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Repositories\Fiscal\Interfaces\DianResolutionRepositoryInterface;
use App\Repositories\Fiscal\Interfaces\TaxRuleRepositoryInterface;
use App\Repositories\Fiscal\Interfaces\DocumentNumberingConfigRepositoryInterface;
use App\Repositories\Fiscal\DianResolutionRepository;
use App\Repositories\Fiscal\TaxRuleRepository;
use App\Repositories\Fiscal\DocumentNumberingConfigRepository;

class FiscalServiceProvider extends ServiceProvider
{
    public function register()
    {
        $this->app->bind(DianResolutionRepositoryInterface::class, DianResolutionRepository::class);
        $this->app->bind(TaxRuleRepositoryInterface::class, TaxRuleRepository::class);
        $this->app->bind(DocumentNumberingConfigRepositoryInterface::class, DocumentNumberingConfigRepository::class);
    }

    public function boot()
    {
        //
    }
}
