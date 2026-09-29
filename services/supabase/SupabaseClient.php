<?php
/**
 * StayFlow PG Management SaaS - Supabase HTTP Client
 * 
 * Production-ready PostgREST and Auth API client for Supabase.
 * Communicates over HTTPS cURL with proper JSON encoding, error handling, and authorization.
 */

declare(strict_types=1);

class SupabaseClient {
    private string $url;
    private string $apiKey;
    private ?string $bearerToken;
    private int $timeout;

    public function __construct(?string $url = null, ?string $apiKey = null, ?string $bearerToken = null, int $timeout = 10) {
        $this->url = rtrim($url ?? (defined('SUPABASE_URL') ? SUPABASE_URL : ''), '/');
        // Backend operations default to SERVICE_ROLE_KEY if available for administrative execution, or ANON_KEY
        $defaultKey = defined('SUPABASE_SERVICE_ROLE_KEY') && !empty(SUPABASE_SERVICE_ROLE_KEY)
            ? SUPABASE_SERVICE_ROLE_KEY
            : (defined('SUPABASE_ANON_KEY') ? SUPABASE_ANON_KEY : '');
        $this->apiKey = $apiKey ?? $defaultKey;
        $this->bearerToken = $bearerToken ?? $this->apiKey;
        $this->timeout = $timeout;
    }

    public function getUrl(): string {
        return $this->url;
    }

    public function from(string $table): SupabaseQueryBuilder {
        return new SupabaseQueryBuilder($this, $table);
    }

    /**
     * Executes an HTTP request against the Supabase endpoint.
     */
    public function request(string $endpoint, string $method = 'GET', array $queryParams = [], ?array $body = null, array $customHeaders = []): array {
        if (empty($this->url)) {
            return [
                'success' => false,
                'data' => null,
                'error' => 'Supabase URL is not configured.',
                'status' => 0
            ];
        }

        $fullUrl = $this->url . '/' . ltrim($endpoint, '/');
        if (!empty($queryParams)) {
            $queryString = http_build_query($queryParams);
            $fullUrl .= (str_contains($fullUrl, '?') ? '&' : '?') . $queryString;
        }

        $headers = [
            'apikey: ' . $this->apiKey,
            'Authorization: Bearer ' . ($this->bearerToken ?? $this->apiKey),
            'Content-Type: application/json',
            'Accept: application/json'
        ];

        foreach ($customHeaders as $k => $v) {
            $headers[] = is_numeric($k) ? $v : "{$k}: {$v}";
        }

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $fullUrl);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_TIMEOUT, $this->timeout);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, strtoupper($method));

        if (!empty($body) && in_array(strtoupper($method), ['POST', 'PUT', 'PATCH', 'DELETE'], true)) {
            $payload = json_encode($body, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $payload);
        }

        // SSL verification
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 2);

        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $curlError = curl_error($ch);
        curl_close($ch);

        if ($response === false) {
            return [
                'success' => false,
                'data' => null,
                'error' => 'cURL Error: ' . $curlError,
                'status' => $httpCode
            ];
        }

        $decoded = json_decode($response, true);
        $isSuccess = ($httpCode >= 200 && $httpCode < 300);

        return [
            'success' => $isSuccess,
            'data' => $decoded ?? $response,
            'error' => $isSuccess ? null : ($decoded['message'] ?? $decoded['error'] ?? 'HTTP ' . $httpCode),
            'status' => $httpCode
        ];
    }
}

/**
 * Fluent Query Builder for Supabase PostgREST
 */
class SupabaseQueryBuilder {
    private SupabaseClient $client;
    private string $table;
    private string $method = 'GET';
    private array $queryParams = [];
    private ?array $body = null;
    private array $headers = [];

    public function __construct(SupabaseClient $client, string $table) {
        $this->client = $client;
        $this->table = $table;
    }

    public function select(string $columns = '*'): self {
        $this->method = 'GET';
        $this->queryParams['select'] = $columns;
        return $this;
    }

    public function insert(array $data, bool $upsert = false): self {
        $this->method = 'POST';
        // PostgREST expects an array of objects or single object
        $this->body = isset($data[0]) && is_array($data[0]) ? $data : [$data];
        if ($upsert) {
            $this->headers['Prefer'] = 'resolution=merge-duplicates,return=representation';
        } else {
            $this->headers['Prefer'] = 'return=representation';
        }
        return $this;
    }

    public function update(array $data): self {
        $this->method = 'PATCH';
        $this->body = $data;
        $this->headers['Prefer'] = 'return=representation';
        return $this;
    }

    public function delete(): self {
        $this->method = 'DELETE';
        $this->headers['Prefer'] = 'return=representation';
        return $this;
    }

    public function eq(string $column, mixed $val): self {
        $this->queryParams[$column] = 'eq.' . (is_bool($val) ? ($val ? 'true' : 'false') : $val);
        return $this;
    }

    public function neq(string $column, mixed $val): self {
        $this->queryParams[$column] = 'neq.' . (is_bool($val) ? ($val ? 'true' : 'false') : $val);
        return $this;
    }

    public function gt(string $column, mixed $val): self {
        $this->queryParams[$column] = 'gt.' . $val;
        return $this;
    }

    public function gte(string $column, mixed $val): self {
        $this->queryParams[$column] = 'gte.' . $val;
        return $this;
    }

    public function lt(string $column, mixed $val): self {
        $this->queryParams[$column] = 'lt.' . $val;
        return $this;
    }

    public function lte(string $column, mixed $val): self {
        $this->queryParams[$column] = 'lte.' . $val;
        return $this;
    }

    public function like(string $column, string $val): self {
        $this->queryParams[$column] = 'like.' . $val;
        return $this;
    }

    public function ilike(string $column, string $val): self {
        $this->queryParams[$column] = 'ilike.' . $val;
        return $this;
    }

    public function in(string $column, array $values): self {
        $formatted = '(' . implode(',', array_map(fn($v) => '"' . addslashes((string)$v) . '"', $values)) . ')';
        $this->queryParams[$column] = 'in.' . $formatted;
        return $this;
    }

    public function order(string $column, string $direction = 'asc'): self {
        $this->queryParams['order'] = $column . '.' . strtolower($direction);
        return $this;
    }

    public function limit(int $count): self {
        $this->queryParams['limit'] = $count;
        return $this;
    }

    public function offset(int $count): self {
        $this->queryParams['offset'] = $count;
        return $this;
    }

    public function execute(): array {
        $endpoint = 'rest/v1/' . $this->table;
        return $this->client->request($endpoint, $this->method, $this->queryParams, $this->body, $this->headers);
    }
}
