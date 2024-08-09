<?php

namespace Dcyilmaz\Translator;

use Illuminate\Support\ServiceProvider;
use Dcyilmaz\Translator\Commands\TranslateLanguage;
use Illuminate\Support\Facades\Config;
use Symfony\Component\Console\Output\ConsoleOutput;

class TranslatorServiceProvider extends ServiceProvider
{
    public function boot()
    {
        $this->app->singleton('translator', function ($app) {
            return new Translator();
        });
    }

    public function register()
    {
        if ($this->app->runningInConsole()) {
            $this->commands([
                TranslateLanguage::class,
            ]);
        }

        $this->checkGoogleTranslateApiKey();
    }

    protected function checkGoogleTranslateApiKey()
    {
        if (empty(Config::get('services.google_translate_api_key'))) {
            $this->warnEnvVariable();
        }
    }

    protected function warnEnvVariable()
    {
        $output = new ConsoleOutput();
        $output->writeln('<comment>Please set the GOOGLE_TRANSLATE_API_KEY in your .env file.</comment>');
    }
}
