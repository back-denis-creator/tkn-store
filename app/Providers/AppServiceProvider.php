<?php

namespace App\Providers;

use Illuminate\Auth\Notifications\VerifyEmail;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        VerifyEmail::toMailUsing(function ($notifiable, string $url): MailMessage {
            return (new MailMessage)
                ->subject('Підтвердження email — '.config('app.name'))
                ->greeting('Вітаємо, '.$notifiable->name.'!')
                ->line('Дякуємо за реєстрацію на '.config('app.name').'. Щоб активувати акаунт, підтвердіть вашу email-адресу.')
                ->action('Підтвердити email', $url)
                ->line('Якщо ви не реєструвалися на нашому сайті, просто проігноруйте цей лист.');
        });
    }
}
