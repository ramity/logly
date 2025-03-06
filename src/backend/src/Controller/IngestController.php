<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Contracts\HttpClient\HttpClientInterface;

use Symfony\Component\Process\Process;
use Symfony\Component\Process\Exception\ProcessFailedException;
use RecursiveIteratorIterator;
use RecursiveDirectoryIterator;

final class IngestController extends AbstractController
{
    public function __construct(HttpClientInterface $httpClient)
    {
        $this->httpClient = $httpClient;
    }

    #[Route('/test', name: 'test', methods: ['GET'])]
    public function test(): JsonResponse
    {
        return $this->json(':3');
    }

    #[Route('/ingest', name: 'ingest', methods: ['POST'])]
    public function ingest(Request $request): JsonResponse
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
        switch($data['error_type'])
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
                $repo_directory = '/example';
                if (!is_dir($repo_directory)) {
                    return $this->json('Example repo directory was not found.');;
                }

                // Get last filename from source parameter
                $source_bits = explode('/', $source);
                $source_filename = $source_bits[count($source_bits) - 1];

                // Does this directory contain a file with the file name specified?
                $file_path = '';
                $file_found = false;
                $iterator = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($repo_directory));
                foreach ($iterator as $file)
                {
                    if ($file->isFile() && $file->getFilename() === $source_filename)
                    {
                        $file_path = $file->getPathname();
                        $file_found = true;
                        break;
                    }
                }

                // File was not found
                // - In the future, try alternative methods like regex code search, possibly
                if (!$file_found)
                {
                    return $this->json('File was not found in example repo.' . $source_filename);
                }

                // Using source, get the complete file
                $source_code = file_get_contents($file_path);
                $prompt = "Don't explain the code, just generate the code block itself. Responding with the complete code, resolve the runtime error $message in the following code:\n$source_code";
                $llm_request = [
                    'model' => $_ENV['LOGLY_MODEL'],
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
                $llm_response = $this->httpClient->request('POST', $llm_url, ['json' => $llm_request, 'timeout' => 600]);
                $llm_response_data = $llm_response->toArray();

                // extract response from within first ```<code>``` and ```language<code>``` block
                $pattern = '/```.*?\n(.*?)```/s';
                $result = preg_match($pattern, $llm_response_data['response'], $matches);

                // Validate
                if ($result == 0)
                {
                    throw new Exception("No code found in the provided response.");
                }

                $new_source_code = $matches[1];
                file_put_contents($file_path, $new_source_code);

                $body = $llm_response_data['response'];

                $branch_name = time();
                // $gitWrapper = new GitWrapper();
                // $git = $gitWrapper->workingCopy($repo_directory);
                // $git->branch($branch_name);
                // $git->add($file_path);
                // $git->commit("Resolves $error on $file_path");
                // $git->push();

                $process = new Process(["git","checkout","-B","$branch_name"]);
                $process->setWorkingDirectory("$repo_directory");
                $process->run();
                if (!$process->isSuccessful()) {
                    throw new ProcessFailedException($process);
                }
                $process = new Process(["git","add","$file_path"]);
                $process->setWorkingDirectory("$repo_directory");
                $process->run();
                if (!$process->isSuccessful()) {
                    throw new ProcessFailedException($process);
                }
                $process = new Process(["git","commit","-m","Resolves $error on $file_path"]);
                $process->setWorkingDirectory("$repo_directory");
                $process->run();
                if (!$process->isSuccessful()) {
                    throw new ProcessFailedException($process);
                }
                $process = new Process(["git","push","origin","$branch_name"]);
                $process->setWorkingDirectory("$repo_directory");
                $process->run();
                if (!$process->isSuccessful()) {
                    throw new ProcessFailedException($process);
                }
                $process = new Process(["gh","pr","create","--title","Corrected $message","--body","$body"]);
                $process->setWorkingDirectory("$repo_directory");
                $process->run();
                if (!$process->isSuccessful()) {
                    throw new ProcessFailedException($process);
                }
                $process = new Process(["git","checkout","master"]);
                $process->setWorkingDirectory("$repo_directory");
                $process->run();
                if (!$process->isSuccessful()) {
                    throw new ProcessFailedException($process);
                }

                return $this->json($llm_response_data);
                break;

            case 'resource':
                break;

            case 'promise':
                break;

            default:
                return $this->json(['error' => 'Unhandled error_type']);
        }
        
        // If you want to return a response to the client
        // return $this->json(['message' => 'yippie :3']);

        // return $this->render('ingest/index.html.twig', [
        //     'controller_name' => 'IngestController',
        // ]);
    }
}
