<?php

namespace App\Controller;

use App\Entity\Car;
use App\Entity\User;
use App\Repository\CarRepository;
use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Throwable;

class UserController extends AbstractController
{
    public function __construct(
        private readonly UserRepository $userRepository,
    )
    {
    }

    #[Route('/admin/user', name: 'users.index', methods: ['GET'])]
    public function index(SessionInterface $session): Response
    {
        $this->checkAuth();

        $success = $session->getFlashBag()->get('success');
        if($success && isset($success[0]))
            $success = $success[0];

        $error = $session->getFlashBag()->get('error');
        if($error && isset($error[0]))
            $error = $error[0];

        return $this->render('user/list.html.twig', [
            'users' => $this->userRepository->findAll(),
            'success' => $success,
            'error' => $error,
        ]);
    }

    #[Route('/admin/{user}/role', name: 'users.role', methods: ['POST'])]
    public function role(User $user, Request $request, SessionInterface $session): Response
    {
        try {
            $this->checkAuth();

            $role = $request->get('role');
            $user->setRole($role);
            $this->userRepository->save($user);
        }
        catch (Throwable $e) {
            $session->getFlashBag()->add('error', $e->getMessage());
            return $this->redirectToRoute('users.index');
        }

        $session->getFlashBag()->add('success', 'Роль пользователя <b>'.$user->getUsername().'</b> была изменена на '.($role == 'admin'?'Администратор':'Пользователь').'.');
        return $this->redirectToRoute('users.index');
    }

    #[Route('/admin/{user}', name: 'users.destroy', methods: ['DELETE'])]
    public function destroy(User $user, SessionInterface $session): Response
    {
        try {
            $this->checkAuth();

            $this->userRepository->remove($user);
        }
        catch (Throwable $e) {
            $session->getFlashBag()->add('error', $e->getMessage());
            return $this->redirectToRoute('users.index');
        }

        $session->getFlashBag()->add('success', 'Пользователь <b>'.$user->getUsername().'</b> удален.');
        return $this->redirectToRoute('users.index');
    }

    protected function checkAuth(): void
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        $this->denyAccessUnlessGranted('ROLE_ADMIN');
    }
}