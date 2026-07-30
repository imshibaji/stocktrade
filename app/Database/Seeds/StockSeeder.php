<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class StockSeeder extends Seeder
{
    public function run()
    {
        $stocks = [
            ['symbol' => 'RELIANCE', 'name' => 'Reliance Industries Ltd', 'sector' => 'Oil & Gas', 'current_price' => '2950.50', 'previous_close' => '2930.00', 'market_cap' => 19900000000000, 'avg_volume' => 8500000, 'pe_ratio' => '27.50', 'week_52_high' => '3100.00', 'week_52_low' => '2220.00', 'dividend_yield' => '0.35', 'beta' => '1.10'],
            ['symbol' => 'TCS', 'name' => 'Tata Consultancy Services', 'sector' => 'IT', 'current_price' => '4250.75', 'previous_close' => '4180.00', 'market_cap' => 15500000000000, 'avg_volume' => 3200000, 'pe_ratio' => '32.10', 'week_52_high' => '4500.00', 'week_52_low' => '3150.00', 'dividend_yield' => '1.15', 'beta' => '0.75'],
            ['symbol' => 'HDFCBANK', 'name' => 'HDFC Bank Ltd', 'sector' => 'Banking', 'current_price' => '1680.20', 'previous_close' => '1665.00', 'market_cap' => 12800000000000, 'avg_volume' => 10500000, 'pe_ratio' => '21.80', 'week_52_high' => '1800.00', 'week_52_low' => '1360.00', 'dividend_yield' => '0.85', 'beta' => '0.90'],
            ['symbol' => 'INFY', 'name' => 'Infosys Ltd', 'sector' => 'IT', 'current_price' => '1850.30', 'previous_close' => '1830.00', 'market_cap' => 7700000000000, 'avg_volume' => 6200000, 'pe_ratio' => '25.40', 'week_52_high' => '1950.00', 'week_52_low' => '1350.00', 'dividend_yield' => '1.45', 'beta' => '0.80'],
            ['symbol' => 'ICICIBANK', 'name' => 'ICICI Bank Ltd', 'sector' => 'Banking', 'current_price' => '1250.60', 'previous_close' => '1235.00', 'market_cap' => 8700000000000, 'avg_volume' => 14000000, 'pe_ratio' => '19.50', 'week_52_high' => '1300.00', 'week_52_low' => '880.00', 'dividend_yield' => '0.65', 'beta' => '1.05'],
            ['symbol' => 'WIPRO', 'name' => 'Wipro Ltd', 'sector' => 'IT', 'current_price' => '520.40', 'previous_close' => '515.00', 'market_cap' => 2850000000000, 'avg_volume' => 9800000, 'pe_ratio' => '22.30', 'week_52_high' => '560.00', 'week_52_low' => '375.00', 'dividend_yield' => '0.20', 'beta' => '0.85'],
            ['symbol' => 'TATAMOTORS', 'name' => 'Tata Motors Ltd', 'sector' => 'Automobile', 'current_price' => '980.15', 'previous_close' => '970.00', 'market_cap' => 3700000000000, 'avg_volume' => 22000000, 'pe_ratio' => '35.60', 'week_52_high' => '1065.00', 'week_52_low' => '400.00', 'dividend_yield' => '0.25', 'beta' => '1.35'],
            ['symbol' => 'BHARTIARTL', 'name' => 'Bharti Airtel Ltd', 'sector' => 'Telecom', 'current_price' => '1420.80', 'previous_close' => '1405.00', 'market_cap' => 8500000000000, 'avg_volume' => 5500000, 'pe_ratio' => '55.20', 'week_52_high' => '1500.00', 'week_52_low' => '750.00', 'dividend_yield' => '0.30', 'beta' => '0.70'],
            ['symbol' => 'SBIN', 'name' => 'State Bank of India', 'sector' => 'Banking', 'current_price' => '780.50', 'previous_close' => '775.00', 'market_cap' => 6950000000000, 'avg_volume' => 18500000, 'pe_ratio' => '12.40', 'week_52_high' => '820.00', 'week_52_low' => '520.00', 'dividend_yield' => '1.25', 'beta' => '1.15'],
            ['symbol' => 'LT', 'name' => 'Larsen & Toubro Ltd', 'sector' => 'Infrastructure', 'current_price' => '3540.90', 'previous_close' => '3510.00', 'market_cap' => 4980000000000, 'avg_volume' => 2800000, 'pe_ratio' => '38.50', 'week_52_high' => '3750.00', 'week_52_low' => '2150.00', 'dividend_yield' => '0.75', 'beta' => '1.20'],
            ['symbol' => 'HCLTECH', 'name' => 'HCL Technologies Ltd', 'sector' => 'IT', 'current_price' => '1580.25', 'previous_close' => '1565.00', 'market_cap' => 4280000000000, 'avg_volume' => 4100000, 'pe_ratio' => '26.80', 'week_52_high' => '1700.00', 'week_52_low' => '1050.00', 'dividend_yield' => '1.10', 'beta' => '0.78'],
            ['symbol' => 'SUNPHARMA', 'name' => 'Sun Pharmaceutical', 'sector' => 'Pharma', 'current_price' => '1520.30', 'previous_close' => '1510.00', 'market_cap' => 3650000000000, 'avg_volume' => 3800000, 'pe_ratio' => '30.20', 'week_52_high' => '1600.00', 'week_52_low' => '950.00', 'dividend_yield' => '0.65', 'beta' => '0.60'],
            ['symbol' => 'MARUTI', 'name' => 'Maruti Suzuki India', 'sector' => 'Automobile', 'current_price' => '12850.00', 'previous_close' => '12750.00', 'market_cap' => 4040000000000, 'avg_volume' => 850000, 'pe_ratio' => '28.90', 'week_52_high' => '13500.00', 'week_52_low' => '8500.00', 'dividend_yield' => '0.55', 'beta' => '0.95'],
            ['symbol' => 'TITAN', 'name' => 'Titan Company Ltd', 'sector' => 'Consumer', 'current_price' => '3450.50', 'previous_close' => '3420.00', 'market_cap' => 3060000000000, 'avg_volume' => 2100000, 'pe_ratio' => '82.50', 'week_52_high' => '3800.00', 'week_52_low' => '2500.00', 'dividend_yield' => '0.28', 'beta' => '0.65'],
            ['symbol' => 'ASIANPAINT', 'name' => 'Asian Paints Ltd', 'sector' => 'Consumer', 'current_price' => '2890.75', 'previous_close' => '2875.00', 'market_cap' => 2770000000000, 'avg_volume' => 1500000, 'pe_ratio' => '52.40', 'week_52_high' => '3200.00', 'week_52_low' => '2750.00', 'dividend_yield' => '0.95', 'beta' => '0.55'],
        ];

        foreach ($stocks as $data) {
            $data['created_at'] = date('Y-m-d H:i:s');
            $data['updated_at'] = date('Y-m-d H:i:s');
            $this->db->table('stocks')->insert($data);

            $stockId = $this->db->insertID();
            $basePrice = (float) $data['current_price'];
            generate_price_history($stockId, $basePrice);
            generate_predictions($stockId, $basePrice);
        }
    }

    private function generatePriceHistory(int $stockId, float $basePrice): void
    {
        $prices = [];
        for ($i = 90; $i >= 0; $i--) {
            $date = date('Y-m-d', strtotime("-{$i} days"));
            $volatility = $basePrice * 0.03;
            $change = (mt_rand(-1000, 1000) / 1000) * $volatility;
            $close = round($basePrice + $change, 2);
            $open = round($close - (mt_rand(-500, 500) / 1000) * $volatility, 2);
            $high = round(max($open, $close) + abs(mt_rand(0, 500) / 1000) * $volatility, 2);
            $low = round(min($open, $close) - abs(mt_rand(0, 500) / 1000) * $volatility, 2);
            $volume = mt_rand(100000, 50000000);

            $prices[] = [
                'stock_id'   => $stockId,
                'price_date' => $date,
                'open'       => $open,
                'high'       => $high,
                'low'        => $low,
                'close'      => $close,
                'volume'     => $volume,
            ];
            $basePrice = $close;
        }

        $this->db->table('stock_prices')->insertBatch($prices);
    }

    private function generatePredictions(int $stockId, float $basePrice): void
    {
        $predictions = [];
        for ($i = 1; $i <= 30; $i++) {
            $date = date('Y-m-d', strtotime("+{$i} days"));
            $trend = (mt_rand(-100, 100) / 10000) * $basePrice;
            $predictedPrice = round($basePrice + ($trend * $i), 2);
            $confidence = round(max(60, min(95, 95 - ($i * 0.5))), 2);

            $predictions[] = [
                'stock_id'        => $stockId,
                'predicted_date'  => $date,
                'predicted_price' => $predictedPrice,
                'confidence_score'=> $confidence,
                'method'          => 'Monte Carlo + EMA',
                'created_at'      => date('Y-m-d H:i:s'),
            ];
        }
        $this->db->table('predictions')->insertBatch($predictions);
    }
}
