<?php

namespace App\Analysis;

/**
 * Class StockTechnicalAnalysisEngine
 *
 * Comprehensive quantitative and technical analysis engine for multi-horizon price evaluation.
 * Computes technical indicators across 6 core pillars: Overlays, Volatility, Momentum,
 * Volumetric Flow, Market Structure (SMC), and Quantitative Statistics.
 *
 * @package App\Analysis
 * @author  Your Team / Engineer Name
 *
 * @note All input OHLCV arrays MUST be sorted chronologically from oldest to newest [0 => Oldest, N-1 => Latest].
 * @note Functions return `null` if the provided dataset length is insufficient for the requested lookback period.
 *
 * Usage Example:
 * $engine = (new StockTechnicalAnalysisEngine())->setHistoricalData($ohlcvArray);
 * $rsi = $engine->calculateRSI(array_column($ohlcvArray, 'close'), 14);
 */

/*
|--------------------------------------------------------------------------
| TECHNICAL ENGINE DEVELOPER NOTES
|--------------------------------------------------------------------------
| 1. DATA INPUT: Always pass array_column($ohlcv, 'close') for price-only functions.
| 2. SMC & VOLUMETRICS: Require full OHLCV structure via setHistoricalData().
| 3. NOISE REGIME: Use calculateEfficiencyRatio() or calculateChoppinessIndex()
|    to determine if the engine should execute Trend or Mean-Reversion logic.
| 4. SIGNAL FILTERS: Breakout signals require VolumeRatio >= 1.5 and CMF > 0.05.
*/

/**
 * Class StockTechnicalAnalysisEngine
 *
 * Enterprise-grade quantitative & technical analysis engine for CodeIgniter 4 and Rubix/ML pipelines.
 * Computes technical indicators across 16 core pillars (58+ functions):
 *
 * =========================================================================================
 * COMPLETE ARCHITECTURAL INDEX
 * =========================================================================================
 * 1.  OVERLAY & TREND: SMA, EMA, VWAP, MACD
 * 2.  VOLATILITY & CHANNELS: ATR, Bollinger Bands, Keltner Channels, Donchian Channels
 * 3.  MOMENTUM & OSCILLATORS: RSI, Stochastic, CCI, ROC, Williams %R, RVI, Coppock Curve
 * 4.  TREND STRENGTH & TRAILING: Supertrend, Parabolic SAR
 * 5.  VOLUME & ACCUMULATION: OBV, CMF, VPT, MFI, Volume Ratio, Force Index, Ease of Movement
 * 6.  SUPPORT, RESISTANCE & PIVOTS: Standard Floor Pivots, Fibonacci Retracements
 * 7.  CANDLESTICK, PATTERNS & SMC: Japanese Patterns, Fair Value Gaps (FVG), BOS Structure
 * 8.  QUANTITATIVE, REGIME & STATS: Linear Regression, Z-Score, Efficiency Ratio, CHOP, Downside Dev, Mansfield RS
 * 9.  HARMONICS: AB=CD Ratio Validation & PRZ Detection
 * 10. ADAPTIVE FILTERS: Kaufman's Adaptive Moving Average (KAMA)
 * 11. MICROSTRUCTURE: Volume Delta, Ulcer Index, Historical VaR
 * 12. MEAN-REVERSION & CYCLES: Hurst Exponent, DPO, Beta vs Benchmark
 * 13. TAIL RISK & SQUEEZE: TTM Squeeze, CVaR, Sortino Ratio, Martin Ratio
 * 14. SPECIALIZED HIGH-FREQUENCY: Aroon, TSI, Vortex, CMO, Mass Index, Connors RSI
 * 15. VOLATILITY SCALING: NATR, RMI, Klinger Volume Oscillator, Rainbow MA Cascade
 * 16. ML FEATURE ENGINEERING & LIQUIDITY: Log Returns, Fractional Differentiation, Min-Max Scaler, Volume Profile (POC/VAH/VAL)
 * =========================================================================================
 *
 * @note All OHLCV array inputs MUST be sorted chronologically [0 = Oldest, N-1 = Latest].
 */

/**
 * Class StockTechnicalAnalysisEngine
 *
 * Comprehensive quantitative and technical analysis engine for multi-horizon price evaluation.
 * Computes technical indicators across 15 core pillars.
 *
 * =========================================================================================
 * COMPLETE FUNCTION INDEX & CATEGORY MAP
 * =========================================================================================
 * 1. PILLAR 1: OVERLAY & TREND INDICATORS
 *    - calculateSMA(array $prices, int $period): ?float
 *    - calculateEMA(array $prices, int $period): ?float
 *    - calculateVWAP(): ?float
 *    - calculateMACD(array $prices, int $fast = 12, int $slow = 26, int $signal = 9): array
 *
 * 2. PILLAR 2: VOLATILITY & CHANNEL INDICATORS
 *    - calculateATR(int $period = 14): ?float
 *    - calculateBollingerBands(array $prices, int $period = 20, float $stdDevMult = 2.0): array
 *    - calculateKeltnerChannels(array $prices, int $emaPeriod = 20, int $atrPeriod = 10, float $multiplier = 2.0): array
 *    - calculateDonchianChannels(int $period = 20): array
 *
 * 3. PILLAR 3: MOMENTUM & OSCILLATOR INDICATORS
 *    - calculateRSI(array $prices, int $period = 14): ?float
 *    - calculateStochastic(int $kPeriod = 14, int $dPeriod = 3): array
 *    - calculateCCI(int $period = 20): ?float
 *    - calculateROC(array $prices, int $period = 12): ?float
 *    - calculateWilliamsR(int $period = 14): ?float
 *    - calculateRVI(array $prices, int $period = 14): ?float
 *    - calculateCoppockCurve(array $prices, int $wmaPeriod = 10, int $rocLong = 14, int $rocShort = 11): ?float
 *
 * 4. PILLAR 4: TREND STRENGTH & TRAILING INDICATORS
 *    - calculateSupertrend(int $period = 10, float $multiplier = 3.0): array
 *    - calculateParabolicSAR(float $step = 0.02, float $maxStep = 0.20): ?float
 *
 * 5. PILLAR 5: VOLUME & ACCUMULATION ANALYTICS
 *    - calculateOBV(): float
 *    - calculateCMF(int $period = 20): ?float
 *    - calculateVPT(array $closePrices, array $volumes): float
 *    - calculateMFI(int $period = 14): ?float
 *    - calculateVolumeRatio(int $period = 20): ?float
 *    - calculateForceIndex(int $period = 13): ?float
 *    - calculateEaseOfMovement(int $period = 14): ?float
 *
 * 6. PILLAR 6: SUPPORT, RESISTANCE & PIVOTS
 *    - calculatePivotPoints(float $high, float $low, float $close): array
 *    - calculateFibonacciRetracement(float $swingHigh, float $swingLow): array
 *
 * 7. PILLAR 7: CANDLESTICK, PATTERNS & MARKET STRUCTURE (SMC)
 *    - detectCandlestickPattern(): string
 *    - detectFairValueGaps(): array
 *    - evaluateMarketStructure(): string
 *
 * 8. PILLAR 8: QUANTITATIVE, REGIME & STATISTICAL METRICS
 *    - calculateLinearRegression(array $prices, int $period = 20): array
 *    - calculateZScore(array $prices, int $period = 20): ?float
 *    - calculateEfficiencyRatio(array $prices, int $period = 10): ?float
 *    - calculateChoppinessIndex(int $period = 14): ?float
 *    - calculateDownsideDeviation(array $returns, float $targetReturn = 0.0): ?float
 *    - calculateMansfieldRelativeStrength(array $stockPrices, array $indexPrices, int $period = 52): ?float
 *    - calculateExpandingMean(array $data): array
 *    - isMonotonicIncreasing(array $arr): bool
 *
 * 9. PILLAR 9: HARMONIC & PATTERN RATIOS
 *    - calculateHarmonicRatios(float $xA, float $aB, float $bC, float $cD): array
 *
 * 10. PILLAR 10: ADAPTIVE INDICATORS
 *    - calculateKAMA(array $prices, int $period = 10, int $fastFast = 2, int $slowSlow = 30): ?float
 *
 * 11. PILLAR 11: MICROSTRUCTURE & TAPE ANALYTICS
 *    - calculateVolumeDelta(): ?float
 *    - calculateUlcerIndex(array $prices, int $period = 14): ?float
 *    - calculateHistoricalVaR(array $returns, float $confidence = 0.95): float
 *
 * 12. PILLAR 12: STATISTICAL MEAN-REVERSION & BENCHMARKING
 *    - calculateHurstExponent(array $prices, int $period = 20): ?float
 *    - calculateDPO(array $prices, int $period = 20): ?float
 *    - calculateBeta(array $stockReturns, array $indexReturns): ?float
 *
 * 13. PILLAR 13: ADVANCED SQUEEZE & TAIL RISK METRICS
 *    - calculateTTMSqueeze(array $prices, int $period = 20): array
 *    - calculateCVaR(array $returns, float $confidence = 0.95): float
 *    - calculateSortinoRatio(array $returns, float $riskFreeRate = 0.05): ?float
 *    - calculateMartinRatio(array $prices, float $riskFreeRate = 0.05): ?float
 *
 * 14. PILLAR 14: SPECIALIZED OSCILLATORS & HIGH-FREQUENCY METRICS
 *    - calculateAroon(int $period = 25): array
 *    - calculateTSI(array $prices, int $longPeriod = 25, int $shortPeriod = 13): ?float
 *    - calculateVortex(int $period = 14): array
 *    - calculateCMO(array $prices, int $period = 14): ?float
 *    - calculateMassIndex(int $emaPeriod = 9, int $sumPeriod = 25): ?float
 *    - calculateConnorsRSI(array $prices, int $rsiPeriod = 3, int $streakPeriod = 2, int $rankPeriod = 100): ?float
 *
 * 15. PILLAR 15: SPECIALIZED VOLATILITY SCALING & CROSS-ASSET COMPARISONS
 *    - calculateNATR(int $period = 14): ?float
 *    - calculateRMI(array $prices, int $period = 14, int $momentumLookback = 3): ?float
 *    - calculateKlingerOscillator(int $fastPeriod = 34, int $slowPeriod = 55): ?float
 *    - calculateRainbowMA(array $prices, int $basePeriod = 2, int $depth = 6): array
 * 
 * 16. PILLAR 16: SPECIALIZED INDICATORS & ML FEATURE ENGINEERING
 *    - calculateLogReturns(array $prices): array
 *    - calculateFractionalDifferentiation(array $prices, float $d = 0.5, int $window = 10): array
 *    - minMaxScale(array $featureArray, float $minTarget = 0.0, float $maxTarget = 1.0): array
 *    - calculateVolumeProfileLevels(int $period = 30, int $bins = 10): array
 * =========================================================================================
 *
 * @note All OHLCV input data arrays MUST be sorted chronologically: oldest [0] to newest [N-1].
 * @note Indicator functions return `null` if the provided history length is insufficient.
 */

