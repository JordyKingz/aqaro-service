<?php

namespace App\Models\Enums;

enum FileType: string
{
    case IMAGE = 'image';
    case DOCUMENT = 'document';
    case VIDEO = 'video';
    case AUDIO = 'audio';
    case OTHER = 'other';

    public static function fromMimeType(string $mimeType): self
    {
        $type = explode('/', $mimeType)[0];
        if ($type === 'image') {
            return self::IMAGE;
        }
        if ($type === 'application') {
            return self::DOCUMENT;
        }
        if ($type === 'video') {
            return self::VIDEO;
        }
        if ($type === 'audio') {
            return self::AUDIO;
        }
        return self::OTHER;
    }
}
