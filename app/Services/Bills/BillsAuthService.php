<?php

namespace App\Services\Bills;

use App\Models\Bills\HthUser;
use App\Models\Bills\HthUserSession;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class BillsAuthService
{
    public function login(Request $request): JsonResponse
    {
        if (! in_array($request->method(), ['POST'], true)) {
            return response()->json(['message' => 'Method not allowed'], 405);
        }

        $username = trim((string) ($request->input('username') ?? ''));
        $password = (string) ($request->input('password') ?? '');

        if ($username === '' || $password === '') {
            return response()->json(['message' => 'Username and password are required'], 400);
        }

        $user = HthUser::query()
            ->where('username', strtolower($username))
            ->where('password', md5($password))
            ->first(['id', 'username', 'fname', 'lname', 'email']);

        if (! $user) {
            return response()->json(['message' => 'Invalid username or password'], 401);
        }

        $token = bin2hex(random_bytes(32));
        $expiresAt = now()->addDays(30);

        HthUserSession::query()->create([
            'session_key' => $token,
            'user_id' => $user->id,
            'last_until' => $expiresAt,
        ]);

        return response()->json([
            'token' => $token,
            'expires_at' => $expiresAt->format('Y-m-d H:i:s'),
            'user' => [
                'id' => (int) $user->id,
                'username' => $user->username,
                'fname' => $user->fname,
                'lname' => $user->lname,
                'email' => $user->email,
            ],
        ]);
    }

    public function me(Request $request): JsonResponse
    {
        if (! in_array($request->method(), ['GET', 'POST'], true)) {
            return response()->json(['message' => 'Method not allowed'], 405);
        }

        $user = $this->resolveUser($request);
        if (! $user) {
            return response()->json(['message' => 'Unauthorized'], 401);
        }

        return response()->json(['user' => $user]);
    }

    public function logout(Request $request): JsonResponse
    {
        if (! in_array($request->method(), ['GET', 'POST'], true)) {
            return response()->json(['message' => 'Method not allowed'], 405);
        }

        $token = $this->bearerToken($request);
        if ($token === '') {
            return response()->json(['message' => 'Unauthorized'], 401);
        }

        HthUserSession::query()->where('session_key', $token)->delete();

        return response()->json(['message' => 'Logged out']);
    }

    /**
     * @return array<string, mixed>|null
     */
    public function resolveUser(Request $request): ?array
    {
        $token = $this->bearerToken($request);
        if ($token === '') {
            return null;
        }

        $row = HthUserSession::query()
            ->from('hth_user_sessions as s')
            ->join('hth_users as u', 'u.id', '=', 's.user_id')
            ->where('s.session_key', $token)
            ->where('s.last_until', '>', now())
            ->first([
                'u.id',
                'u.username',
                'u.fname',
                'u.lname',
                'u.email',
            ]);

        if (! $row) {
            return null;
        }

        return [
            'id' => (int) $row->id,
            'username' => $row->username,
            'fname' => $row->fname,
            'lname' => $row->lname,
            'email' => $row->email,
        ];
    }

    private function bearerToken(Request $request): string
    {
        $header = (string) $request->header('Authorization', '');
        if (preg_match('/Bearer\s+(\S+)/i', $header, $matches)) {
            return $matches[1];
        }

        return '';
    }
}
