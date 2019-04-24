<?php


namespace App\Helper;


use Symfony\Component\HttpFoundation\Request;

class ExtratorDadosRequest
{
    private function buscaDadosRequest(Request $request)
    {
        $filtros = $request->query->all();

        $ordenacao = array_key_exists('sort', $filtros) ? $filtros['sort'] : null;
        unset($filtros['sort']);

        $paginaAtual = array_key_exists('page', $filtros) ? $filtros['page'] : 1;
        unset($filtros['page']);

        $itensPorPagina = array_key_exists('itensPorPagina', $filtros) ? $filtros['itensPorPagina'] : 5;
        unset($filtros['itensPorPagina']);

        return [$ordenacao, $filtros, $paginaAtual, $itensPorPagina];
    }
    
    public function buscaDadosOrdenacao(Request $request)
    {
        [$ordenacao, ] = $this->buscaDadosRequest($request);

        return $ordenacao;
    }

    public function buscaDadosFiltros(Request $request)
    {
        [, $filtros] = $this->buscaDadosRequest($request);

        return $filtros;
    }

    public function buscaDadosPaginacao(Request $request)
    {
        [, , $paginaAtual, $itensPorPagina] = $this->buscaDadosRequest($request);

        return [$paginaAtual, $itensPorPagina];
    }
}