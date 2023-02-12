<?php

namespace App\Controller\Api;

use App\Entity\User;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Validator\Constraints\Collection;
use Symfony\Component\Validator\Constraints\Email;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\NotNull;
use Symfony\Component\Validator\Validator\ValidatorInterface;

#[Route('/api/register', name: 'api_register')]
class ApiRegisterController extends AbstractController
{
    private EntityManagerInterface $entityManager;
    private UserRepository $repository;
    private ValidatorInterface $validator;

    public function __construct(EntityManagerInterface $entityManager, UserRepository $repository, ValidatorInterface $validator)
    {
        $this->entityManager = $entityManager;
        $this->repository = $repository;
        $this->validator = $validator;
    }

    public function __invoke(Request $request, UserPasswordHasherInterface $passwordHasher): Response
    {
        $constraints = new Collection([
            'password' => [
                new NotBlank(),
            ],
            'email' => [
                new NotBlank(),
                new Email(),
                new NotNull()
            ],
        ]);
        $validationResult = $this->validator->validate($request->request->all(), $constraints);
        if(count($validationResult) > 0)
        {
            $messages = [];
            foreach ($validationResult as $error)
            {
                $messages [str_replace(['[', ']'], '' ,$error->getPropertyPath())]= $error->getMessage();
            }
            return new JsonResponse($messages, 400);
        }
        $email = $request->get('email');
        $plaintextPassword = $request->get('password');
        $existentUser = $this->repository->findBy(['email' => $email]);
        if(count($existentUser) > 0)
        {
            return new JsonResponse(['message' => 'Email taken!'], 400);
        }

        $user = new User();
        $user->setEmail($email);
        $user->setPassword($passwordHasher->hashPassword(
            $user,
            $plaintextPassword
        ));
        $this->entityManager->persist($user);
        $this->entityManager->flush();
        return new JsonResponse(['data' => 'success'], 200);
    }
}
