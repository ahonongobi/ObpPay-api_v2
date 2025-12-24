<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\LoanRequest;
use App\Models\Loanrequests;
use Carbon\Carbon;

class ApplyWeeklyLoanInterest extends Command
{
    // Command signature
    protected $signature = 'loans:apply-weekly-interest';

    // Command description
    protected $description = 'Apply weekly interest of 1.595% to overdue loans';

    public function handle()
    {
        $this->info("Running weekly loan interest...");

        // Today
        $today = Carbon::now()->startOfDay();

        // Get loans that are not fully paid and due date < today
        $loans = Loanrequests::where('status', 'approved')
            ->whereDate('due_date', '<', $today)
            ->get();

        foreach ($loans as $loan) {
            $interestRate = 0.01595; // 1.595% per week
            $interest = $loan->amount * $interestRate;

            $loan->interest_amount += $interest;
            $loan->interest_weeks += 1;
            $loan->save();

            $this->info("Applied $interest XOF to loan ID {$loan->id}");
        }

        $this->info("Weekly interest applied successfully.");
    }
}