class StockTechnicalAnalysisEngine
{
    /** @var array Holds current OHLCV dataset slice */
    protected array $data = [];

    /** @var int Cached count of data records */
    protected int $dataCount = 0;

    /**
     * Loads and normalizes OHLCV data into the engine instance.
     *
     * @param array $ohlcv Chronological array of candle bars
     * @return self
     */
    public function loadData(array $ohlcv): self
    {
        $this->data = array_map(function ($bar) {
            // Convert objects (if any) to arrays and lower-case all keys
            return array_change_key_case((array) $bar, CASE_LOWER);
        }, array_values($ohlcv));

        $this->dataCount = count($this->data);
        return $this;
    }

    /**
     * Set OHLCV historical dataset (must be sorted chronologically: oldest to newest)
     */
    public function setHistoricalData(array $historicalData): self
    {
        $this->data = array_values($historicalData);
        $this->dataCount = count($this->data);
        return $this;
    }

    // =========================================================================
    // PILLAR 1: OVERLAY & TREND INDICATORS
    // =========================================================================

    /**
     * Simple Moving Average (SMA)
     */
    public function calculateSMA(array $prices, int $period): ?float
    {
        $count = count($prices);
        if ($count < $period || $period <= 0) return null;

        $slice = array_slice($prices, $count - $period, $period);
        return array_sum($slice) / $period;
    }

    /**
     * Exponential Moving Average (EMA)
     */
    public function calculateEMA(array $prices, int $period): ?float
    {
        $count = count($prices);
        if ($count < $period || $period <= 0) return null;

        $multiplier = 2.0 / ($period + 1);
        $ema = array_sum(array_slice($prices, 0, $period)) / $period;

        for ($i = $period; $i < $count; $i++) {
            $ema = (($prices[$i] - $ema) * $multiplier) + $ema;
        }

        return $ema;
    }

    /**
     * Volume Weighted Average Price (VWAP)
     */
    public function calculateVWAP(): ?float
    {
        if ($this->dataCount === 0) return null;

        $cumulativeTPV = 0.0;
        $cumulativeVolume = 0.0;

        foreach ($this->data as $bar) {
            $typicalPrice = (($bar['high'] ?? 0) + ($bar['low'] ?? 0) + ($bar['close'] ?? 0)) / 3.0;
            $volume = (float) ($bar['volume'] ?? 0);

            $cumulativeTPV += ($typicalPrice * $volume);
            $cumulativeVolume += $volume;
        }

        return $cumulativeVolume > 0 ? $cumulativeTPV / $cumulativeVolume : null;
    }

    /**
     * Moving Average Convergence Divergence (MACD)
     */
    public function calculateMACD(array $prices, int $fast = 12, int $slow = 26, int $signal = 9): array
    {
        $fastEma = $this->calculateEMA($prices, $fast);
        $slowEma = $this->calculateEMA($prices, $slow);

        if ($fastEma === null || $slowEma === null) {
            return ['macd' => null, 'signal' => null, 'histogram' => null];
        }

        $macdLine = $fastEma - $slowEma;

        // Calculate MACD history for signal line calculation
        $macdHistory = [];
        $count = count($prices);
        for ($i = $slow; $i <= $count; $i++) {
            $subPrices = array_slice($prices, 0, $i);
            $f = $this->calculateEMA($subPrices, $fast);
            $s = $this->calculateEMA($subPrices, $slow);
            if ($f !== null && $s !== null) {
                $macdHistory[] = $f - $s;
            }
        }

        $signalLine = $this->calculateEMA($macdHistory, $signal);
        $histogram = ($signalLine !== null) ? ($macdLine - $signalLine) : null;

        return [
            'macd'      => round($macdLine, 4),
            'signal'    => $signalLine !== null ? round($signalLine, 4) : null,
            'histogram' => $histogram !== null ? round($histogram, 4) : null,
        ];
    }

    // =========================================================================
    // PILLAR 2: MOMENTUM & VOLATILITY INDICATORS
    // =========================================================================

    /**
     * Relative Strength Index (RSI)
     */
    public function calculateRSI(array $prices, int $period = 14): ?float
    {
        $count = count($prices);
        if ($count <= $period) return null;

        $gains = 0.0;
        $losses = 0.0;

        for ($i = 1; $i <= $period; $i++) {
            $change = $prices[$i] - $prices[$i - 1];
            if ($change >= 0) {
                $gains += $change;
            } else {
                $losses += abs($change);
            }
        }

        $avgGain = $gains / $period;
        $avgLoss = $losses / $period;

        for ($i = $period + 1; $i < $count; $i++) {
            $change = $prices[$i] - $prices[$i - 1];
            if ($change >= 0) {
                $avgGain = (($avgGain * ($period - 1)) + $change) / $period;
                $avgLoss = ($avgLoss * ($period - 1)) / $period;
            } else {
                $avgGain = ($avgGain * ($period - 1)) / $period;
                $avgLoss = (($avgLoss * ($period - 1)) + abs($change)) / $period;
            }
        }

        if ($avgLoss == 0.0) return 100.0;
        $rs = $avgGain / $avgLoss;

        return round(100.0 - (100.0 / (1.0 + $rs)), 2);
    }

    /**
     * Average True Range (ATR)
     */
    public function calculateATR(int $period = 14): ?float
    {
        if ($this->dataCount <= $period) return null;

        $trSum = 0.0;
        for ($i = $this->dataCount - $period; $i < $this->dataCount; $i++) {
            $high = (float) $this->data[$i]['high'];
            $low = (float) $this->data[$i]['low'];
            $prevClose = (float) $this->data[$i - 1]['close'];

            $tr = max($high - $low, abs($high - $prevClose), abs($low - $prevClose));
            $trSum += $tr;
        }

        return round($trSum / $period, 2);
    }

    /**
     * Bollinger Bands
     */
    public function calculateBollingerBands(array $prices, int $period = 20, float $stdDevMult = 2.0): array
    {
        $sma = $this->calculateSMA($prices, $period);
        if ($sma === null) return ['upper' => null, 'middle' => null, 'lower' => null];

        $slice = array_slice($prices, count($prices) - $period, $period);
        $variance = 0.0;
        foreach ($slice as $price) {
            $variance += pow($price - $sma, 2);
        }

        $stdDev = sqrt($variance / $period);

        return [
            'upper'  => round($sma + ($stdDevMult * $stdDev), 2),
            'middle' => round($sma, 2),
            'lower'  => round($sma - ($stdDevMult * $stdDev), 2)
        ];
    }

    // =========================================================================
    // PILLAR 3: VOLUME & CAR TREND METRICS
    // =========================================================================

    /**
     * On-Balance Volume (OBV)
     */
    public function calculateOBV(): float
    {
        if ($this->dataCount < 2) return 0.0;

        $obv = 0.0;
        for ($i = 1; $i < $this->dataCount; $i++) {
            $currentClose = (float) $this->data[$i]['close'];
            $prevClose = (float) $this->data[$i - 1]['close'];
            $volume = (float) $this->data[$i]['volume'];

            if ($currentClose > $prevClose) {
                $obv += $volume;
            } elseif ($currentClose < $prevClose) {
                $obv -= $volume;
            }
        }

        return $obv;
    }

    /**
     * Expanding Mean (CAR - Cumulative Average)
     */
    public function calculateExpandingMean(array $data): array
    {
        $expanding = [];
        $runningSum = 0.0;
        $count = 0;

        foreach ($data as $val) {
            $count++;
            $runningSum += (float) $val;
            $expanding[] = $runningSum / $count;
        }

        return $expanding;
    }

    /**
     * Monotonic Increasing Check
     */
    public function isMonotonicIncreasing(array $arr): bool
    {
        $count = count($arr);
        for ($i = 1; $i < $count; $i++) {
            if ($arr[$i] < $arr[$i - 1]) {
                return false;
            }
        }
        return true;
    }

    // =========================================================================
    // PILLAR 4: SUPPORT, RESISTANCE & PIVOTS
    // =========================================================================

    /**
     * Standard Floor Pivot Points
     */
    public function calculatePivotPoints(float $high, float $low, float $close): array
    {
        $pivot = ($high + $low + $close) / 3.0;

        return [
            'pivot' => round($pivot, 2),
            'r1'    => round((2 * $pivot) - $low, 2),
            's1'    => round((2 * $pivot) - $high, 2),
            'r2'    => round($pivot + ($high - $low), 2),
            's2'    => round($pivot - ($high - $low), 2),
            'r3'    => round($high + (2 * ($pivot - $low)), 2),
            's3'    => round($low - (2 * ($high - $pivot)), 2),
        ];
    }

    /**
     * Fibonacci Retracement Levels
     */
    public function calculateFibonacciRetracement(float $swingHigh, float $swingLow): array
    {
        $diff = $swingHigh - $swingLow;

        return [
            'level_0.0'   => round($swingHigh, 2),
            'level_23.6' => round($swingHigh - (0.236 * $diff), 2),
            'level_38.2' => round($swingHigh - (0.382 * $diff), 2),
            'level_50.0' => round($swingHigh - (0.500 * $diff), 2),
            'level_61.8' => round($swingHigh - (0.618 * $diff), 2),
            'level_78.6' => round($swingHigh - (0.786 * $diff), 2),
            'level_100.0'=> round($swingLow, 2),
        ];
    }

    // =========================================================================
    // PILLAR 5: CANDLESTICK & CHART PATTERNS
    // =========================================================================

