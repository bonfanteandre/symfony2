<?php

namespace App\Controller;

use App\Helper\EntidadeFactory;
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

    public function __construct(
        ObjectRepository $repository,
        EntityManagerInterface $entityManager,
        EntidadeFactory $factory
    ) {
        $this->repository = $repository;
        $this->entityManager = $entityManager;
        $this->factory = $factory;
    }

    public function buscarTodos(Request $request) : Response
    {
        $ordenacao = $request->query->get('sort');
        $entities = $this->repository->findBy([], $ordenacao);
        return new JsonResponse($entities);
    }

    public function buscarUm(int $id) : Response
    {
        return new JsonResponse($this->repository->find($id));
    }

    public function nova(Request $request) : Response
    {
        $dadosRequest = $request->getContent();
        $entidade = $this->factory->criarEntidade($dadosRequest);

        $this->entityManager->persist($entidade);
        $this->entityManager->flush();

        return new JsonResponse($entidade, Response::HTTP_CREATED);
    }

    public function atualiza(int $id, Request $request) : Response
    {
        $corpoRequisicao = $request->getContent();
        $entidadeEnviada = $this->factory->criarEntidade($corpoRequisicao);

        $entidadeExistente = $this->repository->find($id);
        if (is_null($entidadeExistente)) {
            return new Response('', Response::HTTP_NOT_FOUND);
        }

        $this->atualizarEntidadeExistente($entidadeExistente, $entidadeEnviada);

        $this->entityManager->flush();

        return new JsonResponse($entidadeExistente);
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
