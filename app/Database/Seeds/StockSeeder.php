<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class StockSeeder extends Seeder
{
    public function run()
    {
        $stocks = [
            ['symbol' => 'RELIANCE', 'name' => 'Reliance Industries Ltd', 'sector' => 'Oil & Gas', 'exchange' => 'NSE', 'current_price' => '2950.50', 'previous_close' => '2930.00', 'market_cap' => 19900000000000, 'avg_volume' => 8500000, 'pe_ratio' => '27.50', 'week_52_high' => '3100.00', 'week_52_low' => '2220.00', 'dividend_yield' => '0.35', 'beta' => '1.10'],
            ['symbol' => 'TCS', 'name' => 'Tata Consultancy Services', 'sector' => 'IT', 'exchange' => 'NSE', 'current_price' => '4250.75', 'previous_close' => '4180.00', 'market_cap' => 15500000000000, 'avg_volume' => 3200000, 'pe_ratio' => '32.10', 'week_52_high' => '4500.00', 'week_52_low' => '3150.00', 'dividend_yield' => '1.15', 'beta' => '0.75'],
            ['symbol' => 'HDFCBANK', 'name' => 'HDFC Bank Ltd', 'sector' => 'Banking', 'exchange' => 'NSE', 'current_price' => '1680.20', 'previous_close' => '1665.00', 'market_cap' => 12800000000000, 'avg_volume' => 10500000, 'pe_ratio' => '21.80', 'week_52_high' => '1800.00', 'week_52_low' => '1360.00', 'dividend_yield' => '0.85', 'beta' => '0.90'],
            ['symbol' => 'INFY', 'name' => 'Infosys Ltd', 'sector' => 'IT', 'exchange' => 'NSE', 'current_price' => '1850.30', 'previous_close' => '1830.00', 'market_cap' => 7700000000000, 'avg_volume' => 6200000, 'pe_ratio' => '25.40', 'week_52_high' => '1950.00', 'week_52_low' => '1350.00', 'dividend_yield' => '1.45', 'beta' => '0.80'],
            ['symbol' => 'ICICIBANK', 'name' => 'ICICI Bank Ltd', 'sector' => 'Banking', 'exchange' => 'NSE', 'current_price' => '1250.60', 'previous_close' => '1235.00', 'market_cap' => 8700000000000, 'avg_volume' => 14000000, 'pe_ratio' => '19.50', 'week_52_high' => '1300.00', 'week_52_low' => '880.00', 'dividend_yield' => '0.65', 'beta' => '1.05'],
            ['symbol' => 'WIPRO', 'name' => 'Wipro Ltd', 'sector' => 'IT', 'exchange' => 'NSE', 'current_price' => '520.40', 'previous_close' => '515.00', 'market_cap' => 2850000000000, 'avg_volume' => 9800000, 'pe_ratio' => '22.30', 'week_52_high' => '560.00', 'week_52_low' => '375.00', 'dividend_yield' => '0.20', 'beta' => '0.85'],
            ['symbol' => 'TATAMOTORS', 'name' => 'Tata Motors Ltd', 'sector' => 'Automobile', 'exchange' => 'NSE', 'current_price' => '980.15', 'previous_close' => '970.00', 'market_cap' => 3700000000000, 'avg_volume' => 22000000, 'pe_ratio' => '35.60', 'week_52_high' => '1065.00', 'week_52_low' => '400.00', 'dividend_yield' => '0.25', 'beta' => '1.35'],
            ['symbol' => 'BHARTIARTL', 'name' => 'Bharti Airtel Ltd', 'sector' => 'Telecom', 'exchange' => 'NSE', 'current_price' => '1420.80', 'previous_close' => '1405.00', 'market_cap' => 8500000000000, 'avg_volume' => 5500000, 'pe_ratio' => '55.20', 'week_52_high' => '1500.00', 'week_52_low' => '750.00', 'dividend_yield' => '0.30', 'beta' => '0.70'],
            ['symbol' => 'SBIN', 'name' => 'State Bank of India', 'sector' => 'Banking', 'exchange' => 'NSE', 'current_price' => '780.50', 'previous_close' => '775.00', 'market_cap' => 6950000000000, 'avg_volume' => 18500000, 'pe_ratio' => '12.40', 'week_52_high' => '820.00', 'week_52_low' => '520.00', 'dividend_yield' => '1.25', 'beta' => '1.15'],
            ['symbol' => 'LT', 'name' => 'Larsen & Toubro Ltd', 'sector' => 'Infrastructure', 'exchange' => 'NSE', 'current_price' => '3540.90', 'previous_close' => '3510.00', 'market_cap' => 4980000000000, 'avg_volume' => 2800000, 'pe_ratio' => '38.50', 'week_52_high' => '3750.00', 'week_52_low' => '2150.00', 'dividend_yield' => '0.75', 'beta' => '1.20'],
            ['symbol' => 'HCLTECH', 'name' => 'HCL Technologies Ltd', 'sector' => 'IT', 'exchange' => 'NSE', 'current_price' => '1580.25', 'previous_close' => '1565.00', 'market_cap' => 4280000000000, 'avg_volume' => 4100000, 'pe_ratio' => '26.80', 'week_52_high' => '1700.00', 'week_52_low' => '1050.00', 'dividend_yield' => '1.10', 'beta' => '0.78'],
            ['symbol' => 'SUNPHARMA', 'name' => 'Sun Pharmaceutical', 'sector' => 'Pharma', 'exchange' => 'NSE', 'current_price' => '1520.30', 'previous_close' => '1510.00', 'market_cap' => 3650000000000, 'avg_volume' => 3800000, 'pe_ratio' => '30.20', 'week_52_high' => '1600.00', 'week_52_low' => '950.00', 'dividend_yield' => '0.65', 'beta' => '0.60'],
            ['symbol' => 'MARUTI', 'name' => 'Maruti Suzuki India', 'sector' => 'Automobile', 'exchange' => 'NSE', 'current_price' => '12850.00', 'previous_close' => '12750.00', 'market_cap' => 4040000000000, 'avg_volume' => 850000, 'pe_ratio' => '28.90', 'week_52_high' => '13500.00', 'week_52_low' => '8500.00', 'dividend_yield' => '0.55', 'beta' => '0.95'],
            ['symbol' => 'TITAN', 'name' => 'Titan Company Ltd', 'sector' => 'Consumer', 'exchange' => 'NSE', 'current_price' => '3450.50', 'previous_close' => '3420.00', 'market_cap' => 3060000000000, 'avg_volume' => 2100000, 'pe_ratio' => '82.50', 'week_52_high' => '3800.00', 'week_52_low' => '2500.00', 'dividend_yield' => '0.28', 'beta' => '0.65'],
            ['symbol' => 'ASIANPAINT', 'name' => 'Asian Paints Ltd', 'sector' => 'Consumer', 'exchange' => 'NSE', 'current_price' => '2890.75', 'previous_close' => '2875.00', 'market_cap' => 2770000000000, 'avg_volume' => 1500000, 'pe_ratio' => '52.40', 'week_52_high' => '3200.00', 'week_52_low' => '2750.00', 'dividend_yield' => '0.95', 'beta' => '0.55'],
            ['symbol' => 'BAJFINANCE', 'name' => 'Bajaj Finance Ltd', 'sector' => 'Financial', 'exchange' => 'NSE', 'current_price' => '7250.00', 'previous_close' => '7180.00', 'market_cap' => 3650000000000, 'avg_volume' => 3200000, 'pe_ratio' => '42.30', 'week_52_high' => '7800.00', 'week_52_low' => '5200.00', 'dividend_yield' => '0.15', 'beta' => '1.45'],
            ['symbol' => 'BAJAJFINSV', 'name' => 'Bajaj Finserv Ltd', 'sector' => 'Financial', 'exchange' => 'NSE', 'current_price' => '1780.90', 'previous_close' => '1755.00', 'market_cap' => 2850000000000, 'avg_volume' => 2800000, 'pe_ratio' => '38.50', 'week_52_high' => '1950.00', 'week_52_low' => '1200.00', 'dividend_yield' => '0.22', 'beta' => '1.30'],
            ['symbol' => 'AXISBANK', 'name' => 'Axis Bank Ltd', 'sector' => 'Banking', 'exchange' => 'NSE', 'current_price' => '1050.30', 'previous_close' => '1040.00', 'market_cap' => 5200000000000, 'avg_volume' => 12000000, 'pe_ratio' => '18.20', 'week_52_high' => '1150.00', 'week_52_low' => '780.00', 'dividend_yield' => '0.55', 'beta' => '1.10'],
            ['symbol' => 'KOTAKBANK', 'name' => 'Kotak Mahindra Bank', 'sector' => 'Banking', 'exchange' => 'NSE', 'current_price' => '1950.60', 'previous_close' => '1925.00', 'market_cap' => 4650000000000, 'avg_volume' => 3500000, 'pe_ratio' => '22.80', 'week_52_high' => '2100.00', 'week_52_low' => '1450.00', 'dividend_yield' => '0.40', 'beta' => '0.95'],
            ['symbol' => 'ITC', 'name' => 'ITC Ltd', 'sector' => 'Consumer', 'exchange' => 'NSE', 'current_price' => '485.20', 'previous_close' => '480.00', 'market_cap' => 5950000000000, 'avg_volume' => 15000000, 'pe_ratio' => '19.80', 'week_52_high' => '520.00', 'week_52_low' => '380.00', 'dividend_yield' => '3.20', 'beta' => '0.75'],
            ['symbol' => 'HINDUNILVR', 'name' => 'Hindustan Unilever Ltd', 'sector' => 'Consumer', 'exchange' => 'NSE', 'current_price' => '2650.40', 'previous_close' => '2620.00', 'market_cap' => 7250000000000, 'avg_volume' => 4200000, 'pe_ratio' => '58.50', 'week_52_high' => '2800.00', 'week_52_low' => '2100.00', 'dividend_yield' => '1.35', 'beta' => '0.45'],
            ['symbol' => 'POWERGRID', 'name' => 'Power Grid Corp of India', 'sector' => 'Utilities', 'exchange' => 'NSE', 'current_price' => '245.80', 'previous_close' => '243.00', 'market_cap' => 1650000000000, 'avg_volume' => 8500000, 'pe_ratio' => '14.50', 'week_52_high' => '275.00', 'week_52_low' => '195.00', 'dividend_yield' => '2.80', 'beta' => '0.60'],
            ['symbol' => 'NTPC', 'name' => 'NTPC Ltd', 'sector' => 'Utilities', 'exchange' => 'NSE', 'current_price' => '185.50', 'previous_close' => '183.00', 'market_cap' => 1750000000000, 'avg_volume' => 12000000, 'pe_ratio' => '12.80', 'week_52_high' => '210.00', 'week_52_low' => '145.00', 'dividend_yield' => '2.50', 'beta' => '0.55'],
            ['symbol' => 'ONGC', 'name' => 'Oil and Natural Gas Corp', 'sector' => 'Oil & Gas', 'exchange' => 'NSE', 'current_price' => '235.40', 'previous_close' => '232.00', 'market_cap' => 1350000000000, 'avg_volume' => 9500000, 'pe_ratio' => '11.20', 'week_52_high' => '265.00', 'week_52_low' => '175.00', 'dividend_yield' => '2.10', 'beta' => '0.85'],
            ['symbol' => 'COALINDIA', 'name' => 'Coal India Ltd', 'sector' => 'Mining', 'exchange' => 'NSE', 'current_price' => '345.60', 'previous_close' => '340.00', 'market_cap' => 1150000000000, 'avg_volume' => 11000000, 'pe_ratio' => '10.50', 'week_52_high' => '380.00', 'week_52_low' => '260.00', 'dividend_yield' => '2.90', 'beta' => '0.70'],
            ['symbol' => 'GAIL', 'name' => 'GAIL (India) Ltd', 'sector' => 'Gas', 'exchange' => 'NSE', 'current_price' => '195.30', 'previous_close' => '192.00', 'market_cap' => 950000000000, 'avg_volume' => 7500000, 'pe_ratio' => '13.80', 'week_52_high' => '225.00', 'week_52_low' => '155.00', 'dividend_yield' => '2.40', 'beta' => '0.65'],
            ['symbol' => 'BHUSANSTL', 'name' => 'Bhushan Steel Ltd', 'sector' => 'Metals', 'exchange' => 'NSE', 'current_price' => '52.80', 'previous_close' => '51.50', 'market_cap' => 450000000000, 'avg_volume' => 18000000, 'pe_ratio' => '8.50', 'week_52_high' => '65.00', 'week_52_low' => '35.00', 'dividend_yield' => '0.00', 'beta' => '1.60'],
            ['symbol' => 'UPL', 'name' => 'UPL Ltd', 'sector' => 'Chemicals', 'exchange' => 'NSE', 'current_price' => '785.20', 'previous_close' => '775.00', 'market_cap' => 5500000000000, 'avg_volume' => 3500000, 'pe_ratio' => '45.20', 'week_52_high' => '850.00', 'week_52_low' => '520.00', 'dividend_yield' => '0.35', 'beta' => '1.15'],
            ['symbol' => 'DIVISLAB', 'name' => 'Divi\'s Laboratories Ltd', 'sector' => 'Pharma', 'exchange' => 'NSE', 'current_price' => '4250.00', 'previous_close' => '4200.00', 'market_cap' => 6200000000000, 'avg_volume' => 1200000, 'pe_ratio' => '65.80', 'week_52_high' => '4600.00', 'week_52_low' => '3100.00', 'dividend_yield' => '0.15', 'beta' => '0.85'],
            ['symbol' => 'DRREDDY', 'name' => 'Dr. Reddy\'s Laboratories', 'sector' => 'Pharma', 'exchange' => 'NSE', 'current_price' => '5250.50', 'previous_close' => '5180.00', 'market_cap' => 5100000000000, 'avg_volume' => 2500000, 'pe_ratio' => '35.40', 'week_52_high' => '5800.00', 'week_52_low' => '3800.00', 'dividend_yield' => '0.20', 'beta' => '0.90'],
            ['symbol' => 'CIPLA', 'name' => 'Cipla Ltd', 'sector' => 'Pharma', 'exchange' => 'NSE', 'current_price' => '785.30', 'previous_close' => '778.00', 'market_cap' => 1950000000000, 'avg_volume' => 4500000, 'pe_ratio' => '28.50', 'week_52_high' => '850.00', 'week_52_low' => '580.00', 'dividend_yield' => '0.45', 'beta' => '0.70'],
            ['symbol' => 'AAPL', 'name' => 'Apple Inc', 'sector' => 'Technology', 'exchange' => 'NASDAQ', 'current_price' => '189.50', 'previous_close' => '188.20', 'market_cap' => 2950000000000, 'avg_volume' => 55000000, 'pe_ratio' => '32.50', 'week_52_high' => '199.62', 'week_52_low' => '164.08', 'dividend_yield' => '0.52', 'beta' => '1.20'],
            ['symbol' => 'MSFT', 'name' => 'Microsoft Corp', 'sector' => 'Technology', 'exchange' => 'NASDAQ', 'current_price' => '420.50', 'previous_close' => '418.30', 'market_cap' => 3120000000000, 'avg_volume' => 25000000, 'pe_ratio' => '35.20', 'week_52_high' => '430.82', 'week_52_low' => '309.45', 'dividend_yield' => '0.71', 'beta' => '0.90'],
            ['symbol' => 'GOOGL', 'name' => 'Alphabet Inc', 'sector' => 'Technology', 'exchange' => 'NASDAQ', 'current_price' => '175.80', 'previous_close' => '174.50', 'market_cap' => 2200000000000, 'avg_volume' => 28000000, 'pe_ratio' => '28.40', 'week_52_high' => '191.75', 'week_52_low' => '121.45', 'dividend_yield' => '0.00', 'beta' => '1.05'],
            ['symbol' => 'AMZN', 'name' => 'Amazon.com Inc', 'sector' => 'Consumer', 'exchange' => 'NASDAQ', 'current_price' => '185.60', 'previous_close' => '183.90', 'market_cap' => 1920000000000, 'avg_volume' => 45000000, 'pe_ratio' => '45.80', 'week_52_high' => '189.77', 'week_52_low' => '118.35', 'dividend_yield' => '0.00', 'beta' => '1.15'],
            ['symbol' => 'NVDA', 'name' => 'NVIDIA Corp', 'sector' => 'Technology', 'exchange' => 'NASDAQ', 'current_price' => '875.30', 'previous_close' => '868.50', 'market_cap' => 2160000000000, 'avg_volume' => 42000000, 'pe_ratio' => '65.40', 'week_52_high' => '974.00', 'week_52_low' => '395.00', 'dividend_yield' => '0.02', 'beta' => '1.65'],
            ['symbol' => 'META', 'name' => 'Meta Platforms Inc', 'sector' => 'Technology', 'exchange' => 'NASDAQ', 'current_price' => '505.75', 'previous_close' => '502.30', 'market_cap' => 1290000000000, 'avg_volume' => 18000000, 'pe_ratio' => '33.50', 'week_52_high' => '531.49', 'week_52_low' => '195.71', 'dividend_yield' => '0.00', 'beta' => '1.30'],
            ['symbol' => 'TSLA', 'name' => 'Tesla Inc', 'sector' => 'Automotive', 'exchange' => 'NASDAQ', 'current_price' => '175.40', 'previous_close' => '173.80', 'market_cap' => 560000000000, 'avg_volume' => 95000000, 'pe_ratio' => '55.20', 'week_52_high' => '299.29', 'week_52_low' => '152.37', 'dividend_yield' => '0.00', 'beta' => '2.05'],
            ['symbol' => 'JPM', 'name' => 'JPMorgan Chase & Co', 'sector' => 'Banking', 'exchange' => 'NYSE', 'current_price' => '198.60', 'previous_close' => '197.20', 'market_cap' => 570000000000, 'avg_volume' => 12000000, 'pe_ratio' => '11.50', 'week_52_high' => '205.89', 'week_52_low' => '135.19', 'dividend_yield' => '2.35', 'beta' => '1.10'],
            ['symbol' => 'JNJ', 'name' => 'Johnson & Johnson', 'sector' => 'Healthcare', 'exchange' => 'NYSE', 'current_price' => '156.80', 'previous_close' => '155.90', 'market_cap' => 400000000000, 'avg_volume' => 7000000, 'pe_ratio' => '22.80', 'week_52_high' => '175.00', 'week_52_low' => '145.50', 'dividend_yield' => '3.10', 'beta' => '0.60'],
            ['symbol' => 'V', 'name' => 'Visa Inc', 'sector' => 'Financial', 'exchange' => 'NYSE', 'current_price' => '275.40', 'previous_close' => '273.80', 'market_cap' => 560000000000, 'avg_volume' => 8000000, 'pe_ratio' => '30.50', 'week_52_high' => '280.00', 'week_52_low' => '195.00', 'dividend_yield' => '0.75', 'beta' => '0.95'],
            ['symbol' => 'PG', 'name' => 'Procter & Gamble', 'sector' => 'Consumer', 'exchange' => 'NYSE', 'current_price' => '165.30', 'previous_close' => '164.50', 'market_cap' => 390000000000, 'avg_volume' => 6500000, 'pe_ratio' => '26.40', 'week_52_high' => '168.00', 'week_52_low' => '135.00', 'dividend_yield' => '2.45', 'beta' => '0.40'],
            ['symbol' => 'UNH', 'name' => 'UnitedHealth Group', 'sector' => 'Healthcare', 'exchange' => 'NYSE', 'current_price' => '525.80', 'previous_close' => '522.40', 'market_cap' => 470000000000, 'avg_volume' => 3500000, 'pe_ratio' => '22.10', 'week_52_high' => '559.00', 'week_52_low' => '385.00', 'dividend_yield' => '1.15', 'beta' => '0.70'],
            ['symbol' => 'HD', 'name' => 'Home Depot Inc', 'sector' => 'Consumer', 'exchange' => 'NYSE', 'current_price' => '345.20', 'previous_close' => '342.80', 'market_cap' => 350000000000, 'avg_volume' => 4500000, 'pe_ratio' => '23.50', 'week_52_high' => '380.00', 'week_52_low' => '275.00', 'dividend_yield' => '2.50', 'beta' => '0.90'],
            ['symbol' => 'MA', 'name' => 'Mastercard Inc', 'sector' => 'Financial', 'exchange' => 'NYSE', 'current_price' => '485.60', 'previous_close' => '482.30', 'market_cap' => 430000000000, 'avg_volume' => 5000000, 'pe_ratio' => '30.80', 'week_52_high' => '500.00', 'week_52_low' => '350.00', 'dividend_yield' => '0.55', 'beta' => '1.05'],
            ['symbol' => 'BAC', 'name' => 'Bank of America Corp', 'sector' => 'Banking', 'exchange' => 'NYSE', 'current_price' => '38.50', 'previous_close' => '38.20', 'market_cap' => 310000000000, 'avg_volume' => 45000000, 'pe_ratio' => '12.80', 'week_52_high' => '40.00', 'week_52_low' => '28.00', 'dividend_yield' => '2.85', 'beta' => '1.20'],
            ['symbol' => 'DIS', 'name' => 'Walt Disney Co', 'sector' => 'Entertainment', 'exchange' => 'NYSE', 'current_price' => '98.40', 'previous_close' => '97.80', 'market_cap' => 180000000000, 'avg_volume' => 12000000, 'pe_ratio' => '72.50', 'week_52_high' => '120.00', 'week_52_low' => '78.00', 'dividend_yield' => '0.55', 'beta' => '1.10'],
            ['symbol' => 'ADBE', 'name' => 'Adobe Inc', 'sector' => 'Technology', 'exchange' => 'NASDAQ', 'current_price' => '585.30', 'previous_close' => '580.20', 'market_cap' => 260000000000, 'avg_volume' => 4500000, 'pe_ratio' => '45.60', 'week_52_high' => '688.00', 'week_52_low' => '420.00', 'dividend_yield' => '0.00', 'beta' => '1.25'],
            ['symbol' => 'NFLX', 'name' => 'Netflix Inc', 'sector' => 'Entertainment', 'exchange' => 'NASDAQ', 'current_price' => '625.80', 'previous_close' => '620.50', 'market_cap' => 270000000000, 'avg_volume' => 5500000, 'pe_ratio' => '48.20', 'week_52_high' => '690.00', 'week_52_low' => '350.00', 'dividend_yield' => '0.00', 'beta' => '1.35'],
            ['symbol' => 'PYPL', 'name' => 'PayPal Holdings Inc', 'sector' => 'Financial', 'exchange' => 'NASDAQ', 'current_price' => '62.40', 'previous_close' => '61.80', 'market_cap' => 68000000000, 'avg_volume' => 15000000, 'pe_ratio' => '18.50', 'week_52_high' => '75.00', 'week_52_low' => '50.00', 'dividend_yield' => '0.00', 'beta' => '1.25'],
            ['symbol' => 'INTC', 'name' => 'Intel Corp', 'sector' => 'Technology', 'exchange' => 'NASDAQ', 'current_price' => '36.50', 'previous_close' => '36.20', 'market_cap' => 155000000000, 'avg_volume' => 35000000, 'pe_ratio' => '15.80', 'week_52_high' => '55.00', 'week_52_low' => '28.00', 'dividend_yield' => '1.65', 'beta' => '0.95'],
            ['symbol' => 'CSCO', 'name' => 'Cisco Systems Inc', 'sector' => 'Technology', 'exchange' => 'NASDAQ', 'current_price' => '52.80', 'previous_close' => '52.40', 'market_cap' => 210000000000, 'avg_volume' => 18000000, 'pe_ratio' => '18.20', 'week_52_high' => '58.00', 'week_52_low' => '38.00', 'dividend_yield' => '3.10', 'beta' => '0.90'],
            ['symbol' => 'PFE', 'name' => 'Pfizer Inc', 'sector' => 'Healthcare', 'exchange' => 'NYSE', 'current_price' => '28.50', 'previous_close' => '28.30', 'market_cap' => 162000000000, 'avg_volume' => 45000000, 'pe_ratio' => '12.50', 'week_52_high' => '42.00', 'week_52_low' => '25.00', 'dividend_yield' => '5.80', 'beta' => '0.65'],
            ['symbol' => 'XOM', 'name' => 'Exxon Mobil Corp', 'sector' => 'Energy', 'exchange' => 'NYSE', 'current_price' => '105.60', 'previous_close' => '104.80', 'market_cap' => 430000000000, 'avg_volume' => 15000000, 'pe_ratio' => '11.80', 'week_52_high' => '120.00', 'week_52_low' => '95.00', 'dividend_yield' => '3.45', 'beta' => '0.85'],
            ['symbol' => 'CVX', 'name' => 'Chevron Corp', 'sector' => 'Energy', 'exchange' => 'NYSE', 'current_price' => '155.30', 'previous_close' => '154.20', 'market_cap' => 290000000000, 'avg_volume' => 8000000, 'pe_ratio' => '13.50', 'week_52_high' => '165.00', 'week_52_low' => '115.00', 'dividend_yield' => '3.80', 'beta' => '0.90'],
            ['symbol' => 'KO', 'name' => 'Coca-Cola Co', 'sector' => 'Consumer', 'exchange' => 'NYSE', 'current_price' => '60.80', 'previous_close' => '60.40', 'market_cap' => 260000000000, 'avg_volume' => 12000000, 'pe_ratio' => '24.50', 'week_52_high' => '65.00', 'week_52_low' => '48.00', 'dividend_yield' => '3.05', 'beta' => '0.55'],
            ['symbol' => 'PEP', 'name' => 'PepsiCo Inc', 'sector' => 'Consumer', 'exchange' => 'NASDAQ', 'current_price' => '175.40', 'previous_close' => '174.20', 'market_cap' => 230000000000, 'avg_volume' => 5500000, 'pe_ratio' => '26.80', 'week_52_high' => '185.00', 'week_52_low' => '145.00', 'dividend_yield' => '2.90', 'beta' => '0.65'],
            ['symbol' => 'COST', 'name' => 'Costco Wholesale Corp', 'sector' => 'Consumer', 'exchange' => 'NASDAQ', 'current_price' => '725.50', 'previous_close' => '720.30', 'market_cap' => 300000000000, 'avg_volume' => 3500000, 'pe_ratio' => '42.50', 'week_52_high' => '780.00', 'week_52_low' => '550.00', 'dividend_yield' => '0.65', 'beta' => '0.70'],
            ['symbol' => 'AVGO', 'name' => 'Broadcom Inc', 'sector' => 'Technology', 'exchange' => 'NASDAQ', 'current_price' => '1350.60', 'previous_close' => '1342.80', 'market_cap' => 350000000000, 'avg_volume' => 5500000, 'pe_ratio' => '32.50', 'week_52_high' => '1550.00', 'week_52_low' => '850.00', 'dividend_yield' => '1.85', 'beta' => '1.15'],
            ['symbol' => 'TXN', 'name' => 'Texas Instruments Inc', 'sector' => 'Technology', 'exchange' => 'NASDAQ', 'current_price' => '175.30', 'previous_close' => '174.50', 'market_cap' => 160000000000, 'avg_volume' => 8500000, 'pe_ratio' => '25.40', 'week_52_high' => '200.00', 'week_52_low' => '145.00', 'dividend_yield' => '2.80', 'beta' => '0.95'],
            ['symbol' => 'QCOM', 'name' => 'QUALCOMM Inc', 'sector' => 'Technology', 'exchange' => 'NASDAQ', 'current_price' => '185.70', 'previous_close' => '184.20', 'market_cap' => 205000000000, 'avg_volume' => 7500000, 'pe_ratio' => '22.50', 'week_52_high' => '210.00', 'week_52_low' => '140.00', 'dividend_yield' => '2.10', 'beta' => '1.10'],
            ['symbol' => 'AMD', 'name' => 'Advanced Micro Devices', 'sector' => 'Technology', 'exchange' => 'NASDAQ', 'current_price' => '165.40', 'previous_close' => '163.80', 'market_cap' => 268000000000, 'avg_volume' => 55000000, 'pe_ratio' => '45.80', 'week_52_high' => '227.00', 'week_52_low' => '90.00', 'dividend_yield' => '0.00', 'beta' => '1.80'],
            ['symbol' => 'MU', 'name' => 'Micron Technology Inc', 'sector' => 'Technology', 'exchange' => 'NASDAQ', 'current_price' => '125.60', 'previous_close' => '124.30', 'market_cap' => 110000000000, 'avg_volume' => 18000000, 'pe_ratio' => '18.50', 'week_52_high' => '155.00', 'week_52_low' => '75.00', 'dividend_yield' => '0.50', 'beta' => '1.30'],
            ['symbol' => 'ORCL', 'name' => 'Oracle Corp', 'sector' => 'Technology', 'exchange' => 'NYSE', 'current_price' => '145.80', 'previous_close' => '144.50', 'market_cap' => 320000000000, 'avg_volume' => 10000000, 'pe_ratio' => '32.50', 'week_52_high' => '160.00', 'week_52_low' => '105.00', 'dividend_yield' => '1.40', 'beta' => '0.95'],
            ['symbol' => 'IBM', 'name' => 'International Business Machines', 'sector' => 'Technology', 'exchange' => 'NYSE', 'current_price' => '165.40', 'previous_close' => '164.20', 'market_cap' => 155000000000, 'avg_volume' => 3500000, 'pe_ratio' => '22.80', 'week_52_high' => '185.00', 'week_52_low' => '135.00', 'dividend_yield' => '3.50', 'beta' => '0.75'],
            ['symbol' => 'GE', 'name' => 'General Electric Co', 'sector' => 'Industrial', 'exchange' => 'NYSE', 'current_price' => '165.80', 'previous_close' => '164.50', 'market_cap' => 175000000000, 'avg_volume' => 45000000, 'pe_ratio' => '18.50', 'week_52_high' => '175.00', 'week_52_low' => '120.00', 'dividend_yield' => '0.45', 'beta' => '1.10'],
            ['symbol' => 'CAT', 'name' => 'Caterpillar Inc', 'sector' => 'Industrial', 'exchange' => 'NYSE', 'current_price' => '345.60', 'previous_close' => '342.80', 'market_cap' => 175000000000, 'avg_volume' => 3500000, 'pe_ratio' => '15.50', 'week_52_high' => '380.00', 'week_52_low' => '240.00', 'dividend_yield' => '1.85', 'beta' => '1.05'],
            ['symbol' => 'MMM', 'name' => '3M Co', 'sector' => 'Industrial', 'exchange' => 'NYSE', 'current_price' => '105.40', 'previous_close' => '104.80', 'market_cap' => 62000000000, 'avg_volume' => 4500000, 'pe_ratio' => '18.20', 'week_52_high' => '130.00', 'week_52_low' => '85.00', 'dividend_yield' => '4.20', 'beta' => '0.95'],
            ['symbol' => 'NKE', 'name' => 'Nike Inc', 'sector' => 'Consumer', 'exchange' => 'NYSE', 'current_price' => '115.80', 'previous_close' => '114.50', 'market_cap' => 180000000000, 'avg_volume' => 8500000, 'pe_ratio' => '28.50', 'week_52_high' => '135.00', 'week_52_low' => '88.00', 'dividend_yield' => '1.25', 'beta' => '0.90'],
            ['symbol' => 'MCD', 'name' => 'McDonald\'s Corp', 'sector' => 'Consumer', 'exchange' => 'NYSE', 'current_price' => '285.60', 'previous_close' => '283.40', 'market_cap' => 200000000000, 'avg_volume' => 4500000, 'pe_ratio' => '24.50', 'week_52_high' => '305.00', 'week_52_low' => '230.00', 'dividend_yield' => '2.25', 'beta' => '0.70'],
            ['symbol' => 'WMT', 'name' => 'Walmart Inc', 'sector' => 'Consumer', 'exchange' => 'NYSE', 'current_price' => '68.50', 'previous_close' => '68.20', 'market_cap' => 450000000000, 'avg_volume' => 15000000, 'pe_ratio' => '26.50', 'week_52_high' => '75.00', 'week_52_low' => '55.00', 'dividend_yield' => '1.35', 'beta' => '0.50'],
            ['symbol' => 'TGT', 'name' => 'Target Corp', 'sector' => 'Consumer', 'exchange' => 'NYSE', 'current_price' => '145.80', 'previous_close' => '144.50', 'market_cap' => 75000000000, 'avg_volume' => 5500000, 'pe_ratio' => '22.50', 'week_52_high' => '165.00', 'week_52_low' => '115.00', 'dividend_yield' => '1.65', 'beta' => '0.80'],
            ['symbol' => 'NEE', 'name' => 'NextEra Energy Inc', 'sector' => 'Utilities', 'exchange' => 'NYSE', 'current_price' => '72.50', 'previous_close' => '72.00', 'market_cap' => 145000000000, 'avg_volume' => 8500000, 'pe_ratio' => '20.50', 'week_52_high' => '85.00', 'week_52_low' => '55.00', 'dividend_yield' => '2.85', 'beta' => '0.55'],
            ['symbol' => 'DUK', 'name' => 'Duke Energy Corp', 'sector' => 'Utilities', 'exchange' => 'NYSE', 'current_price' => '105.40', 'previous_close' => '104.80', 'market_cap' => 78000000000, 'avg_volume' => 5500000, 'pe_ratio' => '18.50', 'week_52_high' => '115.00', 'week_52_low' => '78.00', 'dividend_yield' => '3.85', 'beta' => '0.45'],
            ['symbol' => 'BA', 'name' => 'Boeing Co', 'sector' => 'Aerospace', 'exchange' => 'NYSE', 'current_price' => '185.60', 'previous_close' => '183.40', 'market_cap' => 110000000000, 'avg_volume' => 8500000, 'pe_ratio' => '45.50', 'week_52_high' => '220.00', 'week_52_low' => '120.00', 'dividend_yield' => '0.00', 'beta' => '1.45'],
            ['symbol' => 'RTX', 'name' => 'RTX Corp', 'sector' => 'Aerospace', 'exchange' => 'NYSE', 'current_price' => '95.40', 'previous_close' => '94.80', 'market_cap' => 145000000000, 'avg_volume' => 6500000, 'pe_ratio' => '28.50', 'week_52_high' => '115.00', 'week_52_low' => '72.00', 'dividend_yield' => '1.85', 'beta' => '0.85'],
            ['symbol' => 'LMT', 'name' => 'Lockheed Martin Corp', 'sector' => 'Defense', 'exchange' => 'NYSE', 'current_price' => '465.80', 'previous_close' => '462.50', 'market_cap' => 115000000000, 'avg_volume' => 1500000, 'pe_ratio' => '18.50', 'week_52_high' => '520.00', 'week_52_low' => '380.00', 'dividend_yield' => '2.55', 'beta' => '0.60'],
            ['symbol' => 'GS', 'name' => 'Goldman Sachs Group', 'sector' => 'Banking', 'exchange' => 'NYSE', 'current_price' => '425.60', 'previous_close' => '422.30', 'market_cap' => 130000000000, 'avg_volume' => 3500000, 'pe_ratio' => '14.50', 'week_52_high' => '480.00', 'week_52_low' => '280.00', 'dividend_yield' => '2.40', 'beta' => '1.35'],
            ['symbol' => 'BLK', 'name' => 'BlackRock Inc', 'sector' => 'Financial', 'exchange' => 'NYSE', 'current_price' => '825.40', 'previous_close' => '820.20', 'market_cap' => 130000000000, 'avg_volume' => 1800000, 'pe_ratio' => '22.50', 'week_52_high' => '900.00', 'week_52_low' => '620.00', 'dividend_yield' => '1.65', 'beta' => '1.10'],
            ['symbol' => 'SCHW', 'name' => 'Charles Schwab Corp', 'sector' => 'Financial', 'exchange' => 'NYSE', 'current_price' => '68.50', 'previous_close' => '68.00', 'market_cap' => 140000000000, 'avg_volume' => 12000000, 'pe_ratio' => '18.50', 'week_52_high' => '85.00', 'week_52_low' => '48.00', 'dividend_yield' => '1.25', 'beta' => '1.20'],
            ['symbol' => 'AXP', 'name' => 'American Express Co', 'sector' => 'Financial', 'exchange' => 'NYSE', 'current_price' => '235.60', 'previous_close' => '233.80', 'market_cap' => 170000000000, 'avg_volume' => 4500000, 'pe_ratio' => '22.50', 'week_52_high' => '255.00', 'week_52_low' => '175.00', 'dividend_yield' => '1.15', 'beta' => '0.85'],
        ];

        foreach ($stocks as $data) {
            $data['created_at'] = date('Y-m-d H:i:s');
            $data['updated_at'] = date('Y-m-d H:i:s');
            $data['from_yahoo'] = false;
            $data['last_fetched'] = null;
            $this->db->table('stocks')->insert($data);

            $stockId = $this->db->insertID();
            $basePrice = (float) $data['current_price'];
            generate_price_history($stockId, $basePrice);
            generate_predictions($stockId, $basePrice);
        }

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
