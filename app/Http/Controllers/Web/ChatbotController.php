<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use BotMan\BotMan\BotMan;
use BotMan\BotMan\BotManFactory;
use BotMan\BotMan\Drivers\DriverManager;
use BotMan\Drivers\Web\WebDriver;

class ChatbotController extends Controller
{
    private const LINKS = [
        '.*\bprofile\b.*' => ['user.profile', 'Profile', 'Visit the Profile page:'],
        '.*\bWhishlist\b.*' => ['wishList.index', 'WishList', 'Visit WishList Page:'],
        '.*\bMap\b.*' => ['maps', 'Map', 'Visit Page:'],
        '.*\bDealers\b.*' => ['Dealers', 'Dealers', 'Visit Page:'],
        '.*\bMy Orders\b.*' => ['orders', 'My Order', 'Visit Page:'],
    ];

    public function __invoke()
    {
        DriverManager::loadDriver(WebDriver::class);
        $bot = BotManFactory::create([]);

        foreach (self::LINKS as $pattern => [$route, $label, $intro]) {
            $bot->hears($pattern, fn (BotMan $b) => $b->reply($this->link($intro, $route, $label)));
        }

        $bot->hears('.*\bBuy|Rent|Purchase\b.*', fn (BotMan $b) => $b->reply(
            'Sure! <br> <br> Visit ' . $this->anchor('Purchase', 'PurchaseProperty') . ' Page.'
        ));

        $dealerOnly = ' <br> <br> If you are not you must become a trader, you have to send a request from Profile Page.';

        $bot->hears('.*\bSell\b.*', fn (BotMan $b) => $b->reply(
            'Sure! <br> <br> If you are a Dealer visit ' . $this->anchor('property', 'Add Property') . ' Page.' . $dealerOnly
        ));
        $bot->hears('.*\bMy Proper|Own Property\b.*', fn (BotMan $b) => $b->reply(
            'Sure! <br> <br> If you are a Dealer visit ' . $this->anchor('myProperties', 'My Properties') . ' Page.' . $dealerOnly
        ));
        $bot->hears('.*\bDashboard\b.*', fn (BotMan $b) => $b->reply(
            'Sure! <br> <br> If you are a Dealer visit ' . $this->anchor('dealer.dashboard', 'Dashboard') . ' Page.' . $dealerOnly
        ));

        $bot->fallback(function (BotMan $b) {
            $text = mb_strtolower($b->getMessage()->getText());
            $greeting = str_contains($text, 'hello') || str_contains($text, 'مرحبا') || str_contains($text, 'اهلا');

            $b->reply($greeting ? '👋 Hey! How can I help? ' : "🤖 Sorry, I didn't understand that.");
        });

        $bot->listen();
    }

    private function link(string $intro, string $route, string $label): string
    {
        return " {$intro}" . $this->anchor($route, $label) . ' .';
    }

    private function anchor(string $route, string $label): string
    {
        return "<a href='" . e(route($route)) . "' target='_blank'>" . e($label) . '</a>';
    }
}