    /**
     * Detects Japanese Candlestick Patterns on the latest bar
     */
    public function detectCandlestickPattern(): string
    {
        if ($this->dataCount < 2) return 'NEUTRAL';

        $last = $this->data[$this->dataCount - 1];
        $prev = $this->data[$this->dataCount - 2];

        $open  = (float) $last['open'];
        $high  = (float) $last['high'];
        $low   = (float) $last['low'];
        $close = (float) $last['close'];

        $body = abs($close - $open);
        $range = $high - $low;

        if ($range == 0) return 'NEUTRAL';

        // Doji
        if (($body / $range) <= 0.08) {
            return 'DOJI (Indecision)';
        }

        // Bullish Engulfing
        if ($prev['close'] < $prev['open'] && $close > $open && $close > $prev['open'] && $open < $prev['close']) {
            return 'BULLISH ENGULFING';
        }

        // Bearish Engulfing
        if ($prev['close'] > $prev['open'] && $close < $open && $close < $prev['open'] && $open > $prev['close']) {
            return 'BEARISH ENGULFING';
        }

        // Hammer / Pinbar
        $lowerShadow = min($open, $close) - $low;
        $upperShadow = $high - max($open, $close);
        if ($lowerShadow >= (2 * $body) && $upperShadow <= (0.5 * $body)) {
            return 'BULLISH HAMMER';
        }

        return 'NO_PATTERN';
    }

    // =========================================================================
    // PILLAR 6: SMART MONEY CONCEPTS (SMC) & MARKET STRUCTURE
    // =========================================================================

    /**
     * Identifies Fair Value Gaps (FVG) / Imbalances in 3-candle windows
     */
    public function detectFairValueGaps(): array
    {
        $fvgs = [];
        if ($this->dataCount < 3) return $fvgs;

        for ($i = 2; $i < $this->dataCount; $i++) {
            $candle1 = $this->data[$i - 2];
            $candle3 = $this->data[$i];

            // Bullish FVG: Low of candle 3 is higher than High of candle 1
            if ((float) $candle3['low'] > (float) $candle1['high']) {
                $fvgs[] = [
                    'index'     => $i - 1,
                    'type'      => 'BULLISH_FVG',
                    'gap_bottom'=> round((float) $candle1['high'], 2),
                    'gap_top'   => round((float) $candle3['low'], 2),
                ];
            }
            // Bearish FVG: High of candle 3 is lower than Low of candle 1
            elseif ((float) $candle3['high'] < (float) $candle1['low']) {
                $fvgs[] = [
                    'index'     => $i - 1,
                    'type'      => 'BEARISH_FVG',
                    'gap_top'   => round((float) $candle1['low'], 2),
                    'gap_bottom'=> round((float) $candle3['high'], 2),
                ];
            }
        }

        return $fvgs;
    }

    /**
     * Evaluates Market Structure Break (BOS - Break of Structure)
     */
    public function evaluateMarketStructure(): string
    {
        if ($this->dataCount < 20) return 'CONSOLIDATION';

        $highs = array_column(array_slice($this->data, -20), 'high');
        $lows  = array_column(array_slice($this->data, -20), 'low');
        $latestClose = end($this->data)['close'];

        $recentResistance = max(array_slice($highs, 0, 15));
        $recentSupport    = min(array_slice($lows, 0, 15));

        if ($latestClose > $recentResistance) {
            return 'BOS_BULLISH (Breakout Above Structure)';
        } elseif ($latestClose < $recentSupport) {
            return 'BOS_BEARISH (Breakdown Below Structure)';
        }

        return 'RANGING / INSIDE STRUCTURE';
    }


    // =========================================================================
    // NEW ADDITIONS 1: VOLATILITY & CHANNEL INDICATORS
    // =========================================================================

    /**
     * Keltner Channels (ATR-based volatility envelope around an EMA middle band)
     */
    public function calculateKeltnerChannels(array $prices, int $emaPeriod = 20, int $atrPeriod = 10, float $multiplier = 2.0): array
    {
        $middle = $this->calculateEMA($prices, $emaPeriod);
        $atr    = $this->calculateATR($atrPeriod);

        if ($middle === null || $atr === null) {
            return ['upper' => null, 'middle' => null, 'lower' => null];
        }

        return [
            'upper'  => round($middle + ($multiplier * $atr), 2),
            'middle' => round($middle, 2),
            'lower'  => round($middle - ($multiplier * $atr), 2)
        ];
    }

    /**
     * Donchian Channels (Identifies absolute Highs/Lows over a lookback window)
     */
    public function calculateDonchianChannels(int $period = 20): array
    {
        if ($this->dataCount < $period) {
            return ['upper' => null, 'middle' => null, 'lower' => null];
        }

        $slice = array_slice($this->data, $this->dataCount - $period, $period);
        $highs = array_column($slice, 'high');
        $lows  = array_column($slice, 'low');

        $upper = max($highs);
        $lower = min($lows);
        $middle = ($upper + $lower) / 2.0;

        return [
            'upper'  => round($upper, 2),
            'middle' => round($middle, 2),
            'lower'  => round($lower, 2)
        ];
    }

    // =========================================================================
    // NEW ADDITIONS 2: MOMENTUM & OSCILLATOR INDICATORS
    // =========================================================================

    /**
     * Stochastic Oscillator (%K and %D)
     */
    public function calculateStochastic(int $kPeriod = 14, int $dPeriod = 3): array
    {
        if ($this->dataCount < $kPeriod) {
            return ['percent_k' => null, 'percent_d' => null];
        }

        $slice = array_slice($this->data, $this->dataCount - $kPeriod, $kPeriod);
        $highs = array_column($slice, 'high');
        $lows  = array_column($slice, 'low');

        $highestHigh = max($highs);
        $lowestLow   = min($lows);
        $currentClose = (float) end($this->data)['close'];

        $range = $highestHigh - $lowestLow;
        $percentK = ($range > 0.0) ? (($currentClose - $lowestLow) / $range) * 100.0 : 50.0;

        // Calculate smooth %D over past %K values
        $kHistory = [];
        for ($i = $this->dataCount - $kPeriod - $dPeriod + 1; $i <= $this->dataCount - $kPeriod; $i++) {
            if ($i < 0) continue;
            $subSlice = array_slice($this->data, $i, $kPeriod);
            $subHighs = array_column($subSlice, 'high');
            $subLows  = array_column($subSlice, 'low');
            $subClose = (float) $subSlice[count($subSlice) - 1]['close'];

            $subRange = max($subHighs) - min($subLows);
            $kHistory[] = ($subRange > 0.0) ? (($subClose - min($subLows)) / $subRange) * 100.0 : 50.0;
        }

        $percentD = !empty($kHistory) ? array_sum($kHistory) / count($kHistory) : $percentK;

        return [
            'percent_k' => round($percentK, 2),
            'percent_d' => round($percentD, 2)
        ];
    }

    /**
     * Commodity Channel Index (CCI)
     */
    public function calculateCCI(int $period = 20): ?float
    {
        if ($this->dataCount < $period) return null;

        $slice = array_slice($this->data, $this->dataCount - $period, $period);
        $typicalPrices = [];

        foreach ($slice as $bar) {
            $typicalPrices[] = (($bar['high'] ?? 0) + ($bar['low'] ?? 0) + ($bar['close'] ?? 0)) / 3.0;
        }

        $smaTP = array_sum($typicalPrices) / $period;

        $meanDeviation = 0.0;
        foreach ($typicalPrices as $tp) {
            $meanDeviation += abs($tp - $smaTP);
        }
        $meanDeviation /= $period;

        if ($meanDeviation == 0.0) return 0.0;

        $currentTP = end($typicalPrices);
        return round(($currentTP - $smaTP) / (0.015 * $meanDeviation), 2);
    }

    /**
     * Rate of Change (ROC)
     */
    public function calculateROC(array $prices, int $period = 12): ?float
    {
        $count = count($prices);
        if ($count <= $period) return null;

        $currentClose = $prices[$count - 1];
        $prevClose = $prices[$count - 1 - $period];

        if ($prevClose <= 0.0) return 0.0;

        return round((($currentClose - $prevClose) / $prevClose) * 100.0, 2);
    }

    /**
     * Williams %R
     */
    public function calculateWilliamsR(int $period = 14): ?float
    {
        if ($this->dataCount < $period) return null;

        $slice = array_slice($this->data, $this->dataCount - $period, $period);
        $highestHigh = max(array_column($slice, 'high'));
        $lowestLow   = min(array_column($slice, 'low'));
        $currentClose = (float) end($this->data)['close'];

        $range = $highestHigh - $lowestLow;
        if ($range == 0.0) return -50.0;

        return round((($highestHigh - $currentClose) / $range) * -100.0, 2);
    }

    // =========================================================================
    // NEW ADDITIONS 3: TREND STRENGTH & ADVANCED INDICATORS
    // =========================================================================

    /**
     * Supertrend Indicator (Trend direction + Dynamic Trailing Stop)
     */
    public function calculateSupertrend(int $period = 10, float $multiplier = 3.0): array
    {
        if ($this->dataCount < $period) {
            return ['trend' => 'UNKNOWN', 'supertrend' => null];
        }

        $atr = $this->calculateATR($period);
        $lastBar = end($this->data);

        $hl2 = ((float) $lastBar['high'] + (float) $lastBar['low']) / 2.0;
        $basicUpper = $hl2 + ($multiplier * $atr);
        $basicLower = $hl2 - ($multiplier * $atr);

        $currentClose = (float) $lastBar['close'];
        $trend = ($currentClose > $basicUpper) ? 'BULLISH' : (($currentClose < $basicLower) ? 'BEARISH' : 'NEUTRAL');
        $line = ($trend === 'BULLISH') ? $basicLower : $basicUpper;

        return [
            'trend'      => $trend,
            'supertrend' => round($line, 2)
        ];
    }

    /**
     * Parabolic SAR (Stop and Reverse)
     */
    public function calculateParabolicSAR(float $step = 0.02, float $maxStep = 0.20): ?float
    {
        if ($this->dataCount < 5) return null;

        $isLong = $this->data[1]['close'] > $this->data[0]['close'];
        $sar = $isLong ? (float) $this->data[0]['low'] : (float) $this->data[0]['high'];
        $ep  = $isLong ? (float) $this->data[0]['high'] : (float) $this->data[0]['low'];
        $af  = $step;

        for ($i = 1; $i < $this->dataCount; $i++) {
            $high = (float) $this->data[$i]['high'];
            $low  = (float) $this->data[$i]['low'];

            $sar = $sar + ($af * ($ep - $sar));

            if ($isLong) {
                if ($low < $sar) {
                    $isLong = false;
                    $sar = $ep;
                    $ep  = $low;
                    $af  = $step;
                } else {
                    if ($high > $ep) {
                        $ep = $high;
                        $af = min($maxStep, $af + $step);
                    }
                }
            } else {
                if ($high > $sar) {
                    $isLong = true;
                    $sar = $ep;
                    $ep  = $high;
                    $af  = $step;
                } else {
                    if ($low < $ep) {
                        $ep = $low;
                        $af = min($maxStep, $af + $step);
                    }
                }
            }
        }

        return round($sar, 2);
    }

