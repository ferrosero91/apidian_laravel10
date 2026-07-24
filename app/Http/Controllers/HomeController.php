<?php

namespace App\Http\Controllers;

use GuzzleHttp\Client;
use Illuminate\Http\Request;
use App\Company;
use App\Document;
use App\Resolution;
use App\ReceivedDocument;
use App\User;
use App\TypeRegime;
use App\TypeLiability;
use App\Municipality;
use App\TypeDocumentIdentification;
use Illuminate\Validation\ValidationException;
use Exception;


class HomeController extends Controller
{
    /**
     * Create a new controller instance.
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function index()
    {
        /** @var User|null $user */
        $user = auth()->user();

        $companiesQuery = Company::query();
        if ($user && method_exists($user, 'isPlatformAdmin') && !$user->isPlatformAdmin()) {
            $companiesQuery->where(function ($q) use ($user) {
                $q->where('user_id', $user->id)
                    ->orWhereHas('users', function ($q2) use ($user) {
                        $q2->where('users.id', $user->id);
                    });
            });
        }

        $companies = $companiesQuery->with('user')->get()->transform(function ($row) {
            $documents = Document::where('identification_number', $row->identification_number)->count();
            $row->total_documents = $documents;
            return $row;
        });

        // Datos para los selects del modal de edición
        $type_regimes = TypeRegime::all();
        $type_liabilities = TypeLiability::all();
        $municipalities = Municipality::all();
        $type_document_identifications = TypeDocumentIdentification::all();

