<?php

namespace App\Providers;

use App\Repositories\Contracts\Branch\BranchRepositoryInterface;
use App\Repositories\Contracts\Company\CompanyRepositoryInterface;
use App\Repositories\Eloquent\Branch\BranchRepository;
use App\Repositories\Eloquent\Company\CompanyRepository;
use Illuminate\Support\ServiceProvider;

class RepositoryServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->bind(CompanyRepositoryInterface::class, CompanyRepository::class);
        $this->app->bind(BranchRepositoryInterface::class, BranchRepository::class);
    }
}
