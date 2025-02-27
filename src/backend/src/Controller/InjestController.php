<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Contracts\HttpClient\HttpClientInterface;

use GitWrapper\GitWrapper;

final class InjestController extends AbstractController
{
    #[Route('/test', name: 'test', methods: ['GET'])]
    public function test(): JsonResponse
    {
        return $this->json(':3');
    }

    #[Route('/injest', name: 'injest', methods: ['POST'])]
    public function injest(Request $request): JsonResponse
    {
        $data = json_decode($request->getContent(), true);

        // validate decode
        if (!$data)
        {
            return $this->json(['error' => 'invalid request']);
        }

        // validate error type
        if (!$data['error_type'])
        {
            return $this->json(['error' => 'invalid request']);
        }

        // Conditionally switch for custom $prompt
        $prompt = '';
        switch($data->error_type)
        {
            case 'runtime':

                // Validate expected request properties for a runtime error
                $message = $data['message'];
                $source = $data['source'];
                $lineno = $data['lineno'];
                $colno = $data['colno'];
                $error = $data['error'];

                if (!$message)
                {
                    $this->json('Runtime event missing message parameter.');
                }
                if (!$source)
                {
                    $this->json('Runtime event missing source parameter.');
                }
                if (!$lineno)
                {
                    $this->json('Runtime event missing lineno parameter.');
                }
                if (!$colno)
                {
                    $this->json('Runtime event missing colno parameter.');
                }
                if (!$error)
                {
                    $this->json('Runtime event missing error parameter.');
                }

                // Assume the repo is at this specific location for now:
                $repo_directory = '/example/';

                // Get last filename from source parameter
                $source_bits = explode("/", $source);
                $source_filename = $source[count($source_bits) - 1];

                // Does this directory contain a file with the file name specified?
                $file_found = false;
                $directories = array_filter(glob($repo_directory . '*'), 'is_dir');
                foreach ($directories as $directory)
                {
                    if (file_exists($directory . '/' . $source_filename)) {
                        $file_path = $directory . '/' . $source_filename;
                        $file_found = true;
                        break;
                    }
                }

                // File was not found
                // - In the future, try alternative methods like regex code search, possibly
                if (!$file_found)
                {
                    $this->json('File was not found in example repo.');
                }

                // Using source, get the complete file
                $source_code = file_get_contents($file_path);
                $prompt = "Generate the code to fix to resolve the javascript runtime error $message in the following code:\n$source_code";
                $llm_request = [
                    'model' => 'qwen2.5-coder:32b',
                    'prompt' => $prompt,
                    // Complete responses only - turn off streaming responses
                    'stream' => false
                ];
        
                $llm_url = 'http://logly_llm:11434/api/generate';
        
                // Make a POST request using HttpClient
                // Ex:
                // {
                //   'model': 'llama3.2',
                //   'created_at': '2023-08-04T19:22:45.499127Z',
                //   'response': 'The sky is blue because it is the color of the sky.',
                //   'done': true,
                //   'context': [1, 2, 3],
                //   'total_duration': 5043500667,
                //   'load_duration': 5025959,
                //   'prompt_eval_count': 26,
                //   'prompt_eval_duration': 325953000,
                //   'eval_count': 290,
                //   'eval_duration': 4709213000
                // }
                $llm_response = $this->httpClient->request('POST', $llm_url, $llm_request);

                // Get the response content
                $llm_response_data = $llm_response->toArray();
                $new_source_code = $llm_response_data['response'];
                file_put_contents($file_path, $new_source_code);

                $branch_name = time();
                $gitWrapper = new GitWrapper();
                $git = $gitWrapper->workingCopy($repo_directory);
                $git->branch($branch_name);
                $git->add($file_path);
                $git->commit("Resolves $error on $file_path");
                $git->push();

                return $this->json($llm_response);
                break;

            case 'resource':
                break;

            case 'promise':
                break;

            default:
                return $this->json(['error' => 'Unhandled error_type']);
        }
        // If you want to return a response to the client
        return $this->json(['message' => 'yippie :3']);

        // return $this->render('injest/index.html.twig', [
        //     'controller_name' => 'InjestController',
        // ]);
    }
}
