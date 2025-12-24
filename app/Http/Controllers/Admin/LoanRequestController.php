<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Loanrequests;
use App\Models\Wallet;
//use FirebaseService;
use App\Services\FirebaseService;

use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Log;

class LoanRequestController extends Controller
{
    public function index()
    {

        // going to the index page for laon that user have requested
        $loans = Loanrequests::with('user')->orderBy('id', 'desc')->paginate(10);
        return view('admin.loan.index', compact('loans'));
    }

    public function show($id)
    {

        // here we show details of a particular loan request
        $loan = Loanrequests::with('user')->findOrFail($id);

        // Eligibility = score > 50 (for now)
        $eligibility = $loan->user->score >= 50;

        return view('admin.loan.show', compact('loan', 'eligibility'));
    }

    public function approve($id)
    {
        // Get the loan with user
        $loan = Loanrequests::with('user')->findOrFail($id);

        // Update loan status and due date
        $loan->status = "approved";
        // $loan->due_date = now()->addWeek();

        // Calculate due amount with 1.48% interest
        $interestRate = 0.0148;
        $loan->due_amount = $loan->amount * (1 + $interestRate);

        // Log admin action
        admin_log(
            'loan',
            "Prêt #{$loan->id} approuvé",
            ['amount' => $loan->amount]
        );

        $loan->save();

        // Credit user wallet
        $wallet = Wallet::firstOrCreate(["user_id" => $loan->user_id]);
        $wallet->balance += $loan->amount;
        $wallet->save();

        $dueDate = Carbon::parse($loan->due_date);

        // FIREBASE notification
        if ($loan->user->fcm_token) {
            try {
                $firebase = new FirebaseService(); // ← IMPORTANT

                $firebase->sendToToken(
                    $loan->user->fcm_token,
                    "Bonne Nouvelle !",
                    "{$loan->amount} XOF ont été ajoutés à votre compte ObpPay ! Payez {$loan->due_amount} XOF TTC d'ici le {$dueDate->format('d/m/Y')} pour éviter les frais journaliers.",
                    [
                        "screen" => "loan",
                        "loan_id" => $loan->id
                    ]
                );
            } catch (\Exception $e) {
                Log::error("FCM APPROVE ERROR: " . $e->getMessage());
            }
        }

        return back()->with('success', 'Demande d’aide approuvée et montant crédité au portefeuille de l’utilisateur.');
    }


    public function reject($id)
    {
        // here we reject loan or aid request
        $loan = Loanrequests::with('user')->findOrFail($id);
        $loan->status = "rejected";
        $loan->save();


        admin_log(
            'loan',
            "Prêt #{$loan->id} rejeté",
            ['amount' => $loan->amount]
        );

        if ($loan->user->fcm_token) {
            try {
                $firebase = new FirebaseService(); // ← IMPORTANT

                $firebase->sendToToken(
                    $loan->user->fcm_token,
                    "Aide refusée",
                    "Votre demande d’aide a été refusée.",
                    ["screen" => "loan"]
                );
            } catch (\Exception $e) {
                Log::error("FCM REJECT ERROR: " . $e->getMessage());
            }
        }

        return back()->with('success', 'Demande d’aide refusée.');
    }
}
