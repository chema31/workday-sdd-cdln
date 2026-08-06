<?php

namespace App\Filament\Resources\EmployeeResource\Pages;

use App\Filament\Resources\EmployeeResource;
use App\Services\User\UserService;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Database\Eloquent\Model;

class CreateEmployee extends CreateRecord
{
    protected static string $resource = EmployeeResource::class;

    /**
     * All writes go through the service, which hashes the password.
     */
    protected function handleRecordCreation(array $data): Model
    {
        return app(UserService::class)->createEmployee($data);
    }
}
