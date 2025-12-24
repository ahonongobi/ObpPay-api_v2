<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Services\FeeService;
use Illuminate\Http\Request;
use App\Models\Loanrequest;
use App\Models\Loanrequests;
use Carbon\Carbon;


class UserController extends Controller
{
    public function index()
    {
        $users = User::orderBy('id', 'desc')->paginate(10);

        return view('admin.users.index', compact('users'));
    }

    public function show($id)
    {
    //$user = User::with('kyc')->findOrFail($id);
  
        $user = User::with(['wallet', 'kyc', 'transactions'])->findOrFail($id);

        $stats = [
            'total_transactions' => $user->transactions->count(),
            'total_amount' => $user->transactions->sum('amount'),

            'deposits' => $user->transactions->where('type', 'deposit')->sum('amount'),
            'withdraws' => $user->transactions->where('type', 'withdraw')->sum('amount'),

            'transfer_in' => $user->transactions->where('type', 'transfer_in')->sum('amount'),
            'transfer_out' => $user->transactions->where('type', 'transfer_out')->sum('amount'),

            'purchases' => $user->transactions->where('type', 'purchase')->sum('amount'),
        ];

        // Weekly interest and penalty
        // --- Weekly interest and penalty calculations ---
        //$balance = $user->wallet->balance ?? 0;


        //$stats['weeklyInterest'] = FeeService::weeklyInterest($balance);
        $stats['weeklyInterest'] = 0;
        $stats['weeklyPenalty']  = 0;

        $loan = Loanrequests::where('user_id', $user->id)
            ->where('status', 'approved')
            ->latest()
            ->first();


        if ($loan) {

            $amount = $loan->amount;

            // ====== WEEKLY INTEREST (depuis création) ======
            $weeksSinceLoan = Carbon::parse($loan->created_at)->diffInWeeks(now());
            $interest = $weeksSinceLoan * FeeService::weeklyInterest($amount);

            // Update loan if needed
            if ($interest != $loan->interest_amount) {
                $loan->update([
                    'interest_amount' => $interest,
                    'interest_weeks'  => $weeksSinceLoan,
                ]);
            }

            $stats['weeklyInterest'] = $interest;

            // ====== WEEKLY PENALTY (si retard) ======
            if ($loan->due_date && now()->greaterThan($loan->due_date)) {

                $weeksLate = Carbon::parse($loan->due_date)->diffInWeeks(now());
                $penalty = $weeksLate * FeeService::weeklyPenalty($amount);

                if ($penalty != $loan->penalty_amount) {
                    $loan->update([
                        'penalty_amount' => $penalty,
                        'weeks_late'     => $weeksLate,
                    ]);
                }

                $stats['weeklyPenalty'] = $penalty;
            }
        }


        // KYC status global
        $kycStatus = $user->kyc->contains('status', 'approved') ? 'approved' : ($user->kyc->contains('status', 'pending') ? 'pending' : 'none');

        return view('admin.users.show', compact('user', 'stats', 'kycStatus'));
    }



    public function edit($id)
    {
        $user = User::findOrFail($id);
        return view('admin.users.edit', compact('user'));
    }

    public function update(Request $request, $id)
    {

        $user = User::findOrFail($id);

        $request->validate([
            'name' => 'required|string|max:255',
            'phone' => 'required|string|max:20|unique:users,phone,' . $user->id, // <--- ignore current user
            'email' => 'nullable|email|max:255',
            'status' => 'required|in:active,blocked',
            'balance' => 'required|numeric',
        ]);

       // $user = User::findOrFail($id);

        // update user fields
        $user->update([
            'name' => $request->name,
            'phone' => $request->phone,
            'email' => $request->email,
            'status' => $request->status,
        ]);

        // update wallet balance
        if ($user->wallet) {
            $user->wallet->update([
                'balance' => $request->balance
            ]);
        }

        return redirect()->route('admin.users.show', $user->id)
            ->with('success', 'Utilisateur mis à jour avec succès.');
    }
}
