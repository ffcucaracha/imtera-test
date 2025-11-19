<?php
namespace App\Services;

use GuzzleHttp\Client as GuzzleClient;
use Symfony\Component\DomCrawler\Crawler;
use Exception;
use Carbon\Carbon;

class ReviewParserService
{
    protected $client;
    protected $title;

    public function __construct()
    {
        $this->client = new GuzzleClient();
    }

    public function parseReviews(string $url, int $reviewsCount): array
    {
        try {
            $response = $this->client->request('GET', $url);
            $htmlContent = $response->getBody()->getContents();
            
            $crawler = new Crawler($htmlContent);

            $this->title = $this->getTitle($crawler);

            $totalReviews = $this->getTotalReviews($crawler);
            $averageRating = $this->getAverageRating($crawler);
            $reviews = $this->getIndividualReviews($crawler, $reviewsCount);

            return [
                'total_reviews' => $totalReviews,
                'average_rating' => $averageRating,
                'reviews' => $reviews,
            ];
        } catch (Exception $e) {
            return ['error' => $e->getMessage()];
        }
    }

    private function getTitle(Crawler $crawler): ?string
    {
        $meta = $crawler->filter('h1[itemprop="name"]');

        if ($meta->count() > 0) {
            return $meta->text();
        }

        return null;
    }

    private function getTotalReviews(Crawler $crawler): ?int
    {
        // Ищем метатег с itemprop="reviewCount" внутри блока aggregateRating
        $countMeta = $crawler->filter('span[itemprop="aggregateRating"] meta[itemprop="reviewCount"]');

        if ($countMeta->count() > 0) {
            return (int)$countMeta->attr('content');
        }

        return null;
    }

    private function getAverageRating(Crawler $crawler): ?float
    {
        // Ищем метатег с itemprop="ratingValue" внутри блока aggregateRating
        $ratingMeta = $crawler->filter('span[itemprop="aggregateRating"] meta[itemprop="ratingValue"]');

        if ($ratingMeta->count() > 0) {
            return (float)$ratingMeta->attr('content');
        }

        throw new Exception('Average rating not found.');
    }

    private function getIndividualReviews(Crawler $crawler, int $reviewsCount): array
    {
        $reviews = [];

        // Находим контейнер с отзывами и перебираем каждый отдельный отзыв
        $crawler->filter('div.business-reviews-card-view__review')->slice(0, $reviewsCount)->each(function (Crawler $node, $i) use (&$reviews, $reviewsCount) {
            $reviewData = [];

            // ID (у вас его нет в разметке, можно использовать индекс или генерировать)
            $reviewData['id'] = $i + 1;

            // Автор (ищем внутри .business-review-view__author-name span[itemprop="name"])
            try {
                $reviewData['author'] = $node->filter('.business-review-view__author-name span[itemprop="name"]')->text();
            } catch (\InvalidArgumentException $e) {
                $reviewData['author'] = 'Неизвестен';
            }
            
            // Телефона нет в вашей разметке
            $reviewData['phone'] = null;

            // Рейтинг отзыва (ищем meta[itemprop="ratingValue"] внутри блока reviewRating)
            try {
                $ratingValue = $node->filter('span[itemprop="reviewRating"] meta[itemprop="ratingValue"]')->attr('content');
                $reviewData['rating'] = (int)$ratingValue;
            } catch (\InvalidArgumentException $e) {
                $reviewData['rating'] = 0;
            }

            // Дата публикации (ищем meta[itemprop="datePublished"])
            try {
                 $datePublished = $node->filter('span.business-review-view__date meta[itemprop="datePublished"]')->attr('content');
                 try {
                    $datePublished = Carbon::parse($datePublished);
                    $formattedDate = $datePublished->format('d.m.Y H:i');
                    $reviewData['published_at'] = $formattedDate;
                 } catch (Exception $e) {
                    $reviewData['published_at'] = $datePublished;
                 }
            } catch (\InvalidArgumentException $e) {
                $reviewData['published_at'] = null;
            }

            // Заголовок
            $reviewData['title'] = $this->title;

            // Текст отзыва (ищем внутри .business-review-view__body)
            try {
                // Извлекаем текст из спана внутри спойлера
                $reviewData['text'] = trim($node->filter('.business-review-view__body .spoiler-view__text-container')->text());
            } catch (\InvalidArgumentException $e) {
                $reviewData['text'] = null;
            }
            
            // Если текст пустой, пробуем найти просто текст в теле отзыва
            if (!$reviewData['text']) {
                 try {
                    $reviewData['text'] = trim($node->filter('.business-review-view__body')->text());
                } catch (\InvalidArgumentException $e) {
                    $reviewData['text'] = null;
                }
            }


            $reviews[] = $reviewData;
        });

        return $reviews;
    }
}