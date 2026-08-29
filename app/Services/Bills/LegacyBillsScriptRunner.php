<?php

namespace App\Services\Bills;

use App\Exceptions\BillsLegacyResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use RuntimeException;
use Symfony\Component\HttpFoundation\File\UploadedFile;

class LegacyBillsScriptRunner
{
    public function run(string $legacyScript, Request $request): JsonResponse
    {
        $sitePath = rtrim((string) config('bills.site_path'), '/');
        if ($sitePath === '' || ! is_dir($sitePath)) {
            throw new RuntimeException('BILLS_SITE_PATH is not configured or does not exist.');
        }

        $scriptPath = $sitePath.'/api/'.$legacyScript;
        if (! is_file($scriptPath)) {
            return response()->json([
                'message' => 'Legacy endpoint not found: '.$legacyScript,
            ], 404);
        }

        $this->configureEnvironment($request);
        $this->injectSanctumUser($request);

        if (! defined('BILLS_LEGACY_CAPTURE')) {
            define('BILLS_LEGACY_CAPTURE', true);
        }

        $previousDir = getcwd();
        chdir(dirname($scriptPath));

        try {
            require $scriptPath;

            return response()->json([
                'message' => 'Legacy endpoint completed without a JSON response.',
            ], 500);
        } catch (BillsLegacyResponse $response) {
            return response()->json($response->payload, $response->status);
        } finally {
            if ($previousDir) {
                chdir($previousDir);
            }
        }
    }

    private function configureEnvironment(Request $request): void
    {
        $this->syncSuperglobals($request);
        $this->syncDatabaseEnv();
    }

    private function injectSanctumUser(Request $request): void
    {
        $user = $request->user();
        if (! $user) {
            return;
        }

        $name = trim((string) ($user->name ?? ''));
        $parts = preg_split('/\s+/', $name, 2) ?: [];

        $GLOBALS['api_user'] = [
            'id' => (int) $user->id,
            'username' => (string) ($user->email ?? $user->name ?? 'user'),
            'fname' => $parts[0] ?? '',
            'lname' => $parts[1] ?? '',
            'email' => (string) ($user->email ?? ''),
        ];
    }

    private function syncSuperglobals(Request $request): void
    {
        $_SERVER['REQUEST_METHOD'] = $request->method();
        $_SERVER['HTTP_AUTHORIZATION'] = $request->header('Authorization', '');
        $_SERVER['CONTENT_TYPE'] = $request->header('Content-Type', '');
        $_SERVER['REMOTE_ADDR'] = $request->ip() ?? '127.0.0.1';
        $_SERVER['HTTP_USER_AGENT'] = $request->userAgent() ?? '';

        $origin = $request->header('Origin', '');
        if ($origin !== '') {
            $_SERVER['HTTP_ORIGIN'] = $origin;
        }

        $_GET = $request->query->all();
        $_POST = $request->request->all();
        $_REQUEST = array_merge($_GET, $_POST, $request->all());
        $_FILES = $this->normalizeUploadedFiles($request->files->all());

        $json = $request->json()->all();
        if (is_array($json) && $json !== []) {
            $GLOBALS['bills_legacy_json_body'] = $json;
        } elseif ($request->getContent() !== '') {
            $decoded = json_decode($request->getContent(), true);
            $GLOBALS['bills_legacy_json_body'] = is_array($decoded) ? $decoded : [];
        } else {
            $GLOBALS['bills_legacy_json_body'] = [];
        }
    }

    private function syncDatabaseEnv(): void
    {
        $live = config('database.connections.asimo124_bills');
        $test = config('database.connections.asimo124_bills_test');
        $recipes = config('database.connections.mysql');

        putenv('BILLS_DB_HOST='.$live['host']);
        putenv('BILLS_DB_PORT='.(string) $live['port']);
        putenv('BILLS_DB_DATABASE='.$live['database']);
        putenv('BILLS_DB_DATABASE3='.$test['database']);
        putenv('BILLS_DB_DATABASE4='.$recipes['database']);
        putenv('BILLS_DB_USERNAME='.$live['username']);
        putenv('BILLS_DB_PASSWORD='.$live['password']);

        // api_db is optional; fall back to recipes DB name when not configured.
        putenv('BILLS_DB_DATABASE2='.($recipes['database'] ?? 'api_db'));
    }

    /**
     * @param  array<string, mixed>  $files
     * @return array<string, mixed>
     */
    private function normalizeUploadedFiles(array $files): array
    {
        $normalized = [];

        foreach ($files as $key => $file) {
            $normalized[$key] = $this->normalizeUploadedFile($file);
        }

        return $normalized;
    }

    private function normalizeUploadedFile(mixed $file): mixed
    {
        if ($file instanceof UploadedFile) {
            return [
                'name' => $file->getClientOriginalName(),
                'type' => $file->getClientMimeType() ?? '',
                'tmp_name' => $file->getPathname(),
                'error' => $file->getError(),
                'size' => $file->getSize(),
            ];
        }

        if (! is_array($file)) {
            return $file;
        }

        if (array_is_list($file)) {
            return array_map(fn (mixed $item): mixed => $this->normalizeUploadedFile($item), $file);
        }

        $normalized = [];
        foreach ($file as $key => $value) {
            $normalized[$key] = $this->normalizeUploadedFile($value);
        }

        return $normalized;
    }
}
