<?php

namespace App\Commands;

use App\Libraries\ForecastEngine;
use CodeIgniter\CLI\BaseCommand;
use CodeIgniter\CLI\Input;
use CodeIgniter\CLI\Output;

class RunPredictions extends BaseCommand
{
    protected $group = 'predictions';

    protected $description = 'Run prediction queries for all active queries';

    public function run(array $params)
    {
        $this->output->writeln('Running prediction queries...');

        $forecastEngine = new ForecastEngine();
        $queryModel = model('App\\Models\\PredictionQueryModel');
        $resultModel = model('App\\Models\\PredictionQueryResultModel');

        $queries = $queryModel
            ->where('status', 'pending')
            ->findAll();

        if (empty($queries)) {
            $this->output->writeln('No pending prediction queries found.');
            return;
        }

        $processedCount = 0;
        foreach ($queries as $query) {
            $this->output->writeln("Processing: " . $query['name'] . " (ID: " . $query['id'] . ")");

            $forecastEngine->loadData($this->getSampleOHLCVData());
            $forecast = $forecastEngine->predict($query['method'], $query['horizon_days']);

            if ($forecast) {
                $forecast['query_id'] = $query['id'];
                $forecast['stock_id'] = 1;
                $forecast['stock_symbol'] = 'TEST';
                $forecast['stock_name'] = 'Test Stock';
                $forecast['forecast_date'] = date('Y-m-d');

                $resultModel->insert($forecast);
                $processedCount++;

                $this->output->writeln("  -> Predicted: {$forecast['predicted_price']} ({$forecast['predicted_change_pct']}%) - Confidence: {$forecast['confidence_score']}%");
            } else {
                $this->output->writeln("  -> No forecast generated");
            }
        }

        $queryModel
            ->whereIn('id', array_column($queries, 'id'))
            ->set('status', 'completed')
            ->set('last_run_at', date('Y-m-d H:i:s'))
            ->update();

        $this->output->writeln("\nDone! Processed $processedCount predictions.");
    }

    private function getSampleOHLCVData(): array
    {
        $basePrice = 100.0;
        $ohlcv = [];

        for ($i = 0; $i < 30; $i++) {
            $open = $basePrice + (rand() - rand()) * 5;
            $high = $open + rand() * 5;
            $low = $open - rand() * 5;
            $close = ($high + $low) / 2 + (rand() - rand()) * 3;
            $volume = rand() * 10000 + 5000;

            $ohlcv[] = [
                'open' => $open,
                'high' => $high,
                'low' => $low,
                'close' => $close,
                'volume' => $volume,
            ];

            $basePrice = $close;
        }

        return $ohlcv;
    }
}