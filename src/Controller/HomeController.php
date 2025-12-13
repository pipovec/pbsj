<?php declare(strict_types=1);

namespace App\Controller;

use Psr\Cache\InvalidArgumentException;
use Psr\Log\LoggerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Contracts\Cache\ItemInterface;
use Symfony\Contracts\Cache\TagAwareCacheInterface;

final class HomeController extends AbstractController
{
    public function __construct(
        private readonly TagAwareCacheInterface $cache,
        private readonly LoggerInterface $logger
    )
    {
    }

    #[Route('/', name: 'app_home')]
    public function index(Request $request): Response
    {
        if ($request->query->has('clear_cache')) {
            try {
                $this->cache->invalidateTags(['homepage']);
            } catch (InvalidArgumentException $e) {
                $this->logger->warning('Unable to delete homepage cache', ['exception' => $e]);
            }
        }

        return $this->cache->get('homepage', function (ItemInterface $item) {
            $item->tag('homepage');
            $item->expiresAfter(600);

            return $this->render('home/index.html.twig');
        });
    }
}
