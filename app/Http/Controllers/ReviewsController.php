<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Setting;
use App\Services\ReviewParserService;
use Illuminate\Support\Facades\Auth; 
use Illuminate\Support\Facades\Log;
use Inertia\Inertia;

class ReviewsController extends Controller
{
    protected $parserService;

    public function __construct(ReviewParserService $parserService)
    {
        $this->parserService = $parserService;
    }

    public function stub()
    {
        $user = Auth::user();
        $reviews = [
            [
                'id' => 1,
                'author' => 'Иванов Иван',
                'text' => 'Отличный сервис',
                'rating' => 2,
                'published_at' => '2022-01-01',
                'title' => 'Кафе центр'
            ],
            [
                'id' => 2,
                'author' => 'Петров Петр',
                'text' => 'Отличный сервис',
                'rating' => 5,
                'published_at' => '2022-01-01',
                'title' => 'Кафе центр'
            ],
            [
                'id' => 3,
                'author' => 'Сидорова Наташа',
                'phone' => '+7 (999) 999-99-99',
                'text' => 'Были с мужем в данном заведении, зал был пустой, 3 блюда ждали час!!! Мало того, что долго ждали, так ещё и не вкусно от слова совсем... В курице попался жир, во рту прям как сопля, выплюнула, повар сказал, что это гриб, ну очень сомнительный гриб. Пицца на таком тонком тесте, что…
Ещё  что оно аж просвечивалось, королевские креветки были с размером обычной креветки, официант вообще не знает меню, ее спрашивать что-то абсолютно бесполезно! Не рекомендую данное заведение к посещению',
                'rating' => 3,
                'published_at' => '2022-01-01',
                'title' => 'Кафе центр'
            ],
            [
                'id' => 4,
                'author' => 'Андреев Андрей',
                'text' => 'Отличный сервис',
                'rating' => 5,
                'published_at' => '2022-01-01',
                'title' => 'Кафе центр'
            ],
        ];
        $total_count = count($reviews);
        $total_rating = collect($reviews)->sum('rating') / $total_count;
        return Inertia::render('Reviews', [
            'reviews' => $reviews,
            'total_count' => $total_count,
            'total_rating' => $total_rating
        ]);
    }

    public function index()
    {
        $user = Auth::user();
        $url = Setting::where([['key', 'yandex_url'], ['user_id', $user->id]])->first()->value;
        
        // ВНИМАНИЕ: Парсинг чужих сайтов может нарушать их условия использования.
        // Используйте этот код на свой страх и риск и только в рамках закона/правил сайта.
        $reviewsCount = 10;
        $data = $this->parserService->parseReviews($url, $reviewsCount);
        
        if (isset($data['error'])) {
            Log::error($data['error']);
            return Inertia::render('Reviews', [
                'error' => $data['error']
            ]);
        } else {
            return Inertia::render('Reviews', [
                'reviews' => $data['reviews'],
                'total_count' => $data['total_reviews'],
                'total_rating' => $data['average_rating']
            ]);
        }

    }
}
