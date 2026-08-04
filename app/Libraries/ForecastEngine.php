<?php

namespace App\Libraries;

use App\Analysis\StockTechnicalAnalysisEngine;

class ForecastEngine
{
    private StockTechnicalAnalysisEngine $engine;
    private array $ohlcv = [];
    private array $prices = [];
    private array $highs = [];
    private array $lows = [];
    private array $volumes = [];

    public function __construct()
    {
        $this->engine = new StockTechnicalAnalysisEngine();
    }

    public function loadData(array $ohlcv): self
    {
        $this->ohlcv = array_values($ohlcv);
        $this->prices = array_map('floatval', array_column($this->ohlcv, 'close'));
        $this->highs  = array_map('floatval', array_column($this->ohlcv, 'high'));
        $this->lows   = array_map('floatval', array_column($this->ohlcv, 'low'));
        $this->volumes = array_map('floatval', array_column($this->ohlcv, 'volume'));

        $this->engine->loadData($this->ohlcv);
        return $this;
    }

    public function predict(string $method, int $horizonDays = 7): ?array
    {
        if (empty($this->prices)) return null;
        if (count($this->prices) < 5) return null;

        $lastPrice = end($this->prices);

        return match ($method) {
            'linear_regression'  => $this->predictLinearRegression($horizonDays, $lastPrice),
            'ema_crossover'      => $this->predictEmaCrossover($horizonDays, $lastPrice),
            'mean_reversion'     => $this->predictMeanReversion($horizonDays, $lastPrice),
            'rsi_reversion'      => $this->predictRsiReversion($horizonDays, $lastPrice),
            'bollinger_bounce'   => $this->predictBollingerBounce($horizonDays, $lastPrice),
            'macd_divergence'    => $this->predictMacdDivergence($horizonDays, $lastPrice),
            'vwap_reversion'     => $this->predictVwapReversion($horizonDays, $lastPrice),
            'supertrend_follow'  => $this->predictSupertrendFollow($horizonDays, $lastPrice),
            default              => null,
        };
    }

    public function supportedMethods(): array
    {
        return [
            'linear_regression',
            'ema_crossover',
            'mean_reversion',
            'rsi_reversion',
            'bollinger_bounce',
            'macd_divergence',
            'vwap_reversion',
            'supertrend_follow',
        ];
    }

    private function predictLinearRegression(int $horizon, float $lastPrice): ?array
    {
        $period = min(count($this->prices), 20);
        $result = $this->engine->calculateLinearRegression($this->prices, $period);

        if ($result['slope'] === null) return null;

        $slope = $result['slope'];
        $rSquared = $result['r_squared'] ?? 0;

        $predictedPrice = $lastPrice + ($slope * $horizon);
        $changePct = $lastPrice != 0 ? (($predictedPrice - $lastPrice) / $lastPrice) * 100 : 0;

        $confidence = $this->clampConfidence(
            ($rSquared * 50) + min(abs($slope) / $lastPrice * 100, 30)
        );

        $signal = $slope > 0 ? 'BULLISH' : ($slope < 0 ? 'BEARISH' : 'NEUTRAL');

        return [
            'predicted_price'      => round($predictedPrice, 2),
            'predicted_change_pct' => round($changePct, 2),
            'signal'               => $signal,
            'confidence_score'     => $confidence,
            'method'               => 'linear_regression',
            'horizon_days'         => $horizon,
        ];
    }

    private function predictEmaCrossover(int $horizon, float $lastPrice): ?array
    {
        $fastEma = $this->engine->calculateEMA($this->prices, 9);
        $slowEma = $this->engine->calculateEMA($this->prices, 21);

        if ($fastEma === null || $slowEma === null) return null;

        $diff = $fastEma - $slowEma;
        $prevFast = $this->engine->calculateEMA(array_slice($this->prices, 0, -1), 9);
        $prevSlow = $this->engine->calculateEMA(array_slice($this->prices, 0, -1), 21);

        $prevDiff = ($prevFast !== null && $prevSlow !== null) ? $prevFast - $prevSlow : 0;
        $crossover = ($prevDiff <= 0 && $diff > 0) ? 'bullish_cross' : (($prevDiff >= 0 && $diff < 0) ? 'bearish_cross' : 'continuation');

        $emaSlope = $diff;
        $predictedPrice = $lastPrice + ($emaSlope * $horizon * 0.5);
        $changePct = $lastPrice != 0 ? (($predictedPrice - $lastPrice) / $lastPrice) * 100 : 0;

        $confidence = $this->clampConfidence(
            abs($diff) / $lastPrice * 200 + ($crossover !== 'continuation' ? 20 : 0)
        );

        $signal = $diff > 0 ? 'BULLISH' : ($diff < 0 ? 'BEARISH' : 'NEUTRAL');

        return [
            'predicted_price'      => round($predictedPrice, 2),
            'predicted_change_pct' => round($changePct, 2),
            'signal'               => $signal,
            'confidence_score'     => $confidence,
            'method'               => 'ema_crossover',
            'horizon_days'         => $horizon,
            'crossover_type'       => $crossover,
        ];
    }

