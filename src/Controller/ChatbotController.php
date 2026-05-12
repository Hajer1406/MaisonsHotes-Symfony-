<?php

namespace App\Controller;

use App\Repository\MaisonRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Contracts\HttpClient\HttpClientInterface;

class ChatbotController extends AbstractController
{
    public function __construct(
        private HttpClientInterface $client,
        private MaisonRepository $maisonRepository
    ) {}

    #[Route('/api/chatbot', name: 'api_chatbot', methods: ['POST'])]
    public function chat(Request $request): JsonResponse
    {
        $data = json_decode($request->getContent(), true);
        $messages = $data['messages'] ?? [];

        if (empty($messages)) {
            return new JsonResponse(['error' => 'No messages provided'], 400);
        }

        // ✅ Récupère les vraies maisons depuis la BDD
        $maisons = $this->maisonRepository->findAll();
        $maisonsList = '';
        foreach ($maisons as $maison) {
            $maisonsList .= sprintf(
                "- %s | Ville: %s | Prix: %d TND/nuit | Statut: %s | Description: %s\n",
                $maison->getTitle(),
                $maison->getCity(),
                $maison->getPrice(),
                'Disponible',
                substr($maison->getDescription() ?? '', 0, 100)
            );
        }

        $systemPrompt = "Tu es Yasmine, assistante chaleureuse des maisons d'hôtes en Tunisie.
- Réponds UNIQUEMENT en te basant sur les données réelles ci-dessous.
- Ne jamais inventer de prix, de maisons ou d'informations.
- Si une info n'est pas dans les données, dis que tu vas vérifier.
- Réponds en français en 2-4 phrases courtes.
- Utilise parfois des mots tunisiens : 'marhba', 'yeslek'.
- Pour les réservations, oriente vers le formulaire du site.

=== MAISONS DISPONIBLES ===
{$maisonsList}
=== FIN DES DONNÉES ===";

        try {
            $response = $this->client->request('POST', 'https://api.groq.com/openai/v1/chat/completions', [
                'headers' => [
                    'Authorization' => 'Bearer ' . $this->getParameter('groq_api_key'),
                    'Content-Type'  => 'application/json',
                ],
                'json' => [
                    'model'    => 'llama-3.1-8b-instant',
                    'messages' => array_merge(
                        [['role' => 'system', 'content' => $systemPrompt]],
                        $messages
                    ),
                    'max_tokens' => 500,
                ],
            ]);

            $result = $response->toArray();
            return new JsonResponse(['reply' => $result['choices'][0]['message']['content']]);

        } catch (\Exception $e) {
            return new JsonResponse(['reply' => "Désolée, problème technique. Réessayez dans un instant. 🙏"], 200);
        }
    }
}