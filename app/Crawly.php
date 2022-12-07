<?php

namespace App;

use App\Http;
use App\Html;
use Exception;

class Crawly
{
    private const ENDPOINT_URL = 'http://applicant-test.us-east-1.elasticbeanstalk.com/';
    private const TOKEN_REPLACEMENTS = [
        'a' => "\x7a",
        'b' => "\x79",
        'c' => "\x78",
        'd' => "\x77",
        'e' => "\x76",
        'f' => "\x75",
        'g' => "\x74",
        'h' => "\x73",
        'i' => "\x72",
        'j' => "\x71",
        'k' => "\x70",
        'l' => "\x6f",
        'm' => "\x6e",
        'n' => "\x6d",
        'o' => "\x6c",
        'p' => "\x6b",
        'q' => "\x6a",
        'r' => "\x69",
        's' => "\x68",
        't' => "\x67",
        'u' => "\x66",
        'v' => "\x65",
        'w' => "\x64",
        'x' => "\x63",
        'y' => "\x62",
        'z' => "\x61",
        '0' => "\x39",
        '1' => "\x38",
        '2' => "\x37",
        '3' => "\x36",
        '4' => "\x35",
        '5' => "\x34",
        '6' => "\x33",
        '7' => "\x32",
        '8' => "\x31",
        '9' => "\x30"
    ];

    private Http $http;
    private Html $html;

    public function __construct()
    {
        $this->html = new Html();
        $this->http = new Http(static::ENDPOINT_URL);
    }

    /**
     * FLUXOS:
     * 
     * Fluxo 1 - Faz requisição http buscando a pagina principal.
     * Fluxo 2 - Pega html da pagina, e busca inputs.
     * Fluxo 3 - Pega o token do formulario principal e faz o parse para novo token.
     * Fluxo 4 - Faz requisição http buscando a responsta com novo token.
     * Fluxo 5 - Exibe respospota como solicitado do teste Crawly.
     * 
     * EXCEÇÕES
     * 
     * Excessão 1 - Trata exceções http da busca da pagina principal
     * Excessão 2 - Trata exceções http da busca da responsta para exibição
     * 
     */

    /**
     * Executa fluxo principal do teste
     * 
     */
    public function run(): bool
    {
        //Fluxo 1
        $this->http->get('/')
            ->error(function ($response, $request) {
                //Excessão 1
                return throw (new Exception(
                    $request->getErrorMessage() ?: 'Falha ao carregar html formulário principal',
                    $response?->code ?: 404
                ));
            })
            ->success(function ($response) {
                die($response->body);
                //Fluxo 2
                $inputsValues = $this->html
                    ->setHtml($response->body)
                    ->input('text', true) ?: [];
                $token = $inputsValues['token'] ?? '';
                //Fluxo 3
                $tokenAnswer = $this->findTokenAnswer($token);
                //Fluxo 4
                $this->http
                    ->setDtn(true)
                    ->setVerbose(true)
                    ->setCookies($response?->cookies ?: [])
                    ->post('/', ['token' => $tokenAnswer])
                    ->error(function ($response, $request) {
                        //Excessão 2
                        return throw (new Exception(
                            $request->getErrorMessage() ?: '',
                            $response?->code ?: 404
                        ));
                    })
                    ->success(function ($response, $http) {
                        //Fluxo 5
                        $resposta = $this->html
                            ->setHtml($response->body)
                            ->text('span');
                        print_r('<div align="center" style="margin:50px !important">A RESPOSTA É : <b>' .  $resposta . '</b></div>' . PHP_EOL);
                    });
            });

        return true;
    }

    /**
     * Execut parse do token da view principal.
     *
     * @param string  $token
     * @return string
     */
    public function findTokenAnswer(string $token): string
    {
        $tokenList = str_split(trim($token));
        $tokenReplacements = array_change_key_case(static::TOKEN_REPLACEMENTS, CASE_LOWER);
        $tokenListNew = array_map(fn ($value) => ($tokenReplacements[strtolower((string)trim($value))] ?? $value), $tokenList);
        return implode('', $tokenListNew);
    }
}
