<?php

namespace App;

use Closure, Throwable, stdClass;

class Http
{
    protected  const HTTP_GET = 1;
    protected  const HTTP_POST = 2;
    protected  const HTTP_PUT = 3;
    protected  const HTTP_DELETE = 4;
    protected bool   $ssl                      = false;
    protected bool   $dtn                      = false;
    protected bool   $verbose                  = false;
    protected bool   $insecureRequests         = false;
    protected array  $logs                     = [];
    protected array  $cookies                  = [];
    protected array  $headers                  = [];
    protected string|null $error               = null;
    protected string|null $endpoint            = null;
    protected string|null $referer             = null;
    protected string|null $userAgent           = null;
    protected string|null $contentType         = null;
    protected string|null $origin              = null;
    protected string|null $host                = null;
    protected string|null $connection          = null;
    protected string|null $cacheControl        = null;
    protected string|null $accept              = null;
    protected string|null $acceptEncoding      = null;
    protected string|null $acceptLanguage      = null;
    protected object|null $response            = null;
    protected int|null     $callbackCode       = null;
    protected Closure|null $callbackError      = null;
    protected Closure|null $callbackSuccess    = null;


    public function __construct(string $endpoint = null, array $headers = [])
    {
        $this->endpoint = $endpoint;
        $this->headers = $headers;
    }

    public function error(Closure|null $error): static
    {
        $this->callbackError = $error;
        if ($this->callbackError && is_numeric($this->callbackCode) && ($this->callbackCode <> 200)) {
            $self = clone $this;
            $this->callbackCode = null;
            $this->callbackError = null;
            $this->callbackSuccess = null;
            $error($this->getResponse(), $self);
        }
        return $this;
    }

    public function success(Closure|null $success): static
    {
        $this->callbackSuccess = $success;
        if ($this->callbackSuccess && is_numeric($this->callbackCode) && ($this->callbackCode == 200)) {
            $self = clone $this;
            $this->callbackCode = null;
            $this->callbackError = null;
            $this->callbackSuccess = null;
            $success($this->getResponse(), $self);
        }
        return $this;
    }


    public function setEndPoint(string $endpoint): static
    {
        $this->endpoint = $endpoint;
        return $this;
    }

    public function getEndPoint(): string
    {
        return $this->endpoint;
    }

    public function setHeaders(array $headers = []): static
    {
        $this->headers = array_merge($this->getHeaders(), $headers);
        return $this;
    }

    public function getHeaders(): array
    {
        return array_merge($this->header(), $this->headers);
    }

    public function setCookies(array $cookies = []): static
    {
        $this->cookies = $cookies;
        return $this;
    }

    public function getCookies(): array
    {
        return $this->cookies;
    }

    public function setLogs(array $logs = []): static
    {
        $this->logs = $logs;
        return $this;
    }

    public function getLogs(): array
    {
        return $this->logs;
    }

    public function setErrorMessage(string|null $error): static
    {
        $this->error = $error;
        return $this;
    }

    public function getErrorMessage(): string|null
    {
        return $this->error;
    }

    public function setResponse(string $response): static
    {
        $this->response = $response;
        return $this;
    }

    public function getResponse(): object|null
    {
        return $this->response;
    }

    public function setSsl(bool $ssl): static
    {
        $this->ssl = $ssl;
        return $this;
    }

    public function getSsl(): bool
    {
        return $this->ssl;
    }

    public function setDtn(bool $dtn): static
    {
        $this->dtn = $dtn;
        return $this;
    }

    public function getDtn(): bool
    {
        return $this->dtn;
    }

    public function setVerbose(bool $verbose): static
    {
        $this->verbose = $verbose;
        return $this;
    }

    public function getVerbose(): bool
    {
        return $this->verbose;
    }

    public function setInsecureRequests(bool $insecureRequests): static
    {
        $this->insecureRequests = $insecureRequests;
        return $this;
    }

    public function getInsecureRequests(): bool
    {
        return $this->insecureRequests;
    }

