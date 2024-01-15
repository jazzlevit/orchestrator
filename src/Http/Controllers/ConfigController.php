<?php

namespace Jazzlevit\Orchestrator\Http\Controllers;

use Jazzlevit\Orchestrator\Services\OrchestratorService;
use App\Http\Controllers\Controller;

class ConfigController extends Controller
{
    public function show(string $service, OrchestratorService $orchestratorService)
    {
        return $orchestratorService->getConfig($service);
    }
}
