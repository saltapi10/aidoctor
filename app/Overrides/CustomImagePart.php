<?php

declare(strict_types=1);

namespace App\Overrides;

use GeminiAPI\Resources\Parts\PartInterface;
use function json_encode;

// Use the custom enum

class CustomImagePart implements PartInterface, \JsonSerializable
{
    public function __construct(
        public readonly CustomMimeType $mimeType,
        public readonly string $data,
    ) {
    }

    /**
     * @return array{
     *     inlineData: array{
     *         mimeType: string,
     *         data: string,
     *     },
     * }
     */
    public function jsonSerialize(): array
    {
        return [
            'inlineData' => [
                'mimeType' => $this->mimeType->value,
                'data' => $this->data,
            ],
        ];
    }

    public function __toString(): string
    {
        return json_encode($this) ?: '';
    }
}
