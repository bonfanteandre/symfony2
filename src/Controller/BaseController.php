<?php

namespace App\Controller;

use App\Helper\EntidadeFactory;
use App\Helper\ExtratorDadosRequest;
use App\Helper\ResponseFactory;
use Doctrine\Common\Persistence\ObjectRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

abstract class BaseController extends AbstractController
{
    protected $repository;

    protected $entityManager;

    protected $factory;
    /**
     * @var ExtratorDadosRequest
     */
    private $extratorDadosRequest;

    public function __construct(
        ObjectRepository $repository,
        EntityManagerInterface $entityManager,
        EntidadeFactory $factory,
        ExtratorDadosRequest $extratorDadosRequest
    ) {
        $this->repository = $repository;
        $this->entityManager = $entityManager;
        $this->factory = $factory;
        $this->extratorDadosRequest = $extratorDadosRequest;
    }

    public function buscarTodos(Request $request) : Response
    {
        $ordenacao = $this->extratorDadosRequest->buscaDadosOrdenacao($request);
        $filtros = $this->extratorDadosRequest->buscaDadosFiltros($request);
        [$paginaAtual, $itensPorPagina] = $this->extratorDadosRequest->buscaDadosPaginacao($request);
        $entities = $this->repository->findBy(
            $filtros,
            $ordenacao,
            $itensPorPagina,
            ($paginaAtual - 1) * $itensPorPagina
        );

        $fabricaResposta = new ResponseFactory(true, $entities, Response::HTTP_OK, $paginaAtual, $itensPorPagina);
        return $fabricaResposta->getResponse();
    }

    public function buscarUm(int $id) : Response
    {
        $entidade = $this->repository->find($id);
        $status = is_null($entidade) ? Response::HTTP_NO_CONTENT : Response::HTTP_OK;
        $fabricaResposta = new ResponseFactory(true, $entidade, $status);
        return $fabricaResposta->getResponse();
    }

    public function nova(Request $request) : Response
    {
        $dadosRequest = $request->getContent();
        $entidade = $this->factory->criarEntidade($dadosRequest);

        $this->entityManager->persist($entidade);
        $this->entityManager->flush();

        $fabricaConexoes = new ResponseFactory(true, $entidade, Response::HTTP_CREATED);
        return $fabricaConexoes->getResponse();
    }

    public function atualiza(int $id, Request $request) : Response
    {
        $corpoRequisicao = $request->getContent();
        $entidadeEnviada = $this->factory->criarEntidade($corpoRequisicao);

        $entidadeExistente = $this->repository->find($id);
        if (is_null($entidadeExistente)) {
            $fabricaResposta = new ResponseFactory(false, 'Recurso não encontrado', Response::HTTP_NOT_FOUND);
            return $fabricaResposta->getResponse();
        }

        $this->atualizarEntidadeExistente($entidadeExistente, $entidadeEnviada);
        $this->entityManager->flush();

        $fabricaResposta = new ResponseFactory(true, $entidadeExistente, Response::HTTP_OK);
        return $fabricaResposta->getResponse();
    }

    public function remover(int $id) : Response
    {
        $entidade = $this->repository->find($id);

        if (is_null($entidade)) {
            return new Response('', Response::HTTP_NOT_FOUND);
        }

        $this->entityManager->remove($entidade);
        $this->entityManager->flush();

        return new Response('', Response::HTTP_NO_CONTENT);
    }

    abstract public function atualizarEntidadeExistente($entidadeExistente, $entidadeEnviada);
}
