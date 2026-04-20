<?php

declare(strict_types=1);

namespace KirimEmail\Smtp\Laravel;

use Illuminate\Mail\MailManager;
use Illuminate\Support\ServiceProvider;
use KirimEmail\Smtp\Client\SmtpClient;
use KirimEmail\Smtp\Api\MessagesApi;

class KirimEmailServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->mergeConfigFrom(
            __DIR__ . "/../../config/kirimemail.php",
            "kirimemail",
        );

        $this->app->singleton(SmtpClient::class, function ($app) {
            $config = $app["config"]["kirimemail"];

            return new SmtpClient(
                $config["username"] ?? null,
                $config["token"] ?? null,
                $config["domain_api_key"] ?? null,
                $config["domain_api_secret"] ?? null,
                $config["base_url"] ?? "https://smtp-app.kirim.email",
            );
        });

        $this->app->singleton(MessagesApi::class, function ($app) {
            return new MessagesApi($app->make(SmtpClient::class));
        });

        $this->app->singleton(KirimEmailTransport::class, function ($app) {
            $config = $app["config"]["kirimemail"];

            return new KirimEmailTransport(
                $app->make(MessagesApi::class),
                $config["domain"] ?? "",
            );
        });
    }

    public function boot(): void
    {
        if ($this->app->bound("mail.manager")) {
            $this->app
                ->make("mail.manager")
                ->extend("kirimemail", function ($config) {
                    return new KirimEmailTransport(
                        app(MessagesApi::class),
                        $config["domain"] ?? "",
                    );
                });
        }

        $this->publishes(
            [
                __DIR__ . "/../../config/kirimemail.php" => config_path(
                    "kirimemail.php",
                ),
            ],
            "kirimemail-config",
        );
    }
}
