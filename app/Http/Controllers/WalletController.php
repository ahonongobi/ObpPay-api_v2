<?php

namespace App\Http\Controllers;

use App\Models\Transaction;
use App\Models\Transactions;
use App\Services\ScoreService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class WalletController extends Controller
{
    public function balance(Request $request)
    {
        $wallet = $request->user()->wallet;
        return response()->json([
            'balance'  => $wallet->balance,
            'currency' => $wallet->currency,
            'obp_id'   => $request->user()->obp_id,
        ]);
    }

    public function transactions(Request $request)
    {
        $tx = $request->user()
            ->transactions()
            ->orderByDesc('created_at')
            ->limit(20)
            ->get();

        return response()->json($tx);
    }

    public function deposit(Request $request)
    {
        $data = $request->validate([
            'amount' => 'required|numeric|min:0.01',
            'source' => 'nullable|string',
        ]);

        $user = $request->user();
        $wallet = $user->wallet;

        DB::transaction(function () use ($wallet, $user, $data) {
            $wallet->increment('balance', $data['amount']);

            Transactions::create([
                'user_id'    => $user->id,
                'type'       => 'deposit',
                'amount'     => $data['amount'],
                'currency'   => $wallet->currency,
                'description' => $data['source'] ?? 'Recharge',
                'status'     => 'completed',
                'meta'       => null,
            ]);
        });

        add_score($user, 3, "deposit");



        return response()->json([
            'message' => 'Dépôt effectué.',
            'balance' => $wallet->fresh()->balance,
        ]);
    }

    public function transfer(Request $request)
    {
        $data = $request->validate([
            'to_obp_id' => 'required|string|exists:users,obp_id',
            'amount'    => 'required|numeric|min:0.01',
            'fees'      => 'required|numeric|min:0',     // frais envoyés depuis Flutter
            'total'     => 'required|numeric|min:0.01',  // total = amount + fees
            'note'      => 'nullable|string|max:255',
        ]);

        $fromUser = $request->user();
        $toUser   = \App\Models\User::where('obp_id', $data['to_obp_id'])->firstOrFail();

        if ($fromUser->id === $toUser->id) {
            return response()->json(['message' => 'Transfert vers vous-même interdit.'], 422);
        }

        $fromWallet = $fromUser->wallet;
        $toWallet   = $toUser->wallet;

        if ($fromWallet->balance < $data['total']) {
            return response()->json(['message' => 'Solde insuffisant.'], 422);
        }

        DB::transaction(function () use ($fromWallet, $toWallet, $fromUser, $toUser, $data) {
            $fromWallet->decrement('balance', $data['total']);
            $toWallet->increment('balance', $data['amount']);

            Transactions::create([
                'user_id'  => $fromUser->id,
                'type'     => 'transfer_out',
                'amount'   => $data['total'],
                'currency' => $fromWallet->currency,
                'description' => 'Transfert vers ' . $toUser->obp_id,
                'status'   => 'completed',
                'meta' => [
                    'to' => $toUser->obp_id,
                    'note' => $data['note'] ?? '',
                    'details' => [
                        'amount_initial' => $data['amount'], // Montant sans frais
                        'fees' => $data['fees'],            // Montant des frais
                        'total' => $data['total'],          // amount + fees
                        'rate' => [
                            'type' => 'transfer_obppay_to_obppay',
                            'value' => config('fees.transfer_obppay_to_obppay'),
                        ],
                    ],
                ],

            ]);

            Transactions::create([
                'user_id'  => $toUser->id,
                'type'     => 'transfer_in',
                'amount'   => $data['amount'],
                'currency' => $toWallet->currency,
                'description' => 'Transfert reçu de ' . $fromUser->obp_id,
                'status'   => 'completed',
                'meta'     => [
                    'from' => $fromUser->obp_id,
                     'details' => [
                        'amount_received' => $data['amount'],
                    ],
                    'note' => $data['note'] ?? '',
                ],
            ]);
        });
        add_score($fromUser, 5, "transfer");
        return response()->json([
            'message' => 'Transfert effectué.',
            'new_balance' => $fromWallet->fresh()->balance,
        ]);
    }
}
