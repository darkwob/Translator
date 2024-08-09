<?php

namespace Dcyilmaz\Translator\Helpers;

use Google\Cloud\Translate\V2\TranslateClient;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

class TranslateHelper
{
    public static function translate($text, $targetLanguage = 'tr')
    {
        // Cache translation to avoid redundant API calls
        $cacheKey = "translation_{$targetLanguage}_" . md5($text);
        return Cache::remember($cacheKey, 3600, function () use ($text, $targetLanguage) {
            try {
                $translate = new TranslateClient([
                    'key' => env('GOOGLE_TRANSLATE_API_KEY'),
                ]);

                $result = $translate->translate($text, [
                    'target' => $targetLanguage,
                ]);

                return $result['text'];
            } catch (\Exception $e) {
                // Log the error and return the original text if translation fails
                Log::error("Translation failed: " . $e->getMessage());
                return $text;
            }
        });
    }
}