    // =========================================================================
    // NEW ADDITIONS 4: VOLUME & ACCUMULATION ANALYTICS
    // =========================================================================

    /**
     * Chaikin Money Flow (CMF)
     */
    public function calculateCMF(int $period = 20): ?float
    {
        if ($this->dataCount < $period) return null;

        $slice = array_slice($this->data, $this->dataCount - $period, $period);
        $sumMFV = 0.0;
        $sumVol = 0.0;

        foreach ($slice as $bar) {
            $high   = (float) ($bar['high'] ?? 0);
            $low    = (float) ($bar['low'] ?? 0);
            $close  = (float) ($bar['close'] ?? 0);
            $volume = (float) ($bar['volume'] ?? 0);

            $range = $high - $low;
            $mfm = ($range > 0.0) ? ((($close - $low) - ($high - $close)) / $range) : 0.0;

            $sumMFV += ($mfm * $volume);
            $sumVol += $volume;
        }

        return $sumVol > 0 ? round($sumMFV / $sumVol, 4) : 0.0;
    }

    /**
     * Volume Price Trend (VPT)
     */
    public function calculateVPT(array $closePrices, array $volumes): float
    {
        $count = count($closePrices);
        if ($count < 2 || $count !== count($volumes)) return 0.0;

        $vpt = 0.0;
        for ($i = 1; $i < $count; $i++) {
            $prevClose = $closePrices[$i - 1];
            if ($prevClose <= 0) continue;

            $pctChange = ($closePrices[$i] - $prevClose) / $prevClose;
            $vpt += ($volumes[$i] * $pctChange);
        }

        return round($vpt, 2);
    }

    /**
     * Money Flow Index (MFI) - Volume-Weighted RSI
     */
    // public function calculateMFI(int $period = 14): ?float
    // {
    //     if ($this->dataCount <= $period) return null;

    //     $positiveFlow = 0.0;
    //     $negativeFlow = 0.0;

    //     for ($i = $this->dataCount - $period; $i < $this->dataCount; $i++) {
    //         $currTP = (($this->data[$i]['high'] + $this->data[$i]['low'] + $this->data[$i]['close']) / 3.0);
    //         $prevTP = (($this->data[$i - 1]['high'] + $this->data[$i - 1]['low'] + $this->data[$i - 1]['close']) / 3.0);
    //         $rawMoneyFlow = $currTP * (float) $this->data[$i]['volume'];

    //         if ($currTP > $prevTP) {
    //             $positiveFlow += $rawMoneyFlow;
    //         } elseif ($currTP < $prevTP) {
    //             $negativeFlow += $rawMoneyFlow;
    //         }
    //     }

    //     if ($negativeFlow == 0.0) return 100.0;

    //     $moneyRatio = $positiveFlow / $negativeFlow;
    //     return round(100.0 - (100.0 / (1.0 + $moneyRatio)), 2);
    // }
    public function calculateMFI(int $period = 14): ?float
    {
        if ($this->dataCount <= $period) return null;

        $positiveFlow = 0.0;
        $negativeFlow = 0.0;

        for ($i = $this->dataCount - $period; $i < $this->dataCount; $i++) {
            // Ensure index $i - 1 exists
            if (!isset($this->data[$i]) || !isset($this->data[$i - 1])) {
                continue;
            }

            // Safely extract values with case/key fallback checks
            $currHigh  = (float) ($this->data[$i]['high'] ?? $this->data[$i]['High'] ?? 0);
            $currLow   = (float) ($this->data[$i]['low'] ?? $this->data[$i]['Low'] ?? 0);
            $currClose = (float) ($this->data[$i]['close'] ?? $this->data[$i]['Close'] ?? 0);
            $volume    = (float) ($this->data[$i]['volume'] ?? $this->data[$i]['Volume'] ?? $this->data[$i]['vol'] ?? 0);

            $prevHigh  = (float) ($this->data[$i - 1]['high'] ?? $this->data[$i - 1]['High'] ?? 0);
            $prevLow   = (float) ($this->data[$i - 1]['low'] ?? $this->data[$i - 1]['Low'] ?? 0);
            $prevClose = (float) ($this->data[$i - 1]['close'] ?? $this->data[$i - 1]['Close'] ?? 0);

            $currTP = ($currHigh + $currLow + $currClose) / 3.0;
            $prevTP = ($prevHigh + $prevLow + $prevClose) / 3.0;

            $rawMoneyFlow = $currTP * $volume;

            if ($currTP > $prevTP) {
                $positiveFlow += $rawMoneyFlow;
            } elseif ($currTP < $prevTP) {
                $negativeFlow += $rawMoneyFlow;
            }
        }

        if ($negativeFlow == 0.0) {
            return ($positiveFlow > 0.0) ? 100.0 : 50.0;
        }

        $moneyRatio = $positiveFlow / $negativeFlow;
        return round(100.0 - (100.0 / (1.0 + $moneyRatio)), 2);
    }

    /**
     * Volume Ratio (Relative Surge against N-day SMA)
     */
    public function calculateVolumeRatio(int $period = 20): ?float
    {
        if ($this->dataCount < $period) return null;

        $volumes = array_column($this->data, 'volume');
        $latestVolume = (float) end($volumes);
        $slice = array_slice($volumes, $this->dataCount - $period, $period);
        $avgVolume = array_sum($slice) / $period;

        return $avgVolume > 0 ? round($latestVolume / $avgVolume, 2) : null;
    }

    // =========================================================================
    // STATISTICAL & QUANTITATIVE FUNCTIONS
    // =========================================================================

    /**
     * Calculates Linear Regression Slope and R-Squared (Trend Direction & Strength)
     */
    public function calculateLinearRegression(array $prices, int $period = 20): array
    {
        $count = count($prices);
        if ($count < $period) return ['slope' => null, 'r_squared' => null];

        $y = array_slice($prices, $count - $period, $period);
        $x = range(1, $period);

        $sumX = array_sum($x);
        $sumY = array_sum($y);
        
        $sumXY = 0;
        $sumX2 = 0;
        $sumY2 = 0;

        for ($i = 0; $i < $period; $i++) {
            $sumXY += ($x[$i] * $y[$i]);
            $sumX2 += ($x[$i] * $x[$i]);
            $sumY2 += ($y[$i] * $y[$i]);
        }

        $denominator = ($period * $sumX2) - ($sumX * $sumX);
        if ($denominator == 0) return ['slope' => 0.0, 'r_squared' => 0.0];

        $slope = (($period * $sumXY) - ($sumX * $sumY)) / $denominator;
        
        // R-Squared calculation
        $numR = ($period * $sumXY) - ($sumX * $sumY);
        $denR = sqrt((($period * $sumX2) - ($sumX * $sumX)) * (($period * $sumY2) - ($sumY * $sumY)));
        $rSquared = ($denR != 0) ? pow($numR / $denR, 2) : 0.0;

        return [
            'slope'     => round($slope, 4),
            'r_squared' => round($rSquared, 4)
        ];
    }

    /**
     * Calculates Price Z-Score (Standard Deviations from the Mean)
     */
    public function calculateZScore(array $prices, int $period = 20): ?float
    {
        $count = count($prices);
        if ($count < $period) return null;

        $slice = array_slice($prices, $count - $period, $period);
        $mean = array_sum($slice) / $period;

        $variance = 0.0;
        foreach ($slice as $p) {
            $variance += pow($p - $mean, 2);
        }
        $stdDev = sqrt($variance / $period);

        if ($stdDev == 0.0) return 0.0;

        $currentPrice = end($prices);
        return round(($currentPrice - $mean) / $stdDev, 2);
    }

    // =========================================================================
    // ADVANCED VOLUME & ORDER FLOW
    // =========================================================================

    /**
     * Calculates Force Index (Alexander Elder's momentum/volume metric)
     */
    public function calculateForceIndex(int $period = 13): ?float
    {
        if ($this->dataCount < $period + 1) return null;

        $rawForce = [];
        for ($i = $this->dataCount - $period; $i < $this->dataCount; $i++) {
            $change = (float)$this->data[$i]['close'] - (float)$this->data[$i - 1]['close'];
            $volume = (float)$this->data[$i]['volume'];
            $rawForce[] = $change * $volume;
        }

        // Return 13-period EMA of Force Index
        return $this->calculateEMA($rawForce, $period);
    }

    /**
     * Calculates Ease of Movement (EOM)
     */
    public function calculateEaseOfMovement(int $period = 14): ?float
    {
        if ($this->dataCount < $period + 1) return null;

        $eomValues = [];
        for ($i = $this->dataCount - $period; $i < $this->dataCount; $i++) {
            $currHigh = (float)$this->data[$i]['high'];
            $currLow  = (float)$this->data[$i]['low'];
            $prevHigh = (float)$this->data[$i - 1]['high'];
            $prevLow  = (float)$this->data[$i - 1]['low'];
            $volume   = (float)$this->data[$i]['volume'];

            $distanceMoved = (($currHigh + $currLow) / 2.0) - (($prevHigh + $prevLow) / 2.0);
            $boxRatio = ($volume / 100000000.0) / max(0.0001, ($currHigh - $currLow));

            $eomValues[] = $boxRatio > 0 ? $distanceMoved / $boxRatio : 0.0;
        }

        return round(array_sum($eomValues) / $period, 4);
    }

    // =========================================================================
    // REGIME DETECTION & NOISE ANALYTICS
    // =========================================================================

    /**
     * Kaufman's Efficiency Ratio (ER)
     * Values near 1.0 indicate a strong trend; values near 0.0 indicate high noise/chop.
     */
    public function calculateEfficiencyRatio(array $prices, int $period = 10): ?float
    {
        $count = count($prices);
        if ($count <= $period) return null;

        $netChange = abs($prices[$count - 1] - $prices[$count - 1 - $period]);
        
        $sumIndividualChanges = 0.0;
        for ($i = $count - $period; $i < $count; $i++) {
            $sumIndividualChanges += abs($prices[$i] - $prices[$i - 1]);
        }

        if ($sumIndividualChanges == 0.0) return 0.0;

        return round($netChange / $sumIndividualChanges, 4);
    }

