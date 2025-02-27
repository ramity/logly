<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Contracts\HttpClient\HttpClientInterface;

final class InjestController extends AbstractController
{
    #[Route('/test', name: 'test', methods: ['GET'])]
    public function test(): JsonResponse
    {
        return $this->json(":3");
    }

    #[Route('/injest', name: 'injest', methods: ['POST'])]
    public function injest(Request $request): JsonResponse
    {
        $data = json_decode($request->getContent(), true);

        if (!$data)
        {
            return this->json(['error' => 'invalid request', 400]);
        }

        $llm_request = [
            "model" => "qwen2.5-coder:32b",
            "prompt" => $prompt,
            // Complete responses only - turns off streaming responses
            "stream" => false
        ];

        $externalApiUrl = 'http://logly_llm:11434/api/generate';

        // Make a POST request using HttpClient
        // Ex:
        // {
        //   "model": "llama3.2",
        //   "created_at": "2023-08-04T19:22:45.499127Z",
        //   "response": "The sky is blue because it is the color of the sky.",
        //   "done": true,
        //   "context": [1, 2, 3],
        //   "total_duration": 5043500667,
        //   "load_duration": 5025959,
        //   "prompt_eval_count": 26,
        //   "prompt_eval_duration": 325953000,
        //   "eval_count": 290,
        //   "eval_duration": 4709213000
        // }

        $response = $this->httpClient->request('POST', $externalApiUrl, [
            'json' => $data,
        ]);

        // Get the response content
        $responseData = $response->toArray();

        // Do something with the llm_response

        // If you want to return a response to the client
        return $this->json(["message" => "yippie :3"]);

        // return $this->render('injest/index.html.twig', [
        //     'controller_name' => 'InjestController',
        // ]);
    }
}
