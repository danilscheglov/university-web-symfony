<?php

namespace App\Security;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use Symfony\Component\Security\Http\Authorization\AccessDeniedHandlerInterface;
use Twig\Environment;
use Twig\Error\LoaderError;
use Twig\Error\RuntimeError;
use Twig\Error\SyntaxError;

class AccessDeniedHandler implements AccessDeniedHandlerInterface
{
    public function __construct(
        protected Environment $twig
    )
    {
    }

    /**
     * @throws RuntimeError
     * @throws SyntaxError
     * @throws LoaderError
     */
    public function handle(Request $request, AccessDeniedException $accessDeniedException): Response
    {

        return new Response($this->twig->render('errors/403.html.twig'), 403);
    }
}