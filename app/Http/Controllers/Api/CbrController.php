<?php

namespace App\Http\Controllers\Api;

use App\DataTransferObjects\CbrRateRequestDTO;
use App\Helpers\ApiResponder;
use App\Http\Controllers\Controller;
use App\Http\Requests\GetRatesRequest;
use App\Services\CbrService;
use Illuminate\Http\JsonResponse;

class CbrController extends Controller
{
    public function getRates(GetRatesRequest $request, CbrService $service): JsonResponse
    {
        return ApiResponder::success(
            $service->getDifferenceBetweenRates(
                CbrRateRequestDTO::fromRequest($request)
            )
        );
    }
}
