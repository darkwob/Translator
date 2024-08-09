<?php

namespace Dcyilmaz\Translator\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;
use Dcyilmaz\Translator\Helpers\TranslateHelper;

class TranslateLanguage extends Command
{
    protected $signature = 'translate:language {source} {target} {file}';
    protected $description = 'Translate language files from source language to target language using Google Translate';

    public function __construct()
    {
        parent::__construct();
    }

    public function handle()
    {
        $source = $this->argument('source');
        $target = $this->argument('target');
        $file = $this->argument('file');

        $sourcePath = resource_path("lang/{$source}/{$file}.php");
        $targetPath = resource_path("lang/{$target}/{$file}.php");

        if (!File::exists($sourcePath)) {
            $this->error("Source language file not found: {$sourcePath}");
            return;
        }

        $this->createTargetDirectoryIfNotExists($target);

        $translations = File::getRequire($sourcePath);
        $translatedTexts = File::exists($targetPath) ? File::getRequire($targetPath) : [];

        foreach ($translations as $key => $text) {
            if (!array_key_exists($key, $translatedTexts)) {
                $translatedTexts[$key] = TranslateHelper::translate($text, $target);
                $this->info("Translated: {$text} => {$translatedTexts[$key]}");
            }
        }

        $this->saveTranslatedContent($targetPath, $translatedTexts);

        $this->info("Language file translated successfully: {$targetPath}");
    }

    protected function createTargetDirectoryIfNotExists($target)
    {
        $targetDir = resource_path("lang/{$target}");
        if (!File::isDirectory($targetDir)) {
            File::makeDirectory($targetDir, 0755, true);
        }
    }

    protected function saveTranslatedContent($targetPath, $translatedTexts)
    {
        $translatedContent = "<?php\n\nreturn " . var_export($translatedTexts, true) . ";\n";
        File::put($targetPath, $translatedContent);
    }
}