        return view('home', compact('companies', 'type_regimes', 'type_liabilities', 'municipalities', 'type_document_identifications'));
    }

    public function tools()
    {
        return view('tools');
    }

    public function company(Company $company)
    {
        /** @var User|null $user */
        $user = auth()->user();
        if ($user && !$user->canAccessCompany($company)) {
            abort(403, 'No tienes acceso a esta empresa.');
        }

        $documents = Document::where('identification_number', $company->identification_number)->orderBy('id', 'DESC')->paginate(20);

        $resolution_credit_notes = Resolution::where('type_document_id', 4)->where('company_id', $company->id)->get();

        $token_company = $company->user->api_token;

        return view('company.documents', ['company' => $company, 'documents' => $documents, 'resolution_credit_notes' => $resolution_credit_notes, 'token_company' => $token_company]);
    }

    public function getXml(Company $company, $cufe)
    {
        /** @var User|null $user */
        $user = auth()->user();
        if ($user && !$user->canAccessCompany($company)) {
            abort(403, 'No tienes acceso a esta empresa.');
        }

        $token = $company->user->api_token;
        $url = url('/api/ubl2.1/xml/document/'.$cufe);

        $client = new Client();
        $response = $client->post($url, [
            'headers' => [
                'Authorization' => 'Bearer ' . $token,
                'Accept'        => 'application/json',
            ]
        ]);

        // dd($response);
        $responseBody = json_decode($response->getBody(), true);

        // Manejar la respuesta
        if ($response->getStatusCode() == 200) {
            return response()->json($responseBody);
        } else {
            return response()->json([
                'error' => 'Error al hacer la solicitud a la API',
                'status_code' => $response->getStatusCode(),
                'body' => $responseBody,
            ], $response->getStatusCode());
        }
    }

    // replica de SellerLoginController@SellersRadianEventsView
    public function events($company_idnumber){
        $company = Company::where('identification_number', $company_idnumber)->firstOrFail();
        $documents = ReceivedDocument::where('customer','=',$company_idnumber)->where('state_document_id', '=', 1)->paginate(10);
        return view('company.events', compact('documents', 'company_idnumber', 'company'));
    }

    public function update(Request $request, $companyId)
    {
        try {
            $request->validate([
                'identification_number' => 'required|numeric|digits_between:1,15|unique:companies,identification_number,' . $companyId,
                'dv' => 'required|numeric|digits:1',
                'type_regime_id' => 'required|exists:type_regimes,id',
                'type_liability_id' => 'required|exists:type_liabilities,id',
                'municipality_id' => 'required|exists:municipalities,id',
                'merchant_registration' => 'required|string|max:255',
                'address' => 'required|string|max:255',
                'phone' => 'required|string|max:20',
                'type_document_identification_id' => 'required|exists:type_document_identifications,id',
            ], [
                'identification_number.required' => 'El número de identificación es obligatorio.',
                'identification_number.numeric' => 'El número de identificación debe ser numérico.',
                'identification_number.digits_between' => 'El número de identificación debe tener entre 1 y 15 dígitos.',
                'identification_number.unique' => 'Ya existe una empresa con este número de identificación.',
                'dv.required' => 'El dígito de verificación es obligatorio.',
                'dv.numeric' => 'El dígito de verificación debe ser numérico.',
                'dv.digits' => 'El dígito de verificación debe ser de 1 dígito.',
                'type_regime_id.required' => 'El tipo de régimen es obligatorio.',
                'type_regime_id.exists' => 'El tipo de régimen seleccionado no es válido.',
                'type_liability_id.required' => 'El tipo de responsabilidad es obligatorio.',
                'type_liability_id.exists' => 'El tipo de responsabilidad seleccionado no es válido.',
                'municipality_id.required' => 'El municipio es obligatorio.',
                'municipality_id.exists' => 'El municipio seleccionado no es válido.',
                'merchant_registration.required' => 'La matrícula mercantil es obligatoria.',
                'merchant_registration.string' => 'La matrícula mercantil debe ser texto.',
                'merchant_registration.max' => 'La matrícula mercantil no puede tener más de 255 caracteres.',
                'address.required' => 'La dirección es obligatoria.',
                'address.string' => 'La dirección debe ser texto.',
                'address.max' => 'La dirección no puede tener más de 255 caracteres.',
                'phone.required' => 'El teléfono es obligatorio.',
                'phone.string' => 'El teléfono debe ser texto.',
                'phone.max' => 'El teléfono no puede tener más de 20 caracteres.',
                'type_document_identification_id.required' => 'El tipo de documento es obligatorio.',
                'type_document_identification_id.exists' => 'El tipo de documento seleccionado no es válido.',
            ]);

            $company = Company::findOrFail($companyId);

            /** @var User|null $user */
            $user = auth()->user();
            if ($user && !$user->isPlatformAdmin() && !$user->isCompanyOwner($company)) {
                abort(403, 'No tienes permiso para editar esta empresa.');
            }

            $tax_id = $request->tax_id
                ?? (($request->type_regime_id == 2) ? 15 : ($company->tax_id ?? 1));

            $company->update([
                'identification_number' => $request->identification_number,
                'dv' => $request->dv,
                'type_regime_id' => $request->type_regime_id,
                'type_liability_id' => $request->type_liability_id,
                'municipality_id' => $request->municipality_id,
                'merchant_registration' => $request->merchant_registration,
                'address' => $request->address,
                'phone' => $request->phone,
                'type_document_identification_id' => $request->type_document_identification_id,
                'tax_id' => $tax_id,
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Empresa actualizada exitosamente.'
            ]);

        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error de validación.',
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al actualizar la empresa: ' . $e->getMessage()
            ], 500);
        }
    }

    public function toggleState($companyId)
    {
        try {
            $company = Company::findOrFail($companyId);

            /** @var User|null $user */
            $user = auth()->user();
            if ($user && !$user->isPlatformAdmin()) {
                abort(403, 'No tienes permiso para realizar esta acción.');
            }

            $company->update([
                'state' => !$company->state
            ]);

            return response()->json([
                'success' => true,
                'message' => $company->state ? 'Empresa habilitada.' : 'Empresa deshabilitada.'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al cambiar estado: ' . $e->getMessage()
            ], 500);
        }
    }

    public function destroy($companyId)
    {
        try {
            $company = Company::findOrFail($companyId);

            /** @var User|null $user */
            $user = auth()->user();
            if ($user && !$user->isPlatformAdmin()) {
                abort(403, 'No tienes permiso para eliminar empresas.');
            }

            // Eliminar documentos relacionados
            Document::where('identification_number', $company->identification_number)->delete();

            // Eliminar empresa
            $company->delete();

            return response()->json([
                'success' => true,
                'message' => 'Empresa eliminada exitosamente.'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al eliminar empresa: ' . $e->getMessage()
            ], 500);
        }
    }

    public function changeEnvironment(Request $request, $companyId)
    {
        try {
            $request->validate([
                'type_environment_id' => 'required|in:1,2',
            ]);

            $company = Company::findOrFail($companyId);

            /** @var User|null $user */
            $user = auth()->user();
            if ($user && !$user->isPlatformAdmin()) {
                abort(403, 'No tienes permiso para realizar esta acción.');
            }

            $company->update([
                'type_environment_id' => $request->type_environment_id
            ]);

            $envName = $request->type_environment_id == 1 ? 'Producción' : 'Habilitación';

            return response()->json([
                'success' => true,
                'message' => 'Ambiente cambiado a ' . $envName . ' exitosamente.'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al cambiar ambiente: ' . $e->getMessage()
            ], 500);
        }
    }

    // =========================================================================
    // MONITORING
    // =========================================================================

    public function monitoring()
    {
        $stats = [
            'total_companies' => \App\Company::count(),
            'total_documents' => \App\Document::count(),
            'total_users' => \App\User::count(),
            'documents_today' => \App\Document::whereDate('created_at', today())->count(),
            'documents_this_month' => \App\Document::whereMonth('created_at', now()->month)->whereYear('created_at', now()->year)->count(),
            'server_time' => now()->format('Y-m-d H:i:s'),
            'php_version' => phpversion(),
            'laravel_version' => app()->version(),
            'database_size' => $this->getDatabaseSize(),
            'storage_usage' => $this->getStorageUsage(),
        ];

        return view('monitoring', compact('stats'));
    }

    private function getDatabaseSize()
    {
        try {
            $dbName = config('database.connections.mysql.database');
            $result = \DB::select("SELECT ROUND(SUM(data_length + index_length) / 1024 / 1024, 2) AS size FROM information_schema.tables WHERE table_schema = ?", [$dbName]);
            return ($result[0]->size ?? 0) . ' MB';
        } catch (\Exception $e) {
            return 'N/A';
        }
    }

    private function getStorageUsage()
    {
        try {
            $bytes = 0;
            $path = storage_path('app');
            if (\File::isDirectory($path)) {
                $files = \File::allFiles($path);
                foreach ($files as $file) {
                    $bytes += $file->getSize();
                }
            }
            return round($bytes / 1024 / 1024, 2) . ' MB';
        } catch (\Exception $e) {
            return 'N/A';
        }
    }

    // =========================================================================
    // BACKUPS
    // =========================================================================

    public function backups()
    {
        $backupPath = storage_path('app/backups');
        $backups = [];

        if (\File::isDirectory($backupPath)) {
            $files = \File::files($backupPath);
            foreach ($files as $file) {
                if (pathinfo($file->getFilename(), PATHINFO_EXTENSION) === 'sql') {
                    $backups[] = [
                        'name' => $file->getFilename(),
                        'size' => round($file->getSize() / 1024 / 1024, 2) . ' MB',
                        'date' => \Carbon\Carbon::createFromTimestamp($file->getMTime())->format('Y-m-d H:i:s'),
                    ];
                }
            }
        }

        usort($backups, function($a, $b) {
            return strcmp($b['date'], $a['date']);
        });

        return view('backups', compact('backups'));
    }

    public function backupCreate()
    {
        try {
            $backupPath = storage_path('app/backups');
            if (!\File::isDirectory($backupPath)) {
                \File::makeDirectory($backupPath, 0755, true);
            }

            $filename = 'backup_' . date('Y-m-d_H-i-s') . '.sql';
            $dbName = config('database.connections.mysql.database');
            $dbHost = config('database.connections.mysql.host');
            $dbUser = config('database.connections.mysql.username');
            $dbPass = config('database.connections.mysql.password');

            // Try mysqldump first
            $command = "mysqldump -h {$dbHost} -u {$dbUser} -p'{$dbPass}' {$dbName} 2>&1";
            $output = [];
            $returnVar = 0;
            exec($command, $output, $returnVar);

            if ($returnVar === 0 && !empty($output)) {
                $sqlContent = implode("\n", $output);
                \File::put($backupPath . '/' . $filename, $sqlContent);
                return response()->json(['success' => true, 'message' => 'Backup creado exitosamente.']);
            }

            // Fallback: use PHP to dump tables
            return $this->createPhpBackup($backupPath, $filename, $dbName);

        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Error: ' . $e->getMessage()], 500);
        }
    }

    private function createPhpBackup($backupPath, $filename, $dbName)
    {
        try {
            $sql = "-- APIDIAN Database Backup\n";
            $sql .= "-- Date: " . date('Y-m-d H:i:s') . "\n";
            $sql .= "-- Database: {$dbName}\n\n";
            $sql .= "SET FOREIGN_KEY_CHECKS=0;\n\n";

            // Get all tables
            $tables = \DB::select("SHOW TABLES");
            $tableKey = "Tables_in_{$dbName}";

            foreach ($tables as $table) {
                $tableName = $table->$tableKey;
                $sql .= "-- Table: {$tableName}\n";
                $sql .= "DROP TABLE IF EXISTS `{$tableName}`;\n";

                // Get CREATE TABLE statement
                $createTable = \DB::select("SHOW CREATE TABLE `{$tableName}`");
                if (isset($createTable[0]->{'Create Table'})) {
                    $sql .= $createTable[0]->{'Create Table'} . ";\n\n";
                }

                // Get data
                $rows = \DB::select("SELECT * FROM `{$tableName}`");
                if (!empty($rows)) {
                    $columns = array_keys((array) $rows[0]);
                    $sql .= "INSERT INTO `{$tableName}` (`" . implode('`, `', $columns) . "`) VALUES\n";

                    $values = [];
                    foreach ($rows as $row) {
                        $rowValues = [];
                        foreach ((array) $row as $value) {
                            if ($value === null) {
                                $rowValues[] = 'NULL';
                            } else {
                                $rowValues[] = "'" . addslashes($value) . "'";
                            }
                        }
                        $values[] = '(' . implode(', ', $rowValues) . ')';
                    }
                    $sql .= implode(",\n", $values) . ";\n\n";
                }
            }

            $sql .= "SET FOREIGN_KEY_CHECKS=1;\n";

            \File::put($backupPath . '/' . $filename, $sql);

            return response()->json(['success' => true, 'message' => 'Backup creado exitosamente.']);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Error al crear backup: ' . $e->getMessage()], 500);
        }
    }

    public function backupDownload($file)
    {
        $path = storage_path('app/backups/' . $file);
        if (\File::exists($path) && pathinfo($file, PATHINFO_EXTENSION) === 'sql') {
            return response()->download($path);
        }
        abort(404);
    }

    public function backupDelete($file)
    {
        try {
            $path = storage_path('app/backups/' . $file);
            if (\File::exists($path) && pathinfo($file, PATHINFO_EXTENSION) === 'sql') {
                \File::delete($path);
                return response()->json(['success' => true, 'message' => 'Backup eliminado.']);
            }
            return response()->json(['success' => false, 'message' => 'Archivo no encontrado.'], 404);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Error al eliminar.'], 500);
        }
    }
}
