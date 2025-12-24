<?php

namespace App\Http\Controllers;

use App\Models\Loanrequests;
use Illuminate\Http\Request;
use Carbon\Carbon;
class LoanrequestsController extends Controller
{
    public function eligibility(Request $request)
    {

        // get user score from user table and check if its geather than 50, then eligible

        $user_score = $request->user()->score;
        if ($user_score > 50) {
            return response()->json([
                'eligibility' => 1,
                'message' => 'Votre score utilisateur vous rend éligible pour un prêt.',
            ]);
        }
        else {
            return response()->json([
                'eligibility' => 0,
                'message' => 'Votre score utilisateur est insuffisant pour un prêt.',
            ]);
        }

        // eligible si solde >= 50 000
        //$score = min(1, $wallet->balance / 50000);

        //return response()->json([
         //   'eligibility' => $score, // 0..1
        //    'message' => $score >= 0.5 ? 'Vous êtes éligible pour un prêt substantiel.'
         //       : 'Éligibilité faible.',
        //]);
    }

    public function requestLoan(Request $request)
    {
        $data = $request->validate([
            'category'        => 'required|string',
            'custom_category' => 'nullable|string',
            'amount'          => 'required|numeric|min:1',
            'notes'           => 'nullable|string',
            'due_date'       => 'nullable|date',
        ]);

        // if duedate is  1 semines  then set to today + 7 days
        // if 2 semaines then today + 14 days
        // if 1 month then today + 30 days
        

        $duration = $data['due_date'] ?? '1 mois';

        switch ($duration) {
            case '1 semaine':
                $dueDate = Carbon::now()->addDays(7);
                break;

            case '2 semaines':
                $dueDate = Carbon::now()->addDays(14);
                break;

            case '1 mois':
                $dueDate = Carbon::now()->addDays(30);
                break;

            default:
                return response()->json([
                    'message' => 'Durée invalide.'
                ], 422);
        }


        $loan = Loanrequests::create([
            'user_id'        => $request->user()->id,
            'category'       => $data['category'],
            'custom_category' => $data['custom_category'] ?? null,
            'amount'         => $data['amount'],
            'status'         => 'pending',
            'notes'          => $data['notes'] ?? null,
            // 'due_date'       => $data['due_date'] ?? today + 30 days,
            'due_date'       => $data['due_date'] ?? now()->addDays(30),
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Demande de prêt envoyée.',
            'loan'    => $loan,
            
        ], 201);
    }
}
