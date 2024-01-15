<?php

namespace Jazzlevit\Orchestrator\Services;

use Illuminate\Support\Facades\Http;

class NetwerkAppService
{
    const SERVICE_NAME = 'netwerk';
    const API_PREFIX = '/api/v1';
    const PRIVATE_API_PREFIX = '/api/private/v1';

    private $orchestratorConfig = [];

    private $userToken = null;

    private OrchestratorService $orchestratorService;


    public function __construct()
    {
        if (auth('therapist')->user()) {
            $token = auth('therapist')->user()->therapistAuth()->first();

            if ($token) {
                $this->userToken = $token->access_token;
            }
        }
    }

    protected function getOrchestratorService()
    {
        if (empty($this->orchestratorService)) {
            $this->orchestratorService = new OrchestratorService;
        }

        return $this->orchestratorService;
    }

    protected function getOrchestratorConfig()
    {
        if (empty($this->orchestratorConfig)) {
            $this->orchestratorConfig = $this->getOrchestratorService()->getConfig(static::SERVICE_NAME);
        }

        return $this->orchestratorConfig;
    }

    protected function getUrl()
    {
        return $this->getOrchestratorConfig()['connection']['url'];
    }

    protected function getSecret()
    {
        return $this->getOrchestratorConfig()['connection']['secret'];
    }

    protected function post($url, $data = [])
    {
        $url = $this->getUrl() . $url;

        $response = Http::asJson()
            ->withToken($this->userToken, type: 'Bearer')
            ->withHeader('Secret', $this->getSecret())
//            ->withHeader('Organisation', [1,2,3])
            ->post($url, $data);

        return $response;
    }
    protected function delete($url)
    {
        $url = $this->getUrl() . $url;

        $response = Http::asJson()
            ->withToken($this->userToken, type: 'Bearer')
            ->withHeader('Secret', $this->getSecret())
            ->delete($url);

        return $response;
    }

    protected function get($url, $data = [])
    {
        $url = $this->getUrl() . $url . '?' . http_build_query($data);

        $response = Http::asJson()
            ->withToken($this->userToken, type: 'Bearer')
            ->withHeader('Secret', $this->getSecret())
            ->get($url);

        return $response;
    }
}
