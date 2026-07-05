<?php declare(strict_types=1);

namespace App\Controller;

use Psr\Cache\InvalidArgumentException;
use Psr\Log\LoggerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Contracts\Cache\ItemInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Contracts\Cache\TagAwareCacheInterface;
use Symfony\Contracts\HttpClient\Exception\ExceptionInterface;
use Symfony\Contracts\HttpClient\HttpClientInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

final class DeparturesController extends AbstractController
{
    private const WOT_APP_ID = '883ff6ceefb13177357ffea34d6fb06f';

    public function __construct(
        private readonly HttpClientInterface $httpClient,
        private readonly TagAwareCacheInterface $cache,
        private readonly LoggerInterface $logger,
        private readonly string $api2LoginNickname,
        private readonly string $api2LoginPassword,
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

    #[Route('/api/departures/players/{accountId}/mark-deleted', name: 'api_departures_mark_deleted', methods: ['POST'], requirements: ['accountId' => '\d+'])]
    public function markPlayerDeleted(int $accountId): JsonResponse
    {
        if (!$this->isReallyDeletedInWot($accountId)) {
            return new JsonResponse(['deleted' => false, 'reason' => 'not_deleted'], Response::HTTP_CONFLICT);
        }

        try {
            $token = $this->getApi2JwtToken();

            $response = $this->httpClient->request(
                'DELETE',
                "https://api2.fpcstat.cz/api/players_alls/{$accountId}",
                ['headers' => ['Authorization' => 'Bearer ' . $token]]
            );

            $status = $response->getStatusCode();
        } catch (ExceptionInterface $e) {
            $this->logger->error('Failed to mark player as deleted on API2', ['accountId' => $accountId, 'exception' => $e]);

            return new JsonResponse(['deleted' => false, 'reason' => 'upstream_error'], Response::HTTP_BAD_GATEWAY);
        }

        if ($status !== Response::HTTP_NO_CONTENT) {
            $this->logger->error('API2 rejected player delete', ['accountId' => $accountId, 'status' => $status]);

            return new JsonResponse(['deleted' => false, 'reason' => 'upstream_error'], Response::HTTP_BAD_GATEWAY);
        }

        try {
            $this->cache->invalidateTags(['app_departures']);
        } catch (InvalidArgumentException $e) {
            $this->logger->warning('Unable to delete departures cache', ['exception' => $e]);
        }

        return new JsonResponse(['deleted' => true]);
    }

    private function isReallyDeletedInWot(int $accountId): bool
    {
        try {
            $response = $this->httpClient->request(
                'GET',
                'https://api.worldoftanks.eu/wot/account/info/',
                [
                    'query' => [
                        'application_id' => self::WOT_APP_ID,
                        'account_id' => $accountId,
                    ],
                ]
            );

            $data = $response->toArray();
        } catch (ExceptionInterface $e) {
            $this->logger->error('Failed to verify player deletion via WoT API', ['accountId' => $accountId, 'exception' => $e]);

            return false;
        }

        return ($data['status'] ?? null) === 'ok' && array_key_exists((string) $accountId, $data['data'] ?? [])
            && $data['data'][(string) $accountId] === null;
    }

    private function getApi2JwtToken(): string
    {
        $response = $this->httpClient->request('POST', 'https://api2.fpcstat.cz/api/login', [
            'json' => [
                'nickname' => $this->api2LoginNickname,
                'password' => $this->api2LoginPassword,
            ],
        ]);

        return $response->toArray()['token'];
    }
}
