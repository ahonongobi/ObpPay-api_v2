<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Wallet;
use Illuminate\Http\Request;

class WalletController extends Controller
{
    public function showCreditForm()
    {
        return view('admin.wallets.credit');
    }

    public function credit(Request $request)
    {
        return view('admin.wallets.creditadmin');
    }

    public function processCredit(Request $request)
    {
        $data = $request->validate([
            'obp_id' => 'required|string|exists:users,obp_id',
            'amount' => 'required|numeric|min:0.01',
            'reason' => 'nullable|string|max:255',
        ]);

        


        $client = \App\Models\User::where('obp_id', $data['obp_id'])->first();


        $clientWallet = $client->wallet;

      

        if (!$clientWallet) {
            return back()->with("error", "L'utilisateur n'a pas encore de portefeuille.");
        }


        $admin = auth()->user();  // The admin performing the operation
        $adminWallet = $admin->wallet;

        if (!$adminWallet) {
            return back()->with("error", "Aucun portefeuille trouvé pour l'administrateur.");
        }

        // Vérifier que l’admin a assez d’argent pour créditer
        if ($adminWallet->balance < $data['amount']) {
            return back()->with('error', "Solde insuffisant sur votre compte admin.");
        }

        \Illuminate\Support\Facades\DB::transaction(function () use ($client, $clientWallet, $admin, $adminWallet, $data) {

            //  1. DÉBITER l’admin
            $adminWallet->decrement('balance', $data['amount']);

            \App\Models\Transactions::create([
                'user_id'    => $admin->id,
                'type'       => 'admin_credit_out', // nouvelle transaction admin
                'amount'     => $data['amount'],
                'currency'   => $adminWallet->currency,
                'description' => "Crédit envoyé à {$client->obp_id}",
                'status'     => 'completed',
                'meta'       => [
                    'to' => $client->obp_id,
                    'reason' => $data['reason'] ?? '',
                ],
            ]);

            //  2. CRÉDITER le client
            $clientWallet->increment('balance', $data['amount']);

            \App\Models\Transactions::create([
                'user_id'    => $client->id,
                'type'       => 'admin_credit_in', // entrée côté client
                'amount'     => $data['amount'],
                'currency'   => $clientWallet->currency,
                'description' => $data['reason'] ?? 'Crédit administrateur',
                'status'     => 'completed',
                'meta'       => [
                    'from' => $admin->obp_id,
                    'reason' => $data['reason'] ?? '',
                ],
            ]);
        });

        // LOG ADMIN
        admin_log(
            'wallet',
            "Crédit de {$data['amount']} {$clientWallet->currency} pour l'utilisateur {$client->obp_id} effectué par {$admin->name}",
            [
                'amount' => $data['amount'],
                'reason' => $data['reason'] ?? null,
            ]
        );

        return redirect()->route('admin.wallets.credit')
            ->with('success', 'Crédit effectué avec succès (déduit du solde admin).');
    }
}
