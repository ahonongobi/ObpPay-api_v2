<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('loanrequests', function (Blueprint $table) {
            $table->decimal('penalty_amount', 12, 2)->default(0);
            $table->integer('weeks_late')->default(0);
            $table->decimal('interest_amount', 12, 2)->default(0);
            $table->integer('interest_weeks')->default(0);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        //
    }
};
