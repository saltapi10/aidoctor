<?php

declare(strict_types=1);

namespace App\Overrides;

use GeminiAPI\ClientInterface;
use GeminiAPI\Enums\MimeType;
use GeminiAPI\Enums\ModelName;
use GeminiAPI\Enums\Role;
use GeminiAPI\Laravel\ChatSession;
use GeminiAPI\Resources\Content;
use GeminiAPI\Resources\Parts\ImagePart;
use GeminiAPI\Resources\Parts\TextPart;

class CustomGemini extends \GeminiAPI\Laravel\Gemini
{
    private $modelName;

    public function __construct(
        private readonly ClientInterface $client
    ) {
        parent::__construct($client);
        $this->modelName = ModelName::GeminiPro15Flash;
    }

    public function generateText(string $prompt): string
    {
        $response = $this->client
            ->generativeModel($this->modelName)
            ->generateContent(
                new TextPart($prompt),
            );

        return $response->text();
    }

    public function generateTextUsingImage(
        string $imageType,
        string $image,
        string $prompt = '',
    ): string {
        $mimeType = CustomMimeType::tryFrom($imageType);
        if (is_null($mimeType)) {
            //throw InvalidMimeType::create($imageType);
        }

        $parts = [
            new CustomImagePart($mimeType, $image),
        ];

        if (! empty($prompt)) {
            $parts[] = new TextPart($prompt);
        }

        $response = $this->client
            ->generativeModel($this->modelName)
            ->generateContent(...$parts);

        return $response->text();
    }

    public function startChat(array $history = []): ChatSession
    {
        $chatSession = $this->client
            ->generativeModel($this->modelName)
            ->startChat();

        if (! empty($history)) {
            $contents = array_map(
                static function (array $message): Content {
                    if (empty($message['message']) || empty($message['role'])) {
                        //throw new InvalidArgumentException('Invalid message in the chat history');
                        die('Invalid message in the chat history');
                    }

                    if (! is_string($message['message']) || ! in_array($message['role'], ['user', 'model'], true)) {
                        //throw new InvalidArgumentException('Invalid message in the chat history');
                        die('Invalid message in the chat history');
                    }

                    return Content::text($message['message'], Role::from($message['role']));
                },
                $history,
            );
            $chatSession = $chatSession->withHistory($contents);
        }

        return new ChatSession($chatSession);
    }

    public function countTokens(string $prompt): int
    {
        $response = $this->client
            ->generativeModel($this->modelName)
            ->countTokens(
                new TextPart($prompt),
            );

        return $response->totalTokens;
    }


}
