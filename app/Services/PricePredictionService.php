<?php

namespace App\Services;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Phpml\Regression\LeastSquares;

class PricePredictionService
{
    public function predict(Model $record, int $daysAhead = 60): array
    {
        $histories = $record->priceHistories()->orderBy('valid_from')->get();

        // aspon 3 data
        if ($histories->count() < 3) {
            return ['labels' => [], 'values' => []];
        }

        $dataPoints = [];

        foreach ($histories as $h) {
            $dataPoints[] = [
                'timestamp' => Carbon::parse($h->valid_from)->unix(),
                'price' => (float) $h->price,
            ];
        }

        return $this->predictFromDataPoints($dataPoints, $daysAhead);
    }

    public function predictFromDataPoints(array $dataPoints, int $daysAhead = 60): array
    {
        if (count($dataPoints) < 3) {
            return ['labels' => [], 'values' => []];
        }

        $origin = $dataPoints[0]['timestamp'];

        // normalizujeme x os na dni od prveho zaznamu
        $samples = [];
        $targets = [];

        foreach ($dataPoints as $point) {
            // podla docs train() prijima samples ako 2D array a targets ako 1D array
            $samples[] = [($point['timestamp'] - $origin) / 86400.0];
            $targets[] = $point['price'];
        }

        $regression = new LeastSquares;
        $regression->train($samples, $targets);

        // posledna x hodnota od nej predikujeme dopredu
        $lastX = end($samples)[0];
        $lastTimestamp = Carbon::createFromTimestamp(end($dataPoints)['timestamp']);

        // rozdelime predikciu na tyzdenne kroky min 4 body
        $stepCount = max(4, (int) ceil($daysAhead / 7));
        $stepSize = $daysAhead / $stepCount;

        $labels = [];
        $values = [];

        for ($i = 1; $i <= $stepCount; $i++) {
            $dayOffset = $i * $stepSize;

            // predict musi dostat 1D array, nie 2D!!!!!!!!!!
            // ak posleme [[x]] dostaneme naspat array a potom float conv. nefunguje spravne
            $predicted = $regression->predict([$lastX + $dayOffset]);

            $labels[] = $lastTimestamp->copy()->addDays((int) $dayOffset)->format('d.m.Y');
            // cena nemoze byt záporna zaokruhlujeme na 2 desatine miesta
            $values[] = max(0.0, round((float) $predicted, 2));
        }

        return ['labels' => $labels, 'values' => $values];
    }
}
