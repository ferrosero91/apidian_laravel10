<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Company;
use App\User;

class RipsController extends Controller
{
    /**
     * Generate RIPS data for a company.
     */
    public function generate(Request $request)
    {
        try {
            $user = auth()->user();
            if (!$user) {
                return response()->json(['error' => 'No autenticado'], 401);
            }

            // Check if user has RIPS permission
            if (!$user->can_rips) {
                return response()->json(['error' => 'No tiene permiso para generar RIPS'], 403);
            }

            $company = $user->company;
            if (!$company) {
                return response()->json(['error' => 'Empresa no encontrada'], 404);
            }

            $request->validate([
                'start_date' => 'required|date',
                'end_date' => 'required|date|after_or_equal:start_date',
            ]);

            // Get documents in date range
            $documents = \App\Document::where('identification_number', $company->identification_number)
                ->whereBetween('date_issue', [$request->start_date, $request->end_date])
                ->where('state_document_id', 1) // Only validated documents
                ->get();

            if ($documents->isEmpty()) {
                return response()->json([
                    'success' => false,
                    'message' => 'No se encontraron documentos en el rango de fechas seleccionado.'
                ]);
            }

            // Generate RIPS structure
            $rips = [
                'header' => [
                    'issuer_nit' => $company->identification_number,
                    'issuer_dv' => $company->dv,
                    'issuer_name' => $company->user->name,
                    'period_start' => $request->start_date,
                    'period_end' => $request->end_date,
                    'generation_date' => now()->format('Y-m-d H:i:s'),
                ],
                'transactions' => $documents->map(function ($doc) {
                    return [
                        'document_type' => $doc->type_document_id,
                        'prefix' => $doc->prefix,
                        'number' => $doc->number,
                        'date' => $doc->date_issue,
                        'customer_id' => $doc->customer->identification_number ?? '',
                        'customer_name' => $doc->customer->name ?? '',
                        'total' => $doc->total,
                        'tax' => $doc->total_tax,
                    ];
                }),
                'summary' => [
                    'total_documents' => $documents->count(),
                    'total_amount' => $documents->sum('total'),
                    'total_tax' => $documents->sum('total_tax'),
                ],
            ];

            return response()->json([
                'success' => true,
                'rips' => $rips,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Error al generar RIPS',
                'message' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get RIPS configuration for a company.
     */
    public function config(Request $request)
    {
        try {
            $user = auth()->user();
            if (!$user) {
                return response()->json(['error' => 'No autenticado'], 401);
            }

            return response()->json([
                'success' => true,
                'can_rips' => $user->can_rips ?? false,
                'url_fevrips' => $user->url_fevrips ?? null,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Error al obtener configuración RIPS',
                'message' => $e->getMessage()
            ], 500);
        }
    }
}
