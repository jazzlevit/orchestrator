<?php

namespace Jazzlevit\Orchestrator\Services\Auth;

use App\Orchestrator\Services\NetwerkAppService;

class FastTokenService extends NetwerkAppService
{
    public function generate()
    {
        $response = $this->post(static::PRIVATE_API_PREFIX . '/fast-token/generate');

        return $response;
    }
    public function match($token)
    {
        $response = $this->post(static::PRIVATE_API_PREFIX . '/fast-token/match', [
            'fast_token' => $token,
        ]);

        return $response;
    }
}
