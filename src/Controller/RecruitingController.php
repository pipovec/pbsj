<?php declare(strict_types=1);

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

final class RecruitingController extends AbstractController
{
    #[Route('/rekrutierung', name: 'app_recruiting')]
    public function index(): Response
    {
        return $this->render('recruiting/index.html.twig', []);
    }
}