    private function predictMeanReversion(int $horizon, float $lastPrice): ?array
    {
        $vwap = $this->engine->calculateVWAP();
        $sma20 = $this->engine->calculateSMA($this->prices, 20);

        if ($vwap === null && $sma20 === null) return null;

        $target = $vwap ?? $sma20;
        $distance = ($lastPrice - $target) / $target;

        $reversionSpeed = min(abs($distance) * 2, 1.0);
        $predictedPrice = $lastPrice - ($distance * $lastPrice * $reversionSpeed * ($horizon / 7));
        $changePct = $lastPrice != 0 ? (($predictedPrice - $lastPrice) / $lastPrice) * 100 : 0;

        $confidence = $this->clampConfidence(
            abs($distance) * 100 + 10
        );

        $signal = $distance < -0.02 ? 'BULLISH' : ($distance > 0.02 ? 'BEARISH' : 'NEUTRAL');

        return [
            'predicted_price'      => round($predictedPrice, 2),
            'predicted_change_pct' => round($changePct, 2),
            'signal'               => $signal,
            'confidence_score'     => $confidence,
            'method'               => 'mean_reversion',
            'horizon_days'         => $horizon,
            'target_price'         => round($target, 2),
            'distance_from_target' => round($distance * 100, 2),
        ];
    }

    private function predictRsiReversion(int $horizon, float $lastPrice): ?array
    {
        $rsi = $this->engine->calculateRSI($this->prices, 14);
        if ($rsi === null) return null;

        $signal = 'NEUTRAL';
        $confidence = 0;
        $changePct = 0;

        if ($rsi < 30) {
            $signal = 'BULLISH';
            $oversoldDegree = (30 - $rsi) / 30;
            $confidence = $this->clampConfidence($oversoldDegree * 80 + 10);
            $changePct = $oversoldDegree * 5;
        } elseif ($rsi > 70) {
            $signal = 'BEARISH';
            $overboughtDegree = ($rsi - 70) / 30;
            $confidence = $this->clampConfidence($overboughtDegree * 80 + 10);
            $changePct = -($overboughtDegree * 5);
        } else {
            $confidence = 10;
            $changePct = 0;
        }

        $predictedPrice = $lastPrice * (1 + $changePct / 100);

        return [
            'predicted_price'      => round($predictedPrice, 2),
            'predicted_change_pct' => round($changePct, 2),
            'signal'               => $signal,
            'confidence_score'     => $confidence,
            'method'               => 'rsi_reversion',
            'horizon_days'         => $horizon,
            'rsi'                  => round($rsi, 2),
        ];
    }

    private function predictBollingerBounce(int $horizon, float $lastPrice): ?array
    {
        $bands = $this->engine->calculateBollingerBands($this->prices, 20, 2.0);
        if ($bands['upper'] === null || $bands['lower'] === null || $bands['middle'] === null) return null;

        $upper = $bands['upper'];
        $lower = $bands['lower'];
        $middle = $bands['middle'];
        $range = $upper - $lower;

        if ($range == 0) return null;

        $position = ($lastPrice - $lower) / $range;
        $signal = 'NEUTRAL';
        $confidence = 0;
        $changePct = 0;

        if ($position < 0.2) {
            $signal = 'BULLISH';
            $distanceToMiddle = ($middle - $lastPrice) / $lastPrice;
            $confidence = $this->clampConfidence((1 - $position) * 60 + 10);
            $changePct = $distanceToMiddle * 100 * 0.5;
        } elseif ($position > 0.8) {
            $signal = 'BEARISH';
            $distanceToMiddle = ($lastPrice - $middle) / $lastPrice;
            $confidence = $this->clampConfidence($position * 60 + 10);
            $changePct = -($distanceToMiddle * 100 * 0.5);
        } else {
            $confidence = 10;
        }

        $predictedPrice = $lastPrice * (1 + $changePct / 100);

        return [
            'predicted_price'      => round($predictedPrice, 2),
            'predicted_change_pct' => round($changePct, 2),
            'signal'               => $signal,
            'confidence_score'     => $confidence,
            'method'               => 'bollinger_bounce',
            'horizon_days'         => $horizon,
            'band_position'        => round($position, 3),
            'target_price'         => round($middle, 2),
        ];
    }

