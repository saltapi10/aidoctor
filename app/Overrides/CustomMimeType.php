<?php

declare(strict_types=1);

namespace App\Overrides;

enum CustomMimeType: string
{
    case IMAGE_PNG = 'image/png';
    case IMAGE_JPEG = 'image/jpeg';
    case IMAGE_HEIC = 'image/heic';
    case IMAGE_HEIF = 'image/heif';
    case IMAGE_WEBP = 'image/webp';

    // Add any custom MIME types
    case PDF = 'application/pdf';
    case DOCX = 'application/vnd.openxmlformats-officedocument.wordprocessingml.document';

    // Define a default case
    case DEFAULT = 'application/octet-stream';  // Generic binary file type
}
