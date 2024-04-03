<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class CertificateController extends Controller
{
    public function __invoke(): JsonResponse
    {
        return Response()->json();
    }
}