    /**
     * Choppiness Index (CHOP)
     * Below 38.2 = Strong Trend | Above 61.8 = Consolidation / Chop
     */
    public function calculateChoppinessIndex(int $period = 14): ?float
    {
        if ($this->dataCount < $period + 1) return null;

        $atr = $this->calculateATR(1); // 1-period ATR (True Range)
        if ($atr === null) return null;

        $sumTR = 0.0;
        for ($i = $this->dataCount - $period; $i < $this->dataCount; $i++) {
            $high = (float)$this->data[$i]['high'];
            $low  = (float)$this->data[$i]['low'];
            $prevClose = (float)$this->data[$i - 1]['close'];
            
            $tr = max($high - $low, abs($high - $prevClose), abs($low - $prevClose));
            $sumTR += $tr;
        }

        $slice = array_slice($this->data, $this->dataCount - $period, $period);
        $maxHigh = max(array_column($slice, 'high'));
        $minLow  = min(array_column($slice, 'low'));

        $range = $maxHigh - $minLow;
        if ($range == 0.0) return 100.0;

        $chop = 100.0 * (log10($sumTR / $range) / log10($period));
        return round($chop, 2);
    }

    // =========================================================================
    // RISK ASYMMETRY & DOWNSIDE VOLATILITY
    // =========================================================================

    /**
     * Downside Deviation (Semi-Variance for downside risk estimation)
     */
    public function calculateDownsideDeviation(array $returns, float $targetReturn = 0.0): ?float
    {
        $count = count($returns);
        if ($count === 0) return null;

        $squaredDownsideDiffs = 0.0;
        foreach ($returns as $r) {
            if ($r < $targetReturn) {
                $squaredDownsideDiffs += pow($r - $targetReturn, 2);
            }
        }

        return round(sqrt($squaredDownsideDiffs / $count), 4);
    }

    /**
     * Mansfield Relative Strength vs Benchmark Index
     */
    public function calculateMansfieldRelativeStrength(array $stockPrices, array $indexPrices, int $period = 52): ?float
    {
        $count = count($stockPrices);
        if ($count < $period || $count !== count($indexPrices)) return null;

        $relativePerformance = [];
        for ($i = 0; $i < $count; $i++) {
            if ($indexPrices[$i] <= 0) continue;
            $relativePerformance[] = ($stockPrices[$i] / $indexPrices[$i]) * 100.0;
        }

        $smaRP = $this->calculateSMA($relativePerformance, $period);
        if ($smaRP === null || $smaRP == 0.0) return null;

        $currentRP = end($relativePerformance);
        $mrs = (($currentRP / $smaRP) - 1.0) * 100.0;

        return round($mrs, 2);
    }

    // =========================================================================
    // PILLAR 9: HARMONIC & PATTERN RATIOS
    // =========================================================================

    /**
     * Calculates AB=CD Harmonic Pattern Ratios to identify potential reversal zones (PRZ)
     *
     * @param float $xA Price leg XA
     * @param float $aB Price leg AB
     * @param float $bC Price leg BC
     * @param float $cD Price leg CD
     * @return array Matrix of retracement ratios and pattern validity
     */
    public function calculateHarmonicRatios(float $xA, float $aB, float $bC, float $cD): array
    {
        if ($xA == 0.0 || $aB == 0.0 || $bC == 0.0) {
            return ['ab_retracement' => null, 'bc_projection' => null, 'is_valid_abcd' => false];
        }

        $abRetracement = abs($aB / $xA);
        $bcProjection  = abs($cD / $bC);

        // AB=CD validity: AB retracement ~0.618 and BC projection ~1.618 (with 5% tolerance)
        $isValidAbcd = (abs($abRetracement - 0.618) <= 0.05) && (abs($bcProjection - 1.618) <= 0.10);

        return [
            'ab_retracement' => round($abRetracement, 4),
            'bc_projection'  => round($bcProjection, 4),
            'is_valid_abcd'  => $isValidAbcd
        ];
    }

    // =========================================================================
    // PILLAR 10: ADAPTIVE INDICATORS
    // =========================================================================

    /**
     * Kaufman's Adaptive Moving Average (KAMA)
     * Dynamically adjusts smoothing based on market efficiency (fast in trends, slow in noise).
     *
     * @param array $prices Sequential close prices
     * @param int $period Lookback period for Efficiency Ratio (default 10)
     * @param int $fastFast Lookback for fast EMA (default 2)
     * @param int $slowSlow Lookback for slow EMA (default 30)
     * @return float|null
     */
    public function calculateKAMA(array $prices, int $period = 10, int $fastFast = 2, int $slowSlow = 30): ?float
    {
        $er = $this->calculateEfficiencyRatio($prices, $period);
        if ($er === null) return null;

        $fastSC = 2.0 / ($fastFast + 1);
        $slowSC = 2.0 / ($slowSlow + 1);

        // Smoothing Constant (SC)
        $sc = pow($er * ($fastSC - $slowSC) + $slowSC, 2);

        $count = count($prices);
        $kama = $prices[$count - $period - 1]; // Initial seed

        for ($i = $count - $period; $i < $count; $i++) {
            $kama = $kama + ($sc * ($prices[$i] - $kama));
        }

        return round($kama, 2);
    }

    // =========================================================================
    // PILLAR 11: MICROSTRUCTURE & TAPE ANALYTICS
    // =========================================================================

    /**
     * Volume Delta (Measures net buying vs selling pressure within the bar)
     * Approximates order flow delta based on close relative to the bar range.
     *
     * @return float|null Net buying volume (+) or selling volume (-)
     */
    public function calculateVolumeDelta(): ?float
    {
        if ($this->dataCount === 0) return null;

        $latest = end($this->data);
        if (!is_array($latest)) return null;

        // Safely extract values with case/key fallback checks
        $high   = (float) ($latest['high']   ?? $latest['High']   ?? 0);
        $low    = (float) ($latest['low']    ?? $latest['Low']    ?? 0);
        $close  = (float) ($latest['close']  ?? $latest['Close']  ?? 0);
        $volume = (float) ($latest['volume'] ?? $latest['Volume'] ?? $latest['vol'] ?? 0);

        $range = $high - $low;
        if ($range == 0.0) return 0.0;

        // Buying percent vs Selling percent
        $buyPct  = ($close - $low) / $range;
        $sellPct = ($high - $close) / $range;

        $buyVolume  = $volume * $buyPct;
        $sellVolume = $volume * $sellPct;

        return round($buyVolume - $sellVolume, 2);
    }

    /**
     * Ulcer Index (UI) - Measures downside risk depth and drawdown duration
     *
     * @param array $prices Sequential close prices
     * @param int $period Lookback window (default 14)
     * @return float|null
     */
    public function calculateUlcerIndex(array $prices, int $period = 14): ?float
    {
        $count = count($prices);
        if ($count < $period) return null;

        $slice = array_slice($prices, $count - $period, $period);
        $squaredDrawdowns = 0.0;
        $maxPrice = $slice[0];

        foreach ($slice as $price) {
            if ($price > $maxPrice) {
                $maxPrice = $price;
            }
            $percentDrawdown = (($price - $maxPrice) / $maxPrice) * 100.0;
            $squaredDrawdowns += pow($percentDrawdown, 2);
        }

        return round(sqrt($squaredDrawdowns / $period), 2);
    }

    // =========================================================================
    // PILLAR 12: STATISTICAL MEAN-REVERSION
    // =========================================================================

    /**
     * Hurst Exponent (Approximate)
     * Quantifies time-series behavior:
     * H < 0.5 -> Mean-reverting (ranging)
     * H = 0.5 -> Random Walk (Geometric Brownian Motion)
     * H > 0.5 -> Persistent Trend
     *
     * @param array $prices
     * @param int $period
     * @return float|null
     */
    public function calculateHurstExponent(array $prices, int $period = 20): ?float
    {
        $count = count($prices);
        if ($count < $period) return null;

        $slice = array_slice($prices, $count - $period, $period);
        
        // Log returns
        $logReturns = [];
        for ($i = 1; $i < $period; $i++) {
            if ($slice[$i - 1] <= 0) continue;
            $logReturns[] = log($slice[$i] / $slice[$i - 1]);
        }

        $mean = array_sum($logReturns) / count($logReturns);
        
        // Cumulative deviations
        $cumDevs = [];
        $runningSum = 0.0;
        foreach ($logReturns as $r) {
            $runningSum += ($r - $mean);
            $cumDevs[] = $runningSum;
        }

        $range = max($cumDevs) - min($cumDevs);
        
        // Standard Deviation
        $variance = 0.0;
        foreach ($logReturns as $r) {
            $variance += pow($r - $mean, 2);
        }
        $stdDev = sqrt($variance / count($logReturns));

        if ($stdDev == 0.0 || $range == 0.0) return 0.5;

        $rescaledRange = $range / $stdDev;
        $hurst = log($rescaledRange) / log($period);

        return round(min(1.0, max(0.0, $hurst)), 4);
    }

    /**
     * Detrended Price Oscillator (DPO)
     * Removes long-term trends to isolate short-term cycle high/lows.
     *
     * @param array $prices
     * @param int $period
     * @return float|null
     */
    public function calculateDPO(array $prices, int $period = 20): ?float
    {
        $count = count($prices);
        $backShift = (int) floor(($period / 2) + 1);
        
        if ($count < $period + $backShift) return null;

        // SMA shifted back by (period / 2 + 1)
        $subPrices = array_slice($prices, 0, $count - $backShift);
        $sma = $this->calculateSMA($subPrices, $period);

        if ($sma === null) return null;

        $currentClose = end($prices);
        return round($currentClose - $sma, 2);
    }

    /**
     * Value at Risk (VaR) - Historical Method
     * Estimates maximum expected loss at a given confidence level (e.g., 95%).
     *
     * @param array $returns Array of historical percentage returns
     * @param float $confidence Confidence level (e.g., 0.95 for 95%)
     * @return float Expected maximum loss as a percentage
     */
    public function calculateHistoricalVaR(array $returns, float $confidence = 0.95): float
    {
        if (empty($returns)) return 0.0;

        sort($returns); // Sort ascending (worst returns first)
        $index = (int) floor((1.0 - $confidence) * count($returns));

        return round(abs($returns[$index] ?? 0.0), 4);
    }

    /**
     * Beta calculation vs Benchmark Index
     *
     * @param array $stockReturns Percentage daily returns of the stock
     * @param array $indexReturns Percentage daily returns of the benchmark index
     * @return float|null Beta coefficient
     */
    public function calculateBeta(array $stockReturns, array $indexReturns): ?float
    {
        $count = count($stockReturns);
        if ($count === 0 || $count !== count($indexReturns)) return null;

        $meanStock = array_sum($stockReturns) / $count;
        $meanIndex = array_sum($indexReturns) / $count;

        $covariance = 0.0;
        $indexVariance = 0.0;

        for ($i = 0; $i < $count; $i++) {
            $diffIndex = $indexReturns[$i] - $meanIndex;
            $covariance += ($stockReturns[$i] - $meanStock) * $diffIndex;
            $indexVariance += pow($diffIndex, 2);
        }

        if ($indexVariance == 0.0) return 1.0;

        return round($covariance / $indexVariance, 4);
    }

