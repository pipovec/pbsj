<?php declare(strict_types=1);

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Contracts\HttpClient\HttpClientInterface;

final class ChatController extends AbstractController
{
    public function __construct(
        private readonly HttpClientInterface $httpClient,
        private readonly string $api3Url,
        private readonly string $chatApiKey,
    ) {}

    #[Route('/pinnwand', name: 'app_chat')]
    public function index(): Response
    {
        return $this->render('chat/index.html.twig');
    }

    #[Route('/api/chat/messages', name: 'api_chat_messages_get', methods: ['GET'])]
    public function getMessages(Request $request): JsonResponse
    {
        $response = $this->httpClient->request('GET', $this->api3Url . '/api/messages', [
            'query' => $request->query->all(),
        ]);

        return new JsonResponse($response->getContent(), $response->getStatusCode(), [], true);
    }

    #[Route('/api/chat/messages', name: 'api_chat_messages_post', methods: ['POST'])]
    public function postMessage(Request $request): JsonResponse
    {
        $response = $this->httpClient->request('POST', $this->api3Url . '/api/messages', [
            'headers' => [
                'Content-Type' => 'application/json',
                'X-Api-Key' => $this->chatApiKey,
            ],
            'body' => $request->getContent(),
        ]);

        return new JsonResponse($response->getContent(), $response->getStatusCode(), [], true);
    }

    #[Route('/api/chat/replies/{id}', name: 'api_chat_replies', methods: ['GET'])]
    public function getReplies(string $id): JsonResponse
    {
        $response = $this->httpClient->request('GET', $this->api3Url . '/api/messages/' . $id . '/replies');

        return new JsonResponse($response->getContent(), $response->getStatusCode(), [], true);
    }
}
