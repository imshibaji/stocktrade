<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\InvestmentModel;
use App\Models\SellTransactionModel;
use App\Models\UserModel;

class Users extends BaseController
{
    public function users(): string
    {
        $userModel = new UserModel();
        $users = $userModel->orderBy('id', 'DESC')->findAll();

        $investmentModel = new InvestmentModel();
        $txModel = new SellTransactionModel();

        $adminUser = current_user();
        $baseCurrency = strtoupper($adminUser['base_currency'] ?? 'INR');

        foreach ($users as &$user) {
            $userId = (int) $user['id'];

            $taxRates = [
                'stcg_rate' => (float) ($user['stcg_rate'] ?? 15) / 100,
                'ltcg_rate' => (float) ($user['ltcg_rate'] ?? 10) / 100,
                'fee_rates' => get_fee_rates($user),
                'base_currency' => $baseCurrency,
            ];

            $investments = $investmentModel->getUserInvestments($userId);
            $user['investments_count'] = count($investments);

            $investedAmt = 0;
            foreach ($investments as $inv) {
                $native = stock_currency($inv['exchange'] ?? null);
                $investedAmt += convert_to_base_currency((float) $inv['total_invested'], $native);
            }
            $user['invested_amt'] = round($investedAmt, 2);

            $portfolio = $investmentModel->getPortfolioSummary($userId, $taxRates);
            $user['net_pl'] = $portfolio['total_net_profit'] ?? 0;

            $bookedPl = 0;
            foreach ($txModel->getUserTransactions($userId) as $tx) {
                $native = stock_currency($tx['exchange'] ?? null);
                $bookedPl += convert_to_base_currency((float) ($tx['net_profit_loss'] ?? 0), $native);
            }
            $user['booked_pl'] = round($bookedPl, 2);
        }
        unset($user);

        $data = [
            'title' => 'Users - Admin - StockTrade Tips',
            'users' => $users,
            'base_currency' => $baseCurrency,
        ];

        return view('templates/header', $data)
            . view('admin/layout', ['activePage' => 'users', 'content' => view('admin/users', $data)])
            . view('templates/footer');
    }

    public function makeAdmin($id)
    {
        $userModel = new UserModel();
        $userModel->update((int) $id, ['is_admin' => 1]);
        return redirect()->back()->with('success', 'User is now an admin.');
    }

    public function removeAdmin($id)
    {
        $userModel = new UserModel();
        $userModel->update((int) $id, ['is_admin' => 0]);
        return redirect()->back()->with('success', 'Admin privileges removed.');
    }

    public function deleteUser($id)
    {
        $userModel = new UserModel();
        $userModel->delete((int) $id);
        return redirect()->back()->with('success', 'User deleted.');
    }

    public function viewAsUser($id)
    {
        $userModel = new UserModel();
        $target = $userModel->find((int) $id);

        if (!$target) {
            return redirect()->back()->with('error', 'User not found.');
        }

        session()->set('impersonating_user_id', (int) $id);

        return redirect()->to('/dashboard')->with('success', 'Viewing as ' . $target['name']);
    }

    public function stopViewing()
    {
        session()->remove('impersonating_user_id');
        return redirect()->to('/admin/users')->with('success', 'Stopped viewing user. Back to admin.');
    }
}