    public function setReferer(string $referer): static
    {
        $this->referer = $referer;
        return $this;
    }

    public function getReferer(): string
    {
        return $this->referer;
    }

    public function setUserAgent(string $agent): static
    {
        $this->userAgent = $agent;
        return $this;
    }

    public function getUserAgent(): string
    {
        return $this->userAgent;
    }

    public function setContentType(string $contentType): static
    {
        $this->contentType = $contentType;
        return $this;
    }

    public function getContentType(): string
    {
        return $this->contentType;
    }

    public function setOrigin(string $origin): static
    {
        $this->origin = $origin;
        return $this;
    }

    public function getOrigin(): string
    {
        return $this->origin;
    }

    public function setHost(string $host): static
    {
        $this->host = $host;
        return $this;
    }

    public function getHost(): string
    {
        return $this->host;
    }

    public function setConnection(string $connection): static
    {
        $this->connection = $connection;
        return $this;
    }

    public function getConnection(): string
    {
        return $this->connection;
    }

    public function setCacheControl(string $cacheControl): static
    {
        $this->cacheControl = $cacheControl;
        return $this;
    }

    public function getCacheControl(): string
    {
        return $this->cacheControl;
    }

    public function setAccept(string $accept): static
    {
        $this->accept = $accept;
        return $this;
    }

    public function getAccept(): string
    {
        return $this->accept;
    }

    public function setAcceptEncoding(string $accept): static
    {
        $this->acceptEncoding = $accept;
        return $this;
    }

    public function getAcceptEncoding(): string
    {
        return $this->acceptEncoding;
    }

    public function get(string $uri, array  $params = []): static
    {
        $this->send($uri, $params, static::HTTP_GET);
        $this->error($this->callbackError);
        $this->success($this->callbackSuccess);
        return $this;
    }

    public function post(string $uri, array  $params = []): static
    {
        $this->send($uri, $params, static::HTTP_POST);
        $this->error($this->callbackError);
        $this->success($this->callbackSuccess);
        return $this;
    }

    public function put(string $uri, array  $params = []): static
    {
        $this->send($uri, $params, static::HTTP_PUT);
        $this->error($this->callbackError);
        $this->success($this->callbackSuccess);
        return $this;
    }

    public function delete(string $uri, array  $params = []): static
    {
        $this->send($uri, $params, static::HTTP_DELETE);
        $this->error($this->callbackError);
        $this->success($this->callbackSuccess);
        return $this;
    }

    protected function url(string $url, string $uri = '/', array $params = []): string
    {
        $expressao = '/(\/)\1+/';
        $replace = '$1';
        $url = preg_replace($expressao, $replace, $url . $uri);
        $http = parse_url($url, PHP_URL_SCHEME);
        $host = parse_url($url, PHP_URL_HOST);
        $port = parse_url($url, PHP_URL_PORT);
        $uri = parse_url($url, PHP_URL_PATH);
        return ($http . '://' . $host . ($port ? strval(':' . $port) : '') . $uri . (count($params) ? '?' . http_build_query($params) : ''));
    }

    protected function contentTypeJson(): bool
    {
        $contentJson = is_numeric(strpos(strtolower($this->contentType ?: ''), 'application/json'));
        $contentJson = $contentJson ?:  count(array_filter($this->getHeaders(), function ($value) {
            return is_numeric(strpos(strtolower($value), 'application/json'));
        }));
        return $contentJson;
    }

    protected function contentTypeForm(): bool
    {
        $contentForm = is_numeric(strpos(strtolower($this->contentType ?: ''), 'application/x-www-form-urlencoded'));
        $contentForm = $contentForm ?: count(array_filter($this->getHeaders(), function ($value) {
            return is_numeric(strpos(strtolower($value), 'application/x-www-form-urlencoded'));
        }));
        return $contentForm;
    }

