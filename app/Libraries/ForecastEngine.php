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
            'monte_carlo'        => $this->predictMonteCarlo($horizonDays, $lastPrice),
            'sma_follow'         => $this->predictSmaFollow($horizonDays, $lastPrice),
            'wma_follow'         => $this->predictWmaFollow($horizonDays, $lastPrice),
            'holt_linear'        => $this->predictHoltLinear($horizonDays, $lastPrice),
            'roc_follow'         => $this->predictRocFollow($horizonDays, $lastPrice),
            'donchian_breakout'  => $this->predictDonchianBreakout($horizonDays, $lastPrice),
            'fibonacci_projection' => $this->predictFibonacciProjection($horizonDays, $lastPrice),
            'stochastic_reversion' => $this->predictStochasticReversion($horizonDays, $lastPrice),
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
            'monte_carlo',
            'sma_follow',
            'wma_follow',
            'holt_linear',
            'roc_follow',
            'donchian_breakout',
            'fibonacci_projection',
            'stochastic_reversion',
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

    private function predictMonteCarlo(int $horizon, float $lastPrice): ?array
    {
        $n = count($this->prices);
        if ($n < 30) return null;

        $returns = [];
        for ($i = 1; $i < $n; $i++) {
            $prev = $this->prices[$i - 1];
            if ($prev > 0) $returns[] = log($this->prices[$i] / $prev);
        }
        $count = count($returns);
        if ($count < 20) return null;

        $mean = array_sum($returns) / $count;
        $var = 0.0;
        foreach ($returns as $r) {
            $var += ($r - $mean) ** 2;
        }
        $var /= ($count - 1);
        $std = sqrt($var);

        mt_srand(crc32(implode(',', array_slice($this->prices, -30))));
        $paths = [];
        for ($sim = 0; $sim < 1500; $sim++) {
            $price = $lastPrice;
            for ($d = 0; $d < $horizon; $d++) {
                $price *= exp(($mean - 0.5 * $std * $std) + $std * $this->gaussian());
            }
            $paths[] = $price;
        }
        sort($paths);

        $median = $paths[(int) floor($count * 0.5)];
        $p10 = $paths[(int) floor($count * 0.1)];
        $p90 = $paths[(int) floor($count * 0.9)];

        $changePct = $lastPrice > 0 ? (($median - $lastPrice) / $lastPrice) * 100 : 0;
        $confidence = $this->clampConfidence(50 - (($p90 - $p10) / $lastPrice) * 100);
        $signal = $median > $lastPrice ? 'BULLISH' : ($median < $lastPrice ? 'BEARISH' : 'NEUTRAL');

        return [
            'predicted_price'      => round($median, 2),
            'predicted_change_pct' => round($changePct, 2),
            'signal'               => $signal,
            'confidence_score'     => $confidence,
            'method'               => 'monte_carlo',
            'horizon_days'         => $horizon,
            'simulations'          => 1500,
            'lower_bound'          => round($p10, 2),
            'upper_bound'          => round($p90, 2),
            'volatility'           => round($std * 100, 2),
        ];
    }

    private function predictSmaFollow(int $horizon, float $lastPrice): ?array
    {
        $fast = $this->engine->calculateSMA($this->prices, 10);
        $slow = $this->engine->calculateSMA($this->prices, 30);
        if ($fast === null || $slow === null || $slow <= 0) return null;

        $trendRate = ($fast - $slow) / $slow;
        $converge = min(abs($trendRate) * 2, 0.9);
        $predictedPrice = $lastPrice + ($fast - $lastPrice) * ($converge * $horizon / 30);
        if ($predictedPrice <= 0) return null;

        $changePct = $lastPrice != 0 ? (($predictedPrice - $lastPrice) / $lastPrice) * 100 : 0;
        $confidence = $this->clampConfidence(abs($trendRate) * 400 + 10);
        $signal = $trendRate > 0 ? 'BULLISH' : ($trendRate < 0 ? 'BEARISH' : 'NEUTRAL');

        return [
            'predicted_price'      => round($predictedPrice, 2),
            'predicted_change_pct' => round($changePct, 2),
            'signal'               => $signal,
            'confidence_score'     => $confidence,
            'method'               => 'sma_follow',
            'horizon_days'         => $horizon,
            'fast_sma'             => round($fast, 2),
            'slow_sma'             => round($slow, 2),
        ];
    }

    private function predictWmaFollow(int $horizon, float $lastPrice): ?array
    {
        $period = 20;
        $recent = array_slice($this->prices, -$period);
        if (count($recent) < $period) return null;

        $wma = 0.0;
        $weightSum = 0;
        $w = $period;
        foreach ($recent as $p) {
            $wma += $p * $w;
            $weightSum += $w;
            $w--;
        }
        $wma /= $weightSum;

        $slow = $this->engine->calculateSMA($this->prices, 50);
        $drift = $slow !== null && $slow > 0 ? ($wma - $slow) / $slow : 0;
        $predictedPrice = $lastPrice + ($wma - $lastPrice) * (min(abs($drift) * 2 + 0.05, 1.0) * $horizon / 20);
        if ($predictedPrice <= 0) return null;

        $changePct = $lastPrice != 0 ? (($predictedPrice - $lastPrice) / $lastPrice) * 100 : 0;
        $confidence = $this->clampConfidence(abs($drift) * 400 + 10);
        $signal = $wma > $lastPrice ? 'BULLISH' : ($wma < $lastPrice ? 'BEARISH' : 'NEUTRAL');

        return [
            'predicted_price'      => round($predictedPrice, 2),
            'predicted_change_pct' => round($changePct, 2),
            'signal'               => $signal,
            'confidence_score'     => $confidence,
            'method'               => 'wma_follow',
            'horizon_days'         => $horizon,
            'wma'                  => round($wma, 2),
        ];
    }

    private function predictHoltLinear(int $horizon, float $lastPrice): ?array
    {
        $alpha = 0.5;
        $beta = 0.3;
        $level = $this->prices[0];
        $trend = 0.0;

        for ($i = 1; $i < count($this->prices); $i++) {
            $prevLevel = $level;
            $level = $alpha * $this->prices[$i] + (1 - $alpha) * ($level + $trend);
            $trend = $beta * ($level - $prevLevel) + (1 - $beta) * $trend;
        }

        if ($level <= 0) return null;
        $predictedPrice = $level + $trend * $horizon;
        if ($predictedPrice <= 0) return null;

        $changePct = $lastPrice != 0 ? (($predictedPrice - $lastPrice) / $lastPrice) * 100 : 0;
        $confidence = $this->clampConfidence(abs($trend) / $level * 500 + 10);
        $signal = $trend > 0 ? 'BULLISH' : ($trend < 0 ? 'BEARISH' : 'NEUTRAL');

        return [
            'predicted_price'      => round($predictedPrice, 2),
            'predicted_change_pct' => round($changePct, 2),
            'signal'               => $signal,
            'confidence_score'     => $confidence,
            'method'               => 'holt_linear',
            'horizon_days'         => $horizon,
            'trend'                => round($trend, 4),
        ];
    }

    private function predictRocFollow(int $horizon, float $lastPrice): ?array
    {
        $period = 20;
        $roc = $this->engine->calculateROC($this->prices, $period);
        if ($roc === null || $lastPrice <= 0) return null;

        $dailyGrowth = $roc / 100 / $period;
        $predictedPrice = $lastPrice * pow(1 + $dailyGrowth, $horizon);
        if ($predictedPrice <= 0) return null;

        $changePct = $lastPrice != 0 ? (($predictedPrice - $lastPrice) / $lastPrice) * 100 : 0;
        $confidence = $this->clampConfidence(abs($roc) * 2 + 10);
        $signal = $roc > 0 ? 'BULLISH' : ($roc < 0 ? 'BEARISH' : 'NEUTRAL');

        return [
            'predicted_price'      => round($predictedPrice, 2),
            'predicted_change_pct' => round($changePct, 2),
            'signal'               => $signal,
            'confidence_score'     => $confidence,
            'method'               => 'roc_follow',
            'horizon_days'         => $horizon,
            'roc'                  => round($roc, 2),
        ];
    }

    private function predictDonchianBreakout(int $horizon, float $lastPrice): ?array
    {
        $dc = $this->engine->calculateDonchianChannels(20);
        if ($dc['upper'] === null || $dc['lower'] === null || $dc['middle'] === null) return null;

        $upper = $dc['upper'];
        $lower = $dc['lower'];
        $middle = $dc['middle'];
        $range = $upper - $lower;
        if ($range <= 0) return null;

        $position = ($lastPrice - $lower) / $range;
        $signal = 'NEUTRAL';
        $confidence = 10;

        if ($lastPrice > $upper) {
            $signal = 'BULLISH';
            $confidence = $this->clampConfidence($position * 60 + 15);
        } elseif ($lastPrice < $lower) {
            $signal = 'BEARISH';
            $confidence = $this->clampConfidence((1 - $position) * 60 + 15);
        } elseif ($position > 0.8) {
            $signal = 'BULLISH';
            $confidence = $this->clampConfidence($position * 50 + 10);
        } elseif ($position < 0.2) {
            $signal = 'BEARISH';
            $confidence = $this->clampConfidence((1 - $position) * 50 + 10);
        }

        $direction = $signal === 'BULLISH' ? 1 : ($signal === 'BEARISH' ? -1 : 0);
        $move = $lastPrice > 0 ? ($lastPrice - $middle) / $lastPrice : 0;
        $predictedPrice = $lastPrice * (1 + $direction * abs($move) * 0.5 * ($horizon / 20));
        if ($predictedPrice <= 0) return null;

        $changePct = $lastPrice != 0 ? (($predictedPrice - $lastPrice) / $lastPrice) * 100 : 0;

        return [
            'predicted_price'      => round($predictedPrice, 2),
            'predicted_change_pct' => round($changePct, 2),
            'signal'               => $signal,
            'confidence_score'     => $confidence,
            'method'               => 'donchian_breakout',
            'horizon_days'         => $horizon,
            'donchian_upper'       => round($upper, 2),
            'donchian_lower'       => round($lower, 2),
            'band_position'        => round($position, 3),
        ];
    }

    private function predictFibonacciProjection(int $horizon, float $lastPrice): ?array
    {
        $lookback = min(count($this->prices), 60);
        $window = array_slice($this->prices, -$lookback);
        $swingHigh = max($window);
        $swingLow = min($window);
        $range = $swingHigh - $swingLow;
        if ($range <= 0) return null;

        $this->engine->calculateFibonacciRetracement($swingHigh, $swingLow);
        $position = ($lastPrice - $swingLow) / $range;

        if ($position >= 0.5) {
            $target = $swingHigh + 0.618 * $range;
            $signal = 'BULLISH';
            $confidence = $this->clampConfidence($position * 50 + 10);
        } else {
            $target = $swingLow - 0.618 * $range;
            $signal = 'BEARISH';
            $confidence = $this->clampConfidence((1 - $position) * 50 + 10);
        }

        $predictedPrice = $lastPrice + ($target - $lastPrice) * ($horizon / 30);
        if ($predictedPrice <= 0) return null;

        $changePct = $lastPrice != 0 ? (($predictedPrice - $lastPrice) / $lastPrice) * 100 : 0;

        return [
            'predicted_price'      => round($predictedPrice, 2),
            'predicted_change_pct' => round($changePct, 2),
            'signal'               => $signal,
            'confidence_score'     => $confidence,
            'method'               => 'fibonacci_projection',
            'horizon_days'         => $horizon,
            'swing_high'           => round($swingHigh, 2),
            'swing_low'            => round($swingLow, 2),
            'fib_target'           => round($target, 2),
        ];
    }

    private function predictStochasticReversion(int $horizon, float $lastPrice): ?array
    {
        $stoch = $this->engine->calculateStochastic(14);
        $k = $stoch['percent_k'] ?? null;
        if ($k === null) return null;

        $signal = 'NEUTRAL';
        $confidence = 10;
        $changePct = 0;

        if ($k < 20) {
            $signal = 'BULLISH';
            $confidence = $this->clampConfidence(((20 - $k) / 20) * 60 + 10);
            $changePct = ((20 - $k) / 20) * 4;
        } elseif ($k > 80) {
            $signal = 'BEARISH';
            $confidence = $this->clampConfidence((($k - 80) / 20) * 60 + 10);
            $changePct = -(($k - 80) / 20) * 4;
        }

        $predictedPrice = $lastPrice * (1 + $changePct / 100);
        if ($predictedPrice <= 0) return null;

        return [
            'predicted_price'      => round($predictedPrice, 2),
            'predicted_change_pct' => round($changePct, 2),
            'signal'               => $signal,
            'confidence_score'     => $confidence,
            'method'               => 'stochastic_reversion',
            'horizon_days'         => $horizon,
            'stoch_k'              => round($k, 2),
        ];
    }

    private function gaussian(): float
    {
        $u1 = mt_rand(1, 999999) / 1000000;
        $u2 = mt_rand(1, 999999) / 1000000;

        return sqrt(-2 * log($u1)) * cos(2 * M_PI * $u2);
    }

    private function clampConfidence(float $value): float
    {
        return max(5, min(95, round($value, 1)));
    }
}