    // =========================================================================
    // PILLAR 13: ADVANCED QUANTITATIVE & TAIL RISK METRICS
    // =========================================================================

    /**
     * TTMSqueeze (Bollinger Band / Keltner Channel Volatility Squeeze)
     * Identifies periods of extreme volatility compression before explosive breakouts.
     *
     * @param array $prices
     * @param int $period
     * @return array Squeeze status and momentum value
     */
    public function calculateTTMSqueeze(array $prices, int $period = 20): array
    {
        $bb = $this->calculateBollingerBands($prices, $period, 2.0);
        $kc = $this->calculateKeltnerChannels($prices, $period, $period, 1.5);

        if ($bb['upper'] === null || $kc['upper'] === null) {
            return ['is_squeezed' => false, 'momentum' => null];
        }

        // Squeeze is active when Bollinger Bands fall ENTIRELY inside Keltner Channels
        $isSqueezed = ($bb['upper'] < $kc['upper']) && ($bb['lower'] > $kc['lower']);

        // Linear Regression of (Price - Smoothed Midline) for Momentum Histogram
        $midline = ($kc['upper'] + $kc['lower']) / 2.0;
        $currentClose = end($prices);
        $momentum = round($currentClose - $midline, 2);

        return [
            'is_squeezed' => $isSqueezed,
            'momentum'    => $momentum
        ];
    }

    /**
     * Expected Shortfall (Conditional Value at Risk - CVaR)
     * Calculates the average loss expected in the worst alpha% tail scenarios (Tail Risk).
     *
     * @param array $returns Array of historical percentage returns
     * @param float $confidence Confidence level (default 95% -> 5% worst cases)
     * @return float Average expected loss in tail cases
     */
    public function calculateCVaR(array $returns, float $confidence = 0.95): float
    {
        if (empty($returns)) return 0.0;

        sort($returns); // Sort ascending (worst losses first)
        $cutoffIndex = (int) floor((1.0 - $confidence) * count($returns));
        $cutoffIndex = max(1, $cutoffIndex);

        $tailLosses = array_slice($returns, 0, $cutoffIndex);
        $averageTailLoss = array_sum($tailLosses) / count($tailLosses);

        return round(abs($averageTailLoss), 4);
    }

    /**
     * Sortino Ratio (Downside-adjusted Risk/Reward Metric)
     * Evaluates return relative ONLY to bad (downside) volatility.
     *
     * @param array $returns Historical daily returns
     * @param float $riskFreeRate Annualized risk-free rate (e.g. 0.05 for 5%)
     * @return float|null
     */
    public function calculateSortinoRatio(array $returns, float $riskFreeRate = 0.05): ?float
    {
        $count = count($returns);
        if ($count === 0) return null;

        $meanReturn = array_sum($returns) / $count;
        $dailyRf = $riskFreeRate / 252.0; // Daily risk-free conversion

        $downsideDev = $this->calculateDownsideDeviation($returns, $dailyRf);
        if ($downsideDev === null || $downsideDev == 0.0) return null;

        // Annualized Sortino Ratio
        return round((($meanReturn - $dailyRf) / $downsideDev) * sqrt(252), 2);
    }

    /**
     * Coppock Curve (Long-term momentum oscillator for major market bottoms)
     *
     * @param array $prices Sequential close prices
     * @param int $wmaPeriod Weighted Moving Average period (default 10)
     * @param int $rocLong Long Rate of Change lookback (default 14)
     * @param int $rocShort Short Rate of Change lookback (default 11)
     * @return float|null
     */
    public function calculateCoppockCurve(array $prices, int $wmaPeriod = 10, int $rocLong = 14, int $rocShort = 11): ?float
    {
        $count = count($prices);
        if ($count < $rocLong + $wmaPeriod) return null;

        $rocSumSeries = [];
        for ($i = $count - $wmaPeriod; $i < $count; $i++) {
            $subPrices = array_slice($prices, 0, $i + 1);
            $roc1 = $this->calculateROC($subPrices, $rocLong);
            $roc2 = $this->calculateROC($subPrices, $rocShort);

            if ($roc1 !== null && $roc2 !== null) {
                $rocSumSeries[] = $roc1 + $roc2;
            }
        }

        if (count($rocSumSeries) < $wmaPeriod) return null;

        // Apply Weighted Moving Average (WMA) to the sum of ROCs
        $weights = range(1, $wmaPeriod);
        $weightSum = array_sum($weights);
        $wma = 0.0;

        for ($k = 0; $k < $wmaPeriod; $k++) {
            $wma += ($rocSumSeries[$k] * $weights[$k]);
        }

        return round($wma / $weightSum, 2);
    }

    /**
     * Ulcer Performance Index (UPI / Martin Ratio)
     * Measures risk-adjusted performance using drawdown depth/duration rather than variance.
     *
     * @param array $prices Sequential close prices
     * @param float $riskFreeRate Annualized risk-free rate
     * @return float|null
     */
    public function calculateMartinRatio(array $prices, float $riskFreeRate = 0.05): ?float
    {
        $ui = $this->calculateUlcerIndex($prices, count($prices));
        if ($ui === null || $ui == 0.0) return null;

        $startPrice = $prices[0];
        $endPrice   = end($prices);

        if ($startPrice <= 0) return null;

        $totalReturn = ($endPrice - $startPrice) / $startPrice;
        return round(($totalReturn - $riskFreeRate) / $ui, 2);
    }

    /**
     * Relative Volatility Index (RVI)
     * Similar to RSI, but measures the direction of Standard Deviation rather than absolute price changes.
     *
     * @param array $prices Sequential close prices
     * @param int $period Lookback period (default 14)
     * @return float|null
     */
    public function calculateRVI(array $prices, int $period = 14): ?float
    {
        $count = count($prices);
        if ($count < $period + 10) return null;

        $upDevs   = [];
        $downDevs = [];

        for ($i = $count - $period; $i < $count; $i++) {
            $slice = array_slice($prices, $i - 10, 10);
            $mean  = array_sum($slice) / 10;
            $var   = 0.0;
            foreach ($slice as $p) { $var += pow($p - $mean, 2); }
            $stdDev = sqrt($var / 10);

            if ($prices[$i] > $prices[$i - 1]) {
                $upDevs[]   = $stdDev;
                $downDevs[] = 0.0;
            } else {
                $upDevs[]   = 0.0;
                $downDevs[] = $stdDev;
            }
        }

        $avgUp   = array_sum($upDevs) / $period;
        $avgDown = array_sum($downDevs) / $period;

        if ($avgDown == 0.0) return 100.0;

        return round(100.0 - (100.0 / (1.0 + ($avgUp / $avgDown))), 2);
    }

    // =========================================================================
    // PILLAR 14: SPECIALIZED OSCILLATORS & HIGH-FREQUENCY METRICS
    // =========================================================================

    /**
     * Calculates the Aroon Indicator (Aroon Up, Aroon Down, and Aroon Oscillator).
     *
     * The Aroon indicator measures the time elapsed between highest highs and lowest lows
     * over a given lookback window. It is used to detect trend inception, strong directional
     * momentum, and consolidation phases before price action moves occur.
     *
     * - Aroon Up > 70 & Aroon Down < 30: Strong Uptrend
     * - Aroon Down > 70 & Aroon Up < 30: Strong Downtrend
     * - Aroon Oscillator = Aroon Up - Aroon Down (Ranges from -100 to +100)
     *
     * @param int $period Lookback period for high/low evaluation (default: 25 bars).
     * @return array {
     *     @type float|null $up         Aroon Up percentage value (0.0 to 100.0).
     *     @type float|null $down       Aroon Down percentage value (0.0 to 100.0).
     *     @type float|null $oscillator Aroon Oscillator value (-100.0 to +100.0).
     * }
     */
    public function calculateAroon(int $period = 25): array
    {
        if ($this->dataCount < $period + 1) {
            return ['up' => null, 'down' => null, 'oscillator' => null];
        }

        // Slice the dataset for the required lookback window (+1 for current bar)
        $slice = array_slice($this->data, $this->dataCount - $period - 1, $period + 1);
        $highs = array_column($slice, 'high');
        $lows  = array_column($slice, 'low');

        // Identify the array positions of the highest high and lowest low
        $highestIndex = array_search(max($highs), $highs);
        $lowestIndex  = array_search(min($lows), $lows);

        // Calculate elapsed days/bars since the peak and trough
        $daysSinceHigh = $period - $highestIndex;
        $daysSinceLow  = $period - $lowestIndex;

        // Compute percentage components
        $aroonUp   = (($period - $daysSinceHigh) / $period) * 100.0;
        $aroonDown = (($period - $daysSinceLow) / $period) * 100.0;

        return [
            'up'         => round($aroonUp, 2),
            'down'       => round($aroonDown, 2),
            'oscillator' => round($aroonUp - $aroonDown, 2)
        ];
    }

    /**
     * Calculates the True Strength Index (TSI).
     *
     * TSI is a double-smoothed momentum oscillator that uses two Exponential Moving Averages
     * (EMAs) of price changes to eliminate noise and lag. Unlike traditional RSI, TSI measures
     * both momentum direction and trend strength relative to absolute price fluctuations.
     *
     * - TSI > 0: Bullish bias
     * - TSI < 0: Bearish bias
     * - Crossovers above +25 or below -25 indicate extreme overbought/oversold states.
     *
     * @param array $prices      Sequential close prices array (sorted chronologically).
     * @param int   $longPeriod  Primary EMA smoothing period for momentum (default: 25).
     * @param int   $shortPeriod Secondary EMA smoothing period for TSI line (default: 13).
     * @return float|null TSI value bounded between -100.0 and +100.0, or null if data is insufficient.
     */
    public function calculateTSI(array $prices, int $longPeriod = 25, int $shortPeriod = 13): ?float
    {
        $count = count($prices);
        if ($count < $longPeriod + $shortPeriod) return null;

        $momentumSeries = [];
        $absMomentumSeries = [];

        // Step 1: Calculate raw 1-bar price changes and absolute changes
        for ($i = 1; $i < $count; $i++) {
            $change = $prices[$i] - $prices[$i - 1];
            $momentumSeries[]    = $change;
            $absMomentumSeries[] = abs($change);
        }

        // Step 2: Double smooth the directional price momentum
        $firstEmaP  = $this->calculateEMA($momentumSeries, $longPeriod);
        $doubleEmaP = $this->calculateEMA([$firstEmaP], $shortPeriod);

        // Step 3: Double smooth the absolute price momentum
        $firstEmaAbs  = $this->calculateEMA($absMomentumSeries, $longPeriod);
        $doubleEmaAbs = $this->calculateEMA([$firstEmaAbs], $shortPeriod);

        if ($doubleEmaAbs == 0.0 || $doubleEmaAbs === null || $doubleEmaP === null) return 0.0;

        // Step 4: Compute TSI ratio
        return round(100.0 * ($doubleEmaP / $doubleEmaAbs), 2);
    }