    protected function response(string $response, int $headerSize): object
    {
        $header = substr($response, 0, $headerSize);
        $body = substr($response, $headerSize);
        $headerArray = array_filter(explode('\n', $header));
        preg_match_all('/^Set-Cookie:\s*([^;]*)/mi', $header, $matches);
        $cookies = [];
        foreach ($matches[1] as $item) {
            parse_str($item, $cookie);
            $cookies = array_merge($cookies, $cookie);
        }
        $http = new stdClass;
        $http->code = $this->callbackCode;
        $http->headers = $headerArray;
        $http->cookies = $cookies;
        $http->body = $body;
        return $http;
    }

    protected function header(): array
    {
        return [
            'Cache-Control' =>  strval($this->cacheControl ?: 'max-age=0'),
            'Connection' => strval($this->connection ?: 'keep-alive'),
            'DNT' => intval($this->dtn),
            'Host' => parse_url($this->origin ?: $this->endpoint ?: '', PHP_URL_HOST),
            'Origin' => strval($this->origin ?: $this->endpoint ?: ''),
            'Referer' => strval($this->referer ?: $this->endpoint ?: ''),
            'Cookie' => http_build_query($this->cookies ?: [], '', ';'),
            'Upgrade-Insecure-Requests' => intval($this->insecureRequests),
            'Content-Type' => strval($this->contentType ?: 'application/x-www-form-urlencoded'),
            'Accept-Encoding' => strval($this->acceptEncoding ?: 'gzip, deflate'),
            'Accept-Language' => strval($this->acceptLanguage ?: 'pt-PT,pt;q=0.9,en-US;q=0.8,en;q=0.7'),
            'User-Agent' => strval($this->userAgent ?: 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:107.0) Gecko/20100101 Firefox/107.0'),
            'Accept' => strval($this->accept ?: 'text/html,application/xhtml+xml,application/xml;q=0.9,image/avif,image/webp,image/apng,*/*;q=0.8,application/signed-exchange;v=b3;q=0.9'),
        ];
    }

    protected function send(string $uri, array $params = [], int $method = self::HTTP_GET): object|null
    {
        try {
            //http params
            $url = $this->url(
                $this->getEndPoint(),
                $uri,
                in_array($method, [static::HTTP_GET]) ? $params : []
            );
            $httpHeader = array_map(
                fn ($k,  $v) => (is_array($v) ? $v : trim($k) . ':' . trim($v)),
                array_keys($this->getHeaders()),
                $this->getHeaders()
            );
            $httpBody = $this
                ->contentTypeJson() ?
                json_encode($params) : http_build_query($params);
            //curl php
            $curlHeader = [
                CURLOPT_URL => $url,
                CURLOPT_HEADER => true,
                CURLOPT_POSTFIELDS => $httpBody,
                CURLOPT_HTTPHEADER => $httpHeader,
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_CUSTOMREQUEST => match ($method) {
                    static::HTTP_GET => 'GET',
                    static::HTTP_POST => 'POST',
                    static::HTTP_PUT => 'PUT',
                    static::HTTP_DELETE => 'DELETE',
                    default => 'GET'
                }
            ];

            $ch = curl_init();
            curl_setopt_array($ch, $curlHeader);
            curl_setopt($ch, CURLOPT_REFERER, strval($this->referer ?: $this->endpoint ?: ''));
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, intval($this->ssl));
            curl_setopt($ch, CURLOPT_VERBOSE, intval($this->verbose));
            curl_setopt($ch, CURLOPT_COOKIE, http_build_query($this->cookies ?: [], '', ';'));
            $response = curl_exec($ch);
            $headerSize = curl_getinfo($ch, CURLINFO_HEADER_SIZE);
            $this->callbackCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            $this->setLogs(curl_getinfo($ch));
            if ($response === false) {
                $this->setErrorMessage(curl_error($ch));
                curl_close($ch);
                return null;
            }
            curl_close($ch);
            return $this->response = $this->response($response, $headerSize);
        } catch (Throwable $e) {
            $this->callbackCode = 404;
            $this->setErrorMessage($e->getMessage());
            return null;
        }
        return null;
    }
}
