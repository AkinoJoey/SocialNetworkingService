<?php

namespace src\routing;

use Closure;
use src\helpers\Settings;

class Route
{

    private string $path;
    /**
     * @var string[]
     */
    private array $middleware;
    private Closure $callback;

    public function __construct(string $path, callable $callback)
    {
        $this->path = $path;
        // これは、Closure::fromCallable($callable) の代替構文です
        // クロージャはコールバックをクラスにカプセル化し、そのスコープをカプセル化します。元のスコープの変数を使用するにはuseキーワードを使用します
        $this->callback = $callback(...);
    }

    // ルートを作成するための静的関数
    // (new Route($callback))->setMiddleware(...)の代わりに、Route::create($callback)->setMiddleware(...)を実行できます
    public static function create(string $path, callable $callback): Route
    {
        return new self($path, $callback);
    }

    public function setMiddleware(array $middleware): Route
    {
        $this->middleware = $middleware;
        return $this;
    }

    public function getMiddleware(): array
    {
        return $this->middleware ?? [];
    }

    public function getCallback(): Closure
    {
        return $this->callback;
    }

    public function getPath(): string
    {
        return $this->path;
    }

    private function getBaseURL(): string
    {
        if (!isset($_SERVER)) return $this->getPath();

        $host = $_SERVER['HTTP_HOST'];
        return sprintf("%s/%s", $host, $this->getPath());
    }

    private function getSecretKey(): string
    {
        return Settings::env("SIGNATURE_SECRET_KEY");
    }

    public function getSignature(string $url): string
    {
        $parsedUrl = parse_url($url);
        if (!isset($parsedUrl['query'])) throw new \InvalidArgumentException("無効な署名付きURLです");

        $queryParams = [];

        parse_str($parsedUrl['query'], $queryParams);

        if (!isset($queryParams['signature'])) throw new \InvalidArgumentException("無効な署名付きURLです");

        $signature = $queryParams['signature'];

        return $signature;
    }

    public function getSignedURL(array $queryParameters): string
    {
        $url = $this->getBaseURL();

        // 組み込み関数のhttp_build_queryを使用すると、 URLパラメータのクエリ文字列を配列から作成することができます。
        $queryString = http_build_query($queryParameters);

        // HMAC-SHA256を使って署名を作成します。
        $signature = hash_hmac('sha256', $url . '?' . $queryString, $this->getSecretKey());

        // パーツを組み合わせて値を返します。
        return sprintf("%s?%s&signature=%s", $url, $queryString, $signature);
    }

    public function isSignedURLValid(string $url, bool $absolute = true): bool
    {
        // URLデータを含む連想配列を返すparse_url組み込み関数を使用して、URLから署名を抽出します。
        $parsedUrl = parse_url($url);
        if (!isset($parsedUrl['query'])) return false;

        $queryParams = [];

        // $parsedUrl['query']は、"param1=value1&param2=value2 "の形式でキーと値のペアを返し、parse_strはそれらを配列に挿入するために文字列をパースすることができます。
        parse_str($parsedUrl['query'], $queryParams);

        if (!isset($queryParams['signature'])) return false;

        $signature = $queryParams['signature'];

        // 検証用URLから署名を削除します。
        $urlWithoutSignature = str_replace('&signature=' . $signature, '', $url);

        // URL生成時と同じ方法で署名を再作成します。
        $expectedSignature = hash_hmac('sha256', $urlWithoutSignature, $this->getSecretKey());

        return hash_equals($expectedSignature, $signature);
    }
}