    /**
     * Calculates the Vortex Indicator (VI+ and VI-).
     *
     * The Vortex Indicator consists of two lines measuring positive (VI+) and negative (VI-)
     * directional movement based on the distance between current bar extremes and previous bar extremes.
     *
     * - VI+ crossing above VI-: Valid Bullish Buy signal
     * - VI- crossing above VI+: Valid Bearish Sell signal
     *
     * @param int $period Lookback calculation window (default: 14 bars).
     * @return array {
     *     @type float|null $vi_plus  Positive directional vortex line.
     *     @type float|null $vi_minus Negative directional vortex line.
     * }
     */
    public function calculateVortex(int $period = 14): array
    {
        if ($this->dataCount < $period + 1) {
            return ['vi_plus' => null, 'vi_minus' => null];
        }

        $sumVmPlus  = 0.0;
        $sumVmMinus = 0.0;
        $sumTR      = 0.0;

        // Sum vortex movements and True Range over the lookback window
        for ($i = $this->dataCount - $period; $i < $this->dataCount; $i++) {
            $currHigh  = (float)$this->data[$i]['high'];
            $currLow   = (float)$this->data[$i]['low'];
            $prevHigh  = (float)$this->data[$i - 1]['high'];
            $prevLow   = (float)$this->data[$i - 1]['low'];
            $prevClose = (float)$this->data[$i - 1]['close'];

            // Directional movement distance vectors
            $sumVmPlus  += abs($currHigh - $prevLow);
            $sumVmMinus += abs($currLow - $prevHigh);

            // True Range calculation for scaling
            $tr = max($currHigh - $currLow, abs($currHigh - $prevClose), abs($currLow - $prevClose));
            $sumTR += $tr;
        }

        if ($sumTR == 0.0) return ['vi_plus' => 1.0, 'vi_minus' => 1.0];

        return [
            'vi_plus'  => round($sumVmPlus / $sumTR, 4),
            'vi_minus' => round($sumVmMinus / $sumTR, 4)
        ];
    }

    /**
     * Calculates the Chande Momentum Oscillator (CMO).
     *
     * CMO calculates momentum on both up-days and down-days directly without smoothing
     * the results through exponential moving averages. This provides a raw, fast-reacting
     * measure of price velocity.
     *
     * - CMO > +50: Overbought / Strong Bullish Expansion
     * - CMO < -50: Oversold / Strong Bearish Expansion
     *
     * @param array $prices Sequential close prices array.
     * @param int   $period Lookback period for momentum (default: 14).
     * @return float|null Bounded momentum value (-100.0 to +100.0), or null if dataset is short.
     */
    public function calculateCMO(array $prices, int $period = 14): ?float
    {
        $count = count($prices);
        if ($count <= $period) return null;

        $sumGains  = 0.0;
        $sumLosses = 0.0;

        // Sum net price changes over N periods
        for ($i = $count - $period; $i < $count; $i++) {
            $diff = $prices[$i] - $prices[$i - 1];
            if ($diff > 0) {
                $sumGains += $diff;
            } else {
                $sumLosses += abs($diff);
            }
        }

        $totalMove = $sumGains + $sumLosses;
        if ($totalMove == 0.0) return 0.0;

        // Unsmoothed momentum formula
        return round((($sumGains - $sumLosses) / $totalMove) * 100.0, 2);
    }

    /**
     * Calculates the Mass Index.
     *
     * The Mass Index examines high-low price range expansion and contraction to identify
     * potential structural trend reversals based on volatility bulges.
     *
     * - Reversal Spike: When Mass Index rises above 27.0 and subsequently drops below 26.5,
     *   a major trend reversal is imminent regardless of the prior trend direction.
     *
     * @param int $emaPeriod Lookback period for single and double EMA smoothing (default: 9).
     * @param int $sumPeriod Summation window for mass ratios (default: 25).
     * @return float|null Mass Index value, or null if insufficient history is supplied.
     */
    public function calculateMassIndex(int $emaPeriod = 9, int $sumPeriod = 25): ?float
    {
        if ($this->dataCount < $sumPeriod + ($emaPeriod * 2)) return null;

        // Extract raw bar high-low ranges
        $ranges = [];
        for ($i = 0; $i < $this->dataCount; $i++) {
            $ranges[] = (float)$this->data[$i]['high'] - (float)$this->data[$i]['low'];
        }

        // Calculate EMA ratio series: Single EMA(Range) / Double EMA(Range)
        $massRatioSeries = [];
        for ($i = $emaPeriod * 2; $i < $this->dataCount; $i++) {
            $subRanges = array_slice($ranges, 0, $i + 1);
            $singleEma = $this->calculateEMA($subRanges, $emaPeriod);
            $doubleEma = $this->calculateEMA([$singleEma], $emaPeriod);

            if ($doubleEma > 0.0 && $singleEma !== null) {
                $massRatioSeries[] = $singleEma / $doubleEma;
            }
        }

        if (count($massRatioSeries) < $sumPeriod) return null;

        // Sum the ratio series over the summation period
        $slice = array_slice($massRatioSeries, count($massRatioSeries) - $sumPeriod, $sumPeriod);
        return round(array_sum($slice), 2);
    }

    /**
     * Calculates Connors RSI (Composite High-Frequency RSI).
     *
     * Connors RSI is an institutional composite momentum indicator that blends three components:
     * 1. Short-term RSI (momentum speed)
     * 2. Streak Duration RSI (consecutive win/loss streak length)
     * 3. Percentile Rank (relative magnitude of price change vs. history)
     *
     * Excellent for short-term mean-reversion trading systems and high-frequency algorithms.
     *
     * @param array $prices       Sequential close prices array.
     * @param int   $rsiPeriod    Fast RSI lookback window (default: 3).
     * @param int   $streakPeriod Streak RSI lookback window (default: 2).
     * @param int   $rankPeriod   Percentile Rank lookback window (default: 100).
     * @return float|null Composite RSI score between 0.0 and 100.0, or null if dataset length < rankPeriod.
     */
    public function calculateConnorsRSI(array $prices, int $rsiPeriod = 3, int $streakPeriod = 2, int $rankPeriod = 100): ?float
    {
        $count = count($prices);
        if ($count < $rankPeriod) return null;

        // Component 1: Standard Fast Price RSI
        $rsi1 = $this->calculateRSI($prices, $rsiPeriod);
        if ($rsi1 === null) return null;

        // Component 2: Streak RSI (tracks count of consecutive up/down closing bars)
        $streaks = [0];
        for ($i = 1; $i < $count; $i++) {
            $prevStreak = end($streaks);
            if ($prices[$i] > $prices[$i - 1]) {
                $streaks[] = $prevStreak > 0 ? $prevStreak + 1 : 1;
            } elseif ($prices[$i] < $prices[$i - 1]) {
                $streaks[] = $prevStreak < 0 ? $prevStreak - 1 : -1;
            } else {
                $streaks[] = 0;
            }
        }
        $streakRsi = $this->calculateRSI($streaks, $streakPeriod) ?? 50.0;

        // Component 3: Percentile Rank of current 1-bar percentage return
        $currentReturn = ($prices[$count - 1] - $prices[$count - 2]) / $prices[$count - 2];
        $slice = array_slice($prices, $count - $rankPeriod, $rankPeriod);
        
        $countBelow = 0;
        for ($k = 1; $k < count($slice); $k++) {
            $ret = ($slice[$k] - $slice[$k - 1]) / $slice[$k - 1];
            if ($ret < $currentReturn) {
                $countBelow++;
            }
        }
        $percentRank = ($countBelow / ($rankPeriod - 1)) * 100.0;

        // Blend all 3 equal-weighted elements into the final score
        return round(($rsi1 + $streakRsi + $percentRank) / 3.0, 2);
    }

    // =========================================================================
    // PILLAR 15: SPECIALIZED VOLATILITY SCALING & CROSS-ASSET COMPARISONS
    // =========================================================================

    /**
     * Calculates Normalized Average True Range (NATR).
     *
     * Expresses ATR as a percentage of the closing price. This allows direct
     * volatility comparisons across different assets or stocks regardless of price magnitude
     * (e.g., comparing a ₹3,000 stock to a ₹100 stock).
     *
     * @param int $period ATR lookback window (default: 14).
     * @return float|null NATR percentage value (e.g., 2.5 means daily volatility is 2.5% of price).
     */
    public function calculateNATR(int $period = 14): ?float
    {
        $atr = $this->calculateATR($period);
        if ($atr === null || $this->dataCount === 0) return null;

        $latestClose = (float) end($this->data)['close'];
        if ($latestClose <= 0.0) return null;

        return round(($atr / $latestClose) * 100.0, 2);
    }

    /**
     * Calculates the Relative Momentum Index (RMI).
     *
     * RMI is a variation of RSI developed by Roger Altman. Instead of measuring
     * day-to-day close differences (1-bar momentum), it measures price change relative
     * to a custom lookback count (e.g., 3-day momentum over a 14-period window).
     *
     * - Reduces whipsaws compared to standard RSI.
     * - Better suited for persistent trend-following systems.
     *
     * @param array $prices Sequential close prices array.
     * @param int   $period Smoothing window (default: 14).
     * @param int   $momentumLookback Momentum gap in bars (default: 3).
     * @return float|null RMI value bounded between 0.0 and 100.0.
     */
    public function calculateRMI(array $prices, int $period = 14, int $momentumLookback = 3): ?float
    {
        $count = count($prices);
        if ($count <= $period + $momentumLookback) return null;

        $gains = 0.0;
        $losses = 0.0;

        for ($i = $count - $period; $i < $count; $i++) {
            $change = $prices[$i] - $prices[$i - $momentumLookback];
            if ($change >= 0) {
                $gains += $change;
            } else {
                $losses += abs($change);
            }
        }

        $avgGain = $gains / $period;
        $avgLoss = $losses / $period;

        if ($avgLoss == 0.0) return 100.0;

        $rm = $avgGain / $avgLoss;
        return round(100.0 - (100.0 / (1.0 + $rm)), 2);
    }

