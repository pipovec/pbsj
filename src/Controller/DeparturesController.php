<?php declare(strict_types=1);

namespace App\Controller;

use Psr\Cache\InvalidArgumentException;
use Psr\Log\LoggerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Contracts\Cache\ItemInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Contracts\Cache\TagAwareCacheInterface;
use Symfony\Contracts\HttpClient\HttpClientInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

final class DeparturesController extends AbstractController
{
    public function __construct(
        private readonly HttpClientInterface $httpClient,
        private readonly TagAwareCacheInterface $cache,
        private readonly LoggerInterface $logger,
    ){}

    #[Route('/abgänge-von-spielern', name: 'app_departures')]
    public function index(Request $request): Response
    {
        if ($request->query->has('clear_cache')) {
            try {
                $this->cache->invalidateTags(['app_departures']);
            } catch (InvalidArgumentException $e) {
                $this->logger->warning('Unable to delete departures cache', ['exception' => $e]);
            }
        }

        $response = $this->cache->get('abgänge-von-spielern', function(ItemInterface $item) {
            $item->tag('app_departures');
            $item->expiresAfter(600);

            return $this->render('departures/index.html.twig', [
                'players' => $this->getPlayers()
            ]);
        });


        return $response;
    }


    private function getPlayers(): string
    {
        $response =  $this->httpClient->request(
            'GET',
            'https://api2.fpcstat.cz/api/departures-germany',
            [
                'query' => [
                    'page' => 1,
                    'order[wn8]' => 'desc',
                ]
            ]
        );

        return $response->getContent();
    }
}
