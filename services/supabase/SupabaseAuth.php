<?php
/**
 * StayFlow PG Management SaaS - Supabase Authentication Service
 * 
 * Handles Supabase GoTrue Auth API operations:
 * - User Sign Up
 * - User Sign In (password)
 * - Sign Out
 * - Password Recovery
 * - Session & JWT Token Management
 */

declare(strict_types=1);

require_once __DIR__ . '/SupabaseClient.php';

class SupabaseAuth {
    private SupabaseClient $client;

    public function __construct(?SupabaseClient $client = null) {
        $this->client = $client ?? new SupabaseClient();
    }

    /**
     * Sign up a new user with email and password in Supabase Auth.
     */
    public function signUp(string $email, string $password, array $metadata = []): array {
        $body = [
            'email' => $email,
            'password' => $password,
            'data' => $metadata
        ];

        return $this->client->request('auth/v1/signup', 'POST', [], $body);
    }

    /**
     * Sign in user with email and password via Supabase Auth.
     */
    public function signIn(string $email, string $password): array {
        $body = [
            'email' => $email,
            'password' => $password
        ];

        $res = $this->client->request('auth/v1/token?grant_type=password', 'POST', [], $body);
        if ($res['success'] && !empty($res['data']['access_token'])) {
            // Establish session
            if (session_status() === PHP_SESSION_NONE && !headers_sent()) {
                session_start();
            }
            $_SESSION['supabase_access_token'] = $res['data']['access_token'];
            $_SESSION['supabase_refresh_token'] = $res['data']['refresh_token'] ?? null;
            $_SESSION['supabase_user'] = $res['data']['user'] ?? null;
        }

        return $res;
    }

    /**
     * Retrieve the current authenticated user's profile from Supabase Auth.
     */
    public function getUser(?string $jwtToken = null): array {
        $token = $jwtToken ?? ($_SESSION['supabase_access_token'] ?? null);
        if (!$token) {
            return ['success' => false, 'data' => null, 'error' => 'No active auth token.', 'status' => 401];
        }

        $headers = [
            'Authorization' => 'Bearer ' . $token
        ];

        return $this->client->request('auth/v1/user', 'GET', [], null, $headers);
    }

    /**
     * Log out current user from Supabase Auth.
     */
    public function signOut(?string $jwtToken = null): array {
        $token = $jwtToken ?? ($_SESSION['supabase_access_token'] ?? null);
        
        $res = ['success' => true];
        if ($token) {
            $headers = ['Authorization' => 'Bearer ' . $token];
            $res = $this->client->request('auth/v1/logout', 'POST', [], null, $headers);
        }

        if (session_status() === PHP_SESSION_NONE && !headers_sent()) {
            session_start();
        }
        unset($_SESSION['supabase_access_token'], $_SESSION['supabase_refresh_token'], $_SESSION['supabase_user']);

        return $res;
    }

    /**
     * Trigger a password recovery email via Supabase Auth.
     */
    public function resetPassword(string $email, ?string $redirectTo = null): array {
        $body = ['email' => $email];
        $queryParams = [];
        if ($redirectTo) {
            $queryParams['redirect_to'] = $redirectTo;
        }

        return $this->client->request('auth/v1/recover', 'POST', $queryParams, $body);
    }
}
