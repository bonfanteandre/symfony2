<?php

namespace App\Controller;

use App\Repository\UserRepository;
use Firebase\JWT\JWT;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class LoginController extends AbstractController
{
    /**
     * @var UserRepository
     */
    private $userRepository;
    /**
     * @var UserPasswordEncoderInterface
     */
    private $encoder;

    public function __construct(
        UserRepository $userRepository,
        UserPasswordEncoderInterface $encoder
    ) {
        $this->userRepository = $userRepository;
        $this->encoder = $encoder;
    }

    /**
     * @Route("/login", name="login")
     */
    public function index(Request $request)
    {
        $dadosJson = json_decode($request->getContent());

        if (is_null($dadosJson->usuario) || is_null($dadosJson->senha)) {
            return new JsonResponse(['erro' => 'Informe as credenciais'], Response::HTTP_BAD_REQUEST);
        }

        $user = $this->userRepository->findOneBy([
            'username' => $dadosJson->usuario
        ]);

        if (is_null($user)) {
            return new JsonResponse(['erro' => 'Usuário ou senha inválidos'], Response::HTTP_UNAUTHORIZED);
        }

        $senhaValida = $this->encoder->isPasswordValid($user, $dadosJson->senha);
        if (!$senhaValida) {
            return new JsonResponse('Usuário ou senha inválidos', Response::HTTP_UNAUTHORIZED);
        }

        $token = JWT::encode(['username' => $user->getUsername()], 'chave');

        return new JsonResponse(['access_token' => $token], Response::HTTP_OK);
    }
}
