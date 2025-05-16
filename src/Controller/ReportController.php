<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class ReportController extends AbstractController {
    #[Route('/report/pdf', name: 'report_pdf', methods: ['GET'])]
    public function report_pdf(): Response
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');

        return $this->render('car/list.html.twig');
    }
    #[Route('/report/excel', name: 'report_excel', methods: ['GET'])]
    public function report_excel(): Response
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');

        return $this->render('car/list.html.twig');
    }
    #[Route('/report/csv', name: 'report_csv', methods: ['GET'])]
    public function report_csv(): Response
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');

        return $this->render('car/list.html.twig');
    }
}

