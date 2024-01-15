<?php

namespace Jazzlevit\Orchestrator\Services;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;

class OrchestratorService
{
    protected function getUrl()
    {
        return env('ORCHESTRATOR_URL');
    }
    protected function getClient()
    {
        return env('ORCHESTRATOR_CLIENT');
    }

    protected function getSecret()
    {
        return env('ORCHESTRATOR_SECRET');
    }

    protected function post($url, $data)
    {
        $url = $this->getUrl() . $url;

        $response = Http::asJson()
            ->withHeader('Client', $this->getClient())
            ->withHeader('Secret', $this->getSecret())
            ->post($url, $data);

        return $response;
    }

    protected function get($url)
    {
        $url = $this->getUrl() . $url;

        $response = Http::asJson()
            ->withHeader('Client', $this->getClient())
            ->withHeader('Secret', $this->getSecret())
            ->get($url);

        return $response;
    }

    public function getConfig($app)
    {
        $cacheKey = 'connection_config';

        $responseData = Cache::get($cacheKey, []);
        if (empty($responseData)) {
            $response = $this->get('/api/config');

            if ($response->ok()) {
                $responseData = $response->json();

                Cache::put($cacheKey, $responseData, now()->addMinutes(10));
            }
        }

        $result = [];

        if ($responseData) {
           $result = collect($responseData)->where('slug', $app)->first();
        }

        return $result;
    }
}
