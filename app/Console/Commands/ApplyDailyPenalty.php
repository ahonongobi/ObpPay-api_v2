<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Loanrequests;
use Illuminate\Support\Facades\Log;

class ApplyDailyPenalty extends Command
{
    protected $signature = 'loans:apply-penalty';
    protected $description = 'Apply daily penalty to overdue loans';

    public function handle()
    {
        $penaltyRate = 0.01595; // 1.595%

        // Get all approved loans that are overdue and not fully paid
        $loans = Loanrequests::where('status', 'approved')
            ->where('due_date', '<', now())
            ->whereColumn('due_amount', '>', 'amount') // optional: only if not fully paid
            ->get();

        foreach ($loans as $loan) {
            $loan->due_amount *= (1 + $penaltyRate);
            $loan->save();

            // Optionally, send a notification
            if ($loan->user->fcm_token) {
                try {
                    $firebase = new \App\Services\FirebaseService();
                    $firebase->sendToToken(
                        $loan->user->fcm_token,
                        "Frais journaliers",
                        "Votre montant dû pour le prêt #{$loan->id} a augmenté à {$loan->due_amount} XOF à cause du retard.",
                        [
                            "screen" => "loan",
                            "loan_id" => $loan->id
                        ]
                    );
                } catch (\Exception $e) {
                    Log::error("FCM PENALTY ERROR: " . $e->getMessage());
                }
            }
        }

        $this->info('Daily penalties applied.');
    }
}
