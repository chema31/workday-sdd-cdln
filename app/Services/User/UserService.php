<?php

namespace App\Services\User;

use App\Enums\UserRole;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

/**
 * Business logic for employee accounts.
 *
 * Holds no HTTP knowledge: Filament resources and controllers delegate to it.
 */
class UserService
{
    private const MINIMUM_PASSWORD_LENGTH = 8;

    public function createEmployee(array $data): User
    {
        $this->validatePassword($data['password'] ?? null);

        return User::create([
            'name' => $data['name'],
            'email' => $data['email'],
            'password' => Hash::make($data['password']),
            'role' => UserRole::Employee,
            'is_active' => $data['is_active'] ?? true,
        ]);
    }

    /**
     * Update an employee, re-hashing the password only when a new one is given.
     */
    public function updateEmployee(User $user, array $data): User
    {
        if (! empty($data['password'])) {
            $this->validatePassword($data['password']);
            $user->password = Hash::make($data['password']);
        }

        $user->name = $data['name'];
        $user->email = $data['email'];
        $user->is_active = $data['is_active'];
        $user->save();

        return $user;
    }

    /**
     * The configured administrator account must never be deleted or demoted.
     */
    public function isProtectedAdmin(User $user): bool
    {
        return $user->email === config('app.admin_email');
    }

    private function validatePassword(?string $password): void
    {
        if (empty($password)) {
            throw new \InvalidArgumentException('Password is required.');
        }

        if (strlen($password) < self::MINIMUM_PASSWORD_LENGTH) {
            throw new \InvalidArgumentException(
                'Password must be at least '.self::MINIMUM_PASSWORD_LENGTH.' characters.'
            );
        }

        if (! preg_match('/[0-9]/', $password)) {
            throw new \InvalidArgumentException('Password must contain at least one number.');
        }
    }
}
