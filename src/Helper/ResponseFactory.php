<?php

namespace App\Helper;

use Symfony\Component\HttpFoundation\JsonResponse;

class ResponseFactory
{
    private $sucesso;
    private $paginaAtual;
    private $itensPorPagina;
    private $conteudoResposta;
    private $status;

    public function __construct(
        bool $sucesso,
        $conteudoResposta,
        int $status = 200,
        int $paginaAtual = null,
        int $itensPorPagina = null
    ) {
        $this->sucesso = $sucesso;
        $this->paginaAtual = $paginaAtual;
        $this->itensPorPagina = $itensPorPagina;
        $this->conteudoResposta = $conteudoResposta;
        $this->status = $status;
    }

    public function getResponse() : JsonResponse
    {
        $resposta = [
            'sucesso' => $this->sucesso,
            'paginaAtual' => $this->paginaAtual,
            'itensPorPagina' => $this->itensPorPagina,
            'conteudoResposta' => $this->conteudoResposta
        ];

        if (is_null($this->paginaAtual)) {
            unset($resposta['paginaAtual']);
            unset($resposta['itensPorPagina']);
        }

        return new JsonResponse($resposta, $this->status);
    }
}