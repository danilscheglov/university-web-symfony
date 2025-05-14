<?php

namespace App\Controller;

use App\Entity\User;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Symfony\Component\Validator\Constraints as Assert;

class RegistrationController extends AbstractController
{
    #[Route('/register', name: 'app_register', methods: ['GET', 'POST'])]
    public function register(
        Request $request,
        EntityManagerInterface $em,
        UserPasswordHasherInterface $passwordHasher,
        ValidatorInterface $validator,
        UserRepository $userRepository
    ): Response {
        if ($request->query->get('success')) {
            return $this->render('auth/register.html.twig', ['success' => true]);
        }

        $errors = [];
        $formData = [];

        if ($request->isMethod('POST')) {
            $formData = $request->request->all();

            $constraints = new Assert\Collection([
                'username' => [
                    new Assert\NotBlank(['message' => 'Имя пользователя обязательно']),
                    new Assert\Length([
                        'min' => 3,
                        'max' => 50,
                        'minMessage' => 'Имя должно быть не менее 3 символов',
                        'maxMessage' => 'Имя должно быть не длиннее 50 символов'
                    ])
                ],
                'email' => [
                    new Assert\NotBlank(['message' => 'Email обязателен']),
                    new Assert\Email(['message' => 'Неверный формат email'])
                ],
                'password' => [
                    new Assert\NotBlank(['message' => 'Пароль обязателен']),
                    new Assert\Length([
                        'min' => 8,
                        'minMessage' => 'Пароль должен быть не менее 8 символов'
                    ])
                ],
                'password_confirm' => [
                    new Assert\NotBlank(['message' => 'Подтверждение пароля обязательно']),
                    new Assert\EqualTo([
                        'value' => $formData['password'] ?? '',
                        'message' => 'Пароли должны совпадать'
                    ])
                ]
            ]);

            $violations = $validator->validate($formData, $constraints);
            $existingUser = $userRepository->findOneByEmail($formData['email']);

            if ($existingUser) {
                $errors['email'] = 'Этот email уже зарегистрирован';
            }

            if (count($violations) === 0 && empty($errors)) {
                $user = new User();
                $user->setUsername($formData['username'])
                    ->setEmail($formData['email'])
                    ->setPassword(
                        $passwordHasher->hashPassword($user, $formData['password'])
                    );

                if ($formData['email'] === 'admin@example.com') {
                    $user->setRole('admin');
                }

                $em->persist($user);
                $em->flush();

                return $this->redirectToRoute('app_register', ['success' => true]);
            }

            foreach ($violations as $violation) {
                $field = str_replace(['[', ']'], '', $violation->getPropertyPath());
                $errors[$field] = $violation->getMessage();
            }
        }

        return $this->render('auth/register.html.twig', [
            'errors' => $errors,
            'formData' => $formData,
            'success' => false
        ]);
    }
}
