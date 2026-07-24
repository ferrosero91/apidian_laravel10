<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Company;
use App\Document;
use App\User;
use App\Services\StorageService;
use GuzzleHttp\Client;

class PayrollController extends Controller
{
    public function store(Request $request, $testSetId = null)
    {
        try {
            $user = auth()->user();
            if (!$user) {
                return response()->json(['error' => 'No autenticado'], 401);
            }

            $company = $user->company;
            if (!$company) {
                return response()->json(['error' => 'Empresa no encontrada'], 404);
            }

            // Forward to DIAN via payroll endpoint
            $token = $user->api_token;
            $baseUrl = rtrim(config('app.url'), '/');
            $client = new Client(['base_uri' => $baseUrl]);

            $response = $client->post('/api/ubl2.1/invoice', [
                'headers' => [
                    'Authorization' => 'Bearer ' . $token,
                    'Content-Type' => 'application/json',
                    'Accept' => 'application/json',
                ],
                'json' => array_merge($request->all(), [
                    'type_document_id' => 9, // Nomina individual
                ]),
            ]);

            $result = json_decode($response->getBody(), true);

            return response()->json($result);
        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Error al procesar nómina',
                'message' => $e->getMessage()
            ], 500);
        }
    }

    public function adjustNote(Request $request, $testSetId = null)
    {
        try {
            $user = auth()->user();
            if (!$user) {
                return response()->json(['error' => 'No autenticado'], 401);
            }

            $company = $user->company;
            if (!$company) {
                return response()->json(['error' => 'Empresa no encontrada'], 404);
            }

            // Forward to DIAN via credit-note endpoint with payroll type
            $token = $user->api_token;
            $baseUrl = rtrim(config('app.url'), '/');
            $client = new Client(['base_uri' => $baseUrl]);

            $response = $client->post('/api/ubl2.1/credit-note', [
                'headers' => [
                    'Authorization' => 'Bearer ' . $token,
                    'Content-Type' => 'application/json',
                    'Accept' => 'application/json',
                ],
                'json' => array_merge($request->all(), [
                    'type_document_id' => 10, // Nota de ajuste nomina
                ]),
            ]);

            $result = json_decode($response->getBody(), true);

            return response()->json($result);
        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Error al procesar nota de ajuste',
                'message' => $e->getMessage()
            ], 500);
        }
    }
}
