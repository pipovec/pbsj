<?php

namespace App\Controller;

use Psr\Cache\CacheItemInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Contracts\Cache\CacheInterface;
use Symfony\Contracts\HttpClient\HttpClientInterface;

class DeparturesController extends AbstractController
{
    public function __construct(
        private readonly HttpClientInterface $httpClient,
        private readonly CacheInterface $cache
    ){}

    #[Route('/abgänge-von-spielern', name: 'app_departures')]
    public function index(): Response
    {
        $players = $this->cache->get('abgänge-von-spielern', function(CacheItemInterface $item) {
            $item->expiresAfter(60);
            return $this->getPlayers();
        });


        return $this->render('departures/index.html.twig', [
            'players' => $players
        ]);
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
