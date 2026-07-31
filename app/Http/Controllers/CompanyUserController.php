<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Company;
use App\User;

class CompanyUserController extends Controller
{
    protected function ensureCanManageCompany(Company $company): void
    {
        /** @var User|null $user */
        $user = auth()->user();
        if (!$user) {
            abort(403);
        }

        if ($user->isPlatformAdmin()) {
            return;
        }

        if ($user->isCompanyOwner($company)) {
            return;
        }

        abort(403, 'No tienes permiso para administrar esta empresa.');
    }

    public function emailIndex($companyId)
    {
        $company = Company::with('user')->findOrFail($companyId);
        $this->ensureCanManageCompany($company);
        $user = $company->user;

        // Obtener configuracion actual
        $emailConfig = [
            'mail_host' => $user->mail_host ?: config('mail.host'),
            'mail_port' => $user->mail_port ?: config('mail.port'),
            'mail_username' => $user->mail_username ?: config('mail.username'),
            'mail_password' => $user->mail_password ? '********' : '',
            'mail_encryption' => $user->mail_encryption ?: config('mail.encryption'),
            'mail_from_address' => $user->mail_from_address ?: config('mail.from.address'),
            'mail_from_name' => $user->mail_from_name ?: config('mail.from.name'),
            'has_custom_config' => !empty($user->mail_host)
        ];

        return view('company.email', compact('company', 'emailConfig'));
    }

    public function emailStore(Request $request, $companyId)
    {
        $request->validate([
            'mail_host' => 'required|string',
            'mail_port' => 'required|integer',
            'mail_username' => 'required|string',
            'mail_password' => 'required|string',
            'mail_encryption' => 'required|string|in:tls,ssl',
            'mail_from_address' => 'nullable|email',
            'mail_from_name' => 'nullable|string',
        ]);

        try {
            $company = Company::findOrFail($companyId);
            $this->ensureCanManageCompany($company);
            $user = $company->user;

            $user->update([
                'mail_host' => $request->mail_host,
                'mail_port' => $request->mail_port,
                'mail_username' => $request->mail_username,
                'mail_password' => $request->mail_password,
                'mail_encryption' => $request->mail_encryption,
                'mail_from_address' => $request->mail_from_address,
                'mail_from_name' => $request->mail_from_name,
            ]);

            return redirect()->back()->with('success', 'Configuracion de correo actualizada exitosamente.');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Ocurrio un error al actualizar la configuracion de correo.');
        }
    }

    // =========================================================================
    // USERS MANAGEMENT
    // =========================================================================

    public function usersIndex($companyId)
    {
        $company = Company::with(['user', 'users'])->findOrFail($companyId);
        $this->ensureCanManageCompany($company);

        return view('company.users', compact('company'));
    }

    public function usersStore(Request $request, $companyId)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|string|min:6',
            'role' => 'required|string|in:user,admin',
        ]);

        try {
            $company = Company::findOrFail($companyId);
            $this->ensureCanManageCompany($company);

            $user = User::create([
                'name' => $request->name,
                'email' => $request->email,
                'password' => bcrypt($request->password),
                'api_token' => bin2hex(random_bytes(32)),
            ]);

            $company->users()->attach($user->id, ['role' => $request->role]);

            return response()->json(['success' => true, 'message' => 'Usuario agregado exitosamente.']);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Error al agregar usuario: ' . $e->getMessage()], 500);
        }
    }

    public function usersDestroy($companyId, $userId)
    {
        try {
            $company = Company::findOrFail($companyId);
            $this->ensureCanManageCompany($company);

            $company->users()->detach($userId);

            return response()->json(['success' => true, 'message' => 'Usuario eliminado exitosamente.']);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Error al eliminar usuario.'], 500);
        }
    }

    // =========================================================================
    // APP ACCESS
    // =========================================================================

    public function appAccessIndex($companyId)
    {
        $company = Company::findOrFail($companyId);
        $this->ensureCanManageCompany($company);

        $devices = collect(); // Placeholder for devices table

        return view('company.app-access', compact('company', 'devices'));
    }

    public function appAccessStore(Request $request, $companyId)
    {
        $request->validate([
            'app_access_enabled' => 'required|boolean',
            'app_device_limit' => 'required|integer|min:1|max:10',
        ]);

        try {
            $company = Company::findOrFail($companyId);
            $this->ensureCanManageCompany($company);

            $company->update([
                'app_access_enabled' => $request->app_access_enabled,
                'app_device_limit' => $request->app_device_limit,
            ]);

            return response()->json(['success' => true, 'message' => 'Configuración de app guardada.']);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Error al guardar.'], 500);
        }
    }

    public function appAccessGenerateToken($companyId)
    {
        try {
            $company = Company::findOrFail($companyId);
            $this->ensureCanManageCompany($company);

            $company->update([
                'app_access_token' => bin2hex(random_bytes(32)),
            ]);

            return response()->json(['success' => true, 'message' => 'Token generado.', 'token' => $company->app_access_token]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Error al generar token.'], 500);
        }
    }

    public function appAccessRemoveDevice($companyId, $deviceId)
    {
        // Placeholder for device removal
        return response()->json(['success' => true, 'message' => 'Dispositivo eliminado.']);
    }

    // =========================================================================
    // STORAGE S3
    // =========================================================================

    public function storageIndex($companyId)
    {
        $company = Company::findOrFail($companyId);
        $this->ensureCanManageCompany($company);

        $usedSpace = '0 MB';
        $totalSpace = '1 GB';
        $usagePercentage = 0;

        return view('company.storage', compact('company', 'usedSpace', 'totalSpace', 'usagePercentage'));
    }

    public function storageStore(Request $request, $companyId)
    {
        $request->validate([
            'storage_mode' => 'required|in:local,s3,dual',
            'aws_access_key_id' => 'nullable|string',
            'aws_secret_access_key' => 'nullable|string',
            'aws_default_region' => 'nullable|string',
            'aws_bucket' => 'nullable|string',
            'aws_url' => 'nullable|string',
        ]);

        try {
            $company = Company::findOrFail($companyId);
            $this->ensureCanManageCompany($company);

            $data = [
                'storage_mode' => $request->storage_mode,
            ];

            if ($request->storage_mode !== 'local') {
                $data['aws_access_key_id'] = $request->aws_access_key_id;
                $data['aws_secret_access_key'] = $request->aws_secret_access_key;
                $data['aws_default_region'] = $request->aws_default_region;
                $data['aws_bucket'] = $request->aws_bucket;
                $data['aws_url'] = $request->aws_url;
            }

            $company->update($data);

            return response()->json(['success' => true, 'message' => 'Configuración de almacenamiento guardada.']);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Error al guardar: ' . $e->getMessage()], 500);
        }
    }

    public function storageTest($companyId)
    {
        try {
            $company = Company::findOrFail($companyId);
            $this->ensureCanManageCompany($company);

            if (($company->storage_mode ?? 'local') === 'local') {
                return response()->json(['success' => true, 'message' => 'Almacenamiento local activo.']);
            }

            // Test S3 connection
            $s3 = \Storage::disk('s3');
            $s3->put('test-connection.txt', 'test');
            $s3->delete('test-connection.txt');

            return response()->json(['success' => true, 'message' => 'Conexión S3 exitosa.']);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Error de conexión S3: ' . $e->getMessage()], 500);
        }
    }
}
