<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateLoanRequestsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('loan_requests', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('borrower_id')->index();
            $table->timestamp('request_at');
            $table->timestamp('deadline_at');
            $table->timestamp('payday_at');
            $table->enum('status', ['approved', 'unapproved'])->default('unapproved');
            $table->integer('duration');
            $table->integer('repayment_frequency');
            $table->decimal('amount', 13, 4);
            $table->float('interest_rate');
            $table->decimal('arrangement_fee', 13, 4);
            $table->string('description', 255);
            $table->timestamps();
        });

        Schema::table('loan_requests', function (Blueprint $table) {
            $table->foreign('borrower_id')->references('id')->on('borrowers')->onUpdate('cascade')->onDelete('restrict');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('loan_requests', function (Blueprint $table) {
            $table->dropForeign(['borrower_id']);
        });

        Schema::dropIfExists('loan_requests');
    }
}
