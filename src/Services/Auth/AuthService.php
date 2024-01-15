<?php

namespace Jazzlevit\Orchestrator\Services\Auth;

use App\Enums\UserRoles;
use App\Models\Therapist;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class AuthService
{
    private function allowedGuard()
    {
        return [
            'admin' => [
                UserRoles::DIRECTOR,
                UserRoles::MANAGER,
            ],
            'therapist' => [
                UserRoles::HEAD_PRACTICIONER,
                UserRoles::FREELANCER,
                UserRoles::MANAGER,
            ],
        ];
    }

    public function __construct() {}

    public function login($data, $guard = 'therapist'): ?Therapist
    {

        if (empty($data['user'])
            || empty($data['role']
            || empty($data['user_token']))
        ) {
            return null;
        }

        $this->checkGuardAccess($guard, $data['role']['id'] ?? null);

        $therapist = Therapist::where(['netwerk_id' => $data['user']['id']])->first();

        $therapistData = [
            'netwerk_id' => $data['user']['id'],
            'first_name' => $data['user']['first_name'],
            'last_name' => $data['user']['last_name'],
            'email' => $data['user']['email'],
            'password' => Hash::make('all passwords stored in netwerk user databases'),
        ];

        DB::beginTransaction();
        if ($therapist === null) {
            $therapist = Therapist::create($therapistData);
            $therapist->therapistAuth()->create([
                'access_token' => $data['user_token']['access_token'],
                'refresh_token' => $data['user_token']['refresh_token'],
            ]);
        } else {
            $therapist->update($therapistData);
            $therapist->therapistAuth()->delete();
            $therapist->therapistAuth()->create([
                'access_token' => $data['user_token']['access_token'],
                'refresh_token' => $data['user_token']['refresh_token'],
            ]);
        }
        DB::commit();

        auth()->guard($guard)->login($therapist);

        request()->session()->regenerate();

        return $therapist;
    }

    private function checkGuardAccess($guard, $roleId)
    {
        if (empty($roleId)) {
            throw ValidationException::withMessages([
                'email' => [trans('auth.failed')],
            ]);
        }

        if (empty($this->allowedGuard()[$guard])) {
            throw ValidationException::withMessages([
                'email' => [trans('auth.failed')],
            ]);
        }

        if (in_array($roleId, $this->allowedGuard()[$guard]) === false) {
            throw ValidationException::withMessages([
                'email' => [trans('auth.failed')],
            ]);
        }
    }
}
