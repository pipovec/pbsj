<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class RecruitingController extends AbstractController
{
    #[Route('/recruiting', name: 'app_recruiting')]
    public function index(): Response
    {
        return $this->render('recruiting/index.html.twig', []);
    }
}