    /**
     * Calculates Klinger Volume Oscillator (KVO).
     *
     * KVO evaluates long-term money flow trends while detecting subtle, short-term volume surges.
     * It compares high-low ranges weighted by volume and directional force.
     *
     * - KVO > Signal Line: Bullish accumulation
     * - KVO < Signal Line: Bearish distribution
     *
     * @param int $fastPeriod Short EMA lookback (default: 34).
     * @param int $slowPeriod Long EMA lookback (default: 55).
     * @return float|null Raw Klinger Oscillator value.
     */
    public function calculateKlingerOscillator(int $fastPeriod = 34, int $slowPeriod = 55): ?float
    {
        if ($this->dataCount < $slowPeriod + 1) return null;

        $volumeForce = [];
        for ($i = 1; $i < $this->dataCount; $i++) {
            $high  = (float)$this->data[$i]['high'];
            $low   = (float)$this->data[$i]['low'];
            $close = (float)$this->data[$i]['close'];
            $vol   = (float)$this->data[$i]['volume'];

            $prevHigh  = (float)$this->data[$i - 1]['high'];
            $prevLow   = (float)$this->data[$i - 1]['low'];
            $prevClose = (float)$this->data[$i - 1]['close'];

            $trend = (($high + $low + $close) > ($prevHigh + $prevLow + $prevClose)) ? 1 : -1;
            $range = $high - $low;
            $dm = $high - $low;

            $vf = ($range > 0) ? $vol * abs(2 * (($close - $low) - ($high - $close)) / $range) * $trend * 100 : 0;
            $volumeForce[] = $vf;
        }

        $fastEma = $this->calculateEMA($volumeForce, $fastPeriod);
        $slowEma = $this->calculateEMA($volumeForce, $slowPeriod);

        if ($fastEma === null || $slowEma === null) return null;

        return round($fastEma - $slowEma, 2);
    }

    /**
     * Calculates Rainbow Moving Averages (Cascade Array).
     *
     * Computes a recursive chain of SMAs where each subsequent level smoothes the
     * previous moving average level. Used to visualize trend strength, expansion, and contraction.
     *
     * @param array $prices Sequential close prices array.
     * @param int   $basePeriod Base SMA period (default: 2).
     * @param int   $depth Number of cascade levels (default: 6).
     * @return array Map of levels (sma1, sma2, ..., smaN).
     */
    public function calculateRainbowMA(array $prices, int $basePeriod = 2, int $depth = 6): array
    {
        $levels = [];
        $currentPrices = $prices;

        for ($d = 1; $d <= $depth; $d++) {
            $sma = $this->calculateSMA($currentPrices, $basePeriod);
            if ($sma === null) {
                $levels["sma{$d}"] = null;
                continue;
            }

            $levels["sma{$d}"] = round($sma, 2);
            
            // Append current level output for recursive cascade calculation
            $currentPrices[] = $sma;
        }

        return $levels;
    }

    // =========================================================================
    // PILLAR 16: MACHINE LEARNING FEATURE ENGINEERING & STATIONARITY
    // =========================================================================

    /**
     * Calculates Logarithmic Returns (Stationary Price Transformations).
     *
     * Converts raw non-stationary close prices into time-additive, stationary log returns.
     * Log returns are standard input features for regression and neural network predictors.
     *
     * @param array $prices Sequential close prices array.
     * @return array Array of stationary log returns: ln(Price_t / Price_{t-1}).
     */
    public function calculateLogReturns(array $prices): array
    {
        $count = count($prices);
        if ($count < 2) return [];

        $logReturns = [];
        for ($i = 1; $i < $count; $i++) {
            if ($prices[$i - 1] <= 0 || $prices[$i] <= 0) {
                $logReturns[] = 0.0;
                continue;
            }
            $logReturns[] = round(log($prices[$i] / $prices[$i - 1]), 6);
        }

        return $logReturns;
    }

    /**
     * Calculates Fractional Differentiation (Preserves Memory while Enforcing Stationarity).
     *
     * Standard price differencing (d=1) removes all long-term memory/trend context.
     * Fractional differentiation (e.g., d=0.4 or d=0.5) achieves stationarity for ML
     * models while preserving historical price memory (Marcos López de Prado method).
     *
     * @param array $prices Sequential close prices array.
     * @param float $d      Fractional differentiation degree (typically between 0.3 and 0.7).
     * @param int   $window Lookback memory window for expansion weights (default: 10).
     * @return array Fractionally differentiated price series.
     */
    public function calculateFractionalDifferentiation(array $prices, float $d = 0.5, int $window = 10): array
    {
        $count = count($prices);
        if ($count < $window) return [];

        // Generate binomial expansion weights for degree d
        $weights = [1.0];
        for ($k = 1; $k < $window; $k++) {
            $weights[] = -$weights[$k - 1] * ($d - $k + 1) / $k;
        }

        $diffSeries = [];
        for ($i = $window - 1; $i < $count; $i++) {
            $val = 0.0;
            for ($k = 0; $k < $window; $k++) {
                $val += $weights[$k] * $prices[$i - $k];
            }
            $diffSeries[] = round($val, 4);
        }

        return $diffSeries;
    }

    /**
     * Min-Max Feature Scaler (Normalizes features to a bounded range [0, 1] or [-1, 1]).
     *
     * Standardizes technical indicator values prior to passing them into ML models 
     * (e.g., Rubix/ML Ridge, K-Means, or Neural Networks) to prevent large values from dominating.
     *
     * @param array $featureArray Array of feature values.
     * @param float $minTarget   Desired minimum scale bound (default: 0.0).
     * @param float $maxTarget   Desired maximum scale bound (default: 1.0).
     * @return array Min-Max scaled feature vector.
     */
    public function minMaxScale(array $featureArray, float $minTarget = 0.0, float $maxTarget = 1.0): array
    {
        if (empty($featureArray)) return [];

        $minVal = min($featureArray);
        $maxVal = max($featureArray);
        $range  = $maxVal - $minVal;

        if ($range == 0.0) {
            return array_fill(0, count($featureArray), $minTarget);
        }

        $scaled = [];
        foreach ($featureArray as $val) {
            $normalized = ($val - $minVal) / $range;
            $scaled[]   = round($minTarget + ($normalized * ($maxTarget - $minTarget)), 6);
        }

        return $scaled;
    }

    /**
     * Estimates Value-Area High (VAH), Value-Area Low (VAL), and Point of Control (POC).
     *
     * Approximates Market Profile / Volume Profile key institutional liquidity zones
     * over a given lookback window (70% Value Area threshold).
     *
     * @param int $period Lookback bar count (default: 30).
     * @param int $bins   Price distribution bin count (default: 10).
     * @return array {
     *     @type float|null $poc Point of Control (highest volume price level).
     *     @type float|null $vah Value Area High (top of 70% volume distribution).
     *     @type float|null $val Value Area Low (bottom of 70% volume distribution).
     * }
     */
    public function calculateVolumeProfileLevels(int $period = 30, int $bins = 10): array
    {
        if ($this->dataCount < $period) {
            return ['poc' => null, 'vah' => null, 'val' => null];
        }

        $slice = array_slice($this->data, $this->dataCount - $period, $period);

        // Extract highs and lows safely
        $highs = [];
        $lows  = [];

        foreach ($slice as $bar) {
            $barArray = (array) $bar;
            $highs[]  = (float) ($barArray['high'] ?? $barArray['High'] ?? 0);
            $lows[]   = (float) ($barArray['low']  ?? $barArray['Low']  ?? 0);
        }

        if (empty($highs) || empty($lows)) {
            return ['poc' => null, 'vah' => null, 'val' => null];
        }

        $minPrice = min($lows);
        $maxPrice = max($highs);
        $binSize  = ($maxPrice - $minPrice) / max(1, $bins);

        if ($binSize == 0.0) {
            return ['poc' => round($minPrice, 2), 'vah' => round($minPrice, 2), 'val' => round($minPrice, 2)];
        }

        // Aggregate volume into price bins
        $volumeBins = array_fill(0, $bins, 0.0);
        foreach ($slice as $bar) {
            $barArray = (array) $bar;

            $high   = (float) ($barArray['high']   ?? $barArray['High']   ?? 0);
            $low    = (float) ($barArray['low']    ?? $barArray['Low']    ?? 0);
            $close  = (float) ($barArray['close']  ?? $barArray['Close']  ?? 0);
            $volume = (float) ($barArray['volume'] ?? $barArray['Volume'] ?? $barArray['vol'] ?? 0);

            $typicalPrice = ($high + $low + $close) / 3.0;
            $binIndex     = min($bins - 1, (int) floor(($typicalPrice - $minPrice) / $binSize));
            $binIndex     = max(0, $binIndex); // Safety bound

            $volumeBins[$binIndex] += $volume;
        }

        // Identify POC (Bin with highest volume)
        $pocBin   = array_search(max($volumeBins), $volumeBins);
        $pocPrice = $minPrice + ($pocBin * $binSize) + ($binSize / 2.0);

        // Calculate 70% Value Area bounds
        $totalVolume  = array_sum($volumeBins);
        $targetVolume = $totalVolume * 0.70;

        $accumulatedVolume = $volumeBins[$pocBin];
        $leftIndex  = $pocBin;
        $rightIndex = $pocBin;

        while ($accumulatedVolume < $targetVolume && ($leftIndex > 0 || $rightIndex < $bins - 1)) {
            $nextLeftVol  = ($leftIndex > 0) ? $volumeBins[$leftIndex - 1] : -1;
            $nextRightVol = ($rightIndex < $bins - 1) ? $volumeBins[$rightIndex + 1] : -1;

            if ($nextLeftVol >= $nextRightVol && $leftIndex > 0) {
                $leftIndex--;
                $accumulatedVolume += $volumeBins[$leftIndex];
            } elseif ($rightIndex < $bins - 1) {
                $rightIndex++;
                $accumulatedVolume += $volumeBins[$rightIndex];
            }
        }

        $valPrice = $minPrice + ($leftIndex * $binSize);
        $vahPrice = $minPrice + (($rightIndex + 1) * $binSize);

        return [
            'poc' => round($pocPrice, 2),
            'vah' => round($vahPrice, 2),
            'val' => round($valPrice, 2)
        ];
    }
}