    private function predictMacdDivergence(int $horizon, float $lastPrice): ?array
    {
        $macd = $this->engine->calculateMACD($this->prices, 12, 26, 9);
        if ($macd['macd'] === null || $macd['histogram'] === null) return null;

        $histogram = $macd['histogram'];
        $signalLine = $macd['signal'];
        $macdLine = $macd['macd'];

        $histDirection = $histogram > 0 ? 'positive' : 'negative';
        $histStrength = abs($histogram) / max(abs($lastPrice), 1);

        $signal = $histogram > 0 ? 'BULLISH' : ($histogram < 0 ? 'BEARISH' : 'NEUTRAL');
        $confidence = $this->clampConfidence(min($histStrength * 500 + 10, 60));

        $trendFactor = $histogram * 0.01;
        $predictedPrice = $lastPrice * (1 + $trendFactor * ($horizon / 7));
        $changePct = $lastPrice != 0 ? (($predictedPrice - $lastPrice) / $lastPrice) * 100 : 0;

        return [
            'predicted_price'      => round($predictedPrice, 2),
            'predicted_change_pct' => round($changePct, 2),
            'signal'               => $signal,
            'confidence_score'     => $confidence,
            'method'               => 'macd_divergence',
            'horizon_days'         => $horizon,
            'histogram'            => round($histogram, 4),
            'hist_direction'       => $histDirection,
        ];
    }

    private function predictVwapReversion(int $horizon, float $lastPrice): ?array
    {
        $vwap = $this->engine->calculateVWAP();
        if ($vwap === null) return null;

        $distance = ($lastPrice - $vwap) / $vwap;
        $signal = $distance < -0.01 ? 'BULLISH' : ($distance > 0.01 ? 'BEARISH' : 'NEUTRAL');

        $reversionSpeed = min(abs($distance) * 3, 1.0);
        $predictedPrice = $lastPrice - ($distance * $lastPrice * $reversionSpeed * ($horizon / 7));
        $changePct = $lastPrice != 0 ? (($predictedPrice - $lastPrice) / $lastPrice) * 100 : 0;

        $confidence = $this->clampConfidence(abs($distance) * 150 + 5);

        return [
            'predicted_price'      => round($predictedPrice, 2),
            'predicted_change_pct' => round($changePct, 2),
            'signal'               => $signal,
            'confidence_score'     => $confidence,
            'method'               => 'vwap_reversion',
            'horizon_days'         => $horizon,
            'vwap'                 => round($vwap, 2),
            'distance_from_vwap'   => round($distance * 100, 2),
        ];
    }

    private function predictSupertrendFollow(int $horizon, float $lastPrice): ?array
    {
        $st = $this->engine->calculateSupertrend(10, 3.0);
        if ($st['trend'] === 'UNKNOWN' || $st['supertrend'] === null) return null;

        $atr = $this->engine->calculateATR(10);
        $signal = $st['trend'] === 'BULLISH' ? 'BULLISH' : ($st['trend'] === 'BEARISH' ? 'BEARISH' : 'NEUTRAL');

        $atrPct = $atr != 0 ? $atr / $lastPrice : 0;
        $confidence = $this->clampConfidence(min($atrPct * 200 + 10, 50));

        $direction = $signal === 'BULLISH' ? 1 : ($signal === 'BEARISH' ? -1 : 0);
        $predictedPrice = $lastPrice + ($direction * $atr * 1.5 * ($horizon / 7));
        $changePct = $lastPrice != 0 ? (($predictedPrice - $lastPrice) / $lastPrice) * 100 : 0;

        return [
            'predicted_price'      => round($predictedPrice, 2),
            'predicted_change_pct' => round($changePct, 2),
            'signal'               => $signal,
            'confidence_score'     => $confidence,
            'method'               => 'supertrend_follow',
            'horizon_days'         => $horizon,
            'supertrend'           => round($st['supertrend'], 2),
            'trend'                => $st['trend'],
        ];
    }

    private function clampConfidence(float $value): float
    {
        return max(5, min(95, round($value, 1)));
    }
}