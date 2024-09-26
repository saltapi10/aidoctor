<?php

namespace App\Providers;

use App\Overrides\CustomGemini;
use GeminiAPI\ClientInterface;
use GeminiAPI\Laravel\Gemini;
use Illuminate\Support\ServiceProvider;

use GeminiAPI\Resources\Parts\ImagePart;
use App\Overrides\CustomImagePart;
use GeminiAPI\Enums\MimeType;
use App\Overrides\CustomMimeType;
use Illuminate\Foundation\AliasLoader;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        // Bind the original Gemini class to your custom CustomGemini class
        $this->app->bind(Gemini::class, function ($app) {
            // Pass in the necessary dependencies (ClientInterface)
            return new CustomGemini($app->make(ClientInterface::class));
        });

        // Bind the ImagePart class to use the CustomImagePart
        $this->app->bind(ImagePart::class, CustomImagePart::class);

        // Bind the MimeType enum to use the CustomMimeType
        $this->app->bind(MimeType::class, CustomMimeType::class);
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        //
    }
}
