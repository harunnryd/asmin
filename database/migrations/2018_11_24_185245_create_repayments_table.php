<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateRepaymentsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('repayments', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('loan_request_id')->index();
            $table->uuid('borrower_id')->index();
            $table->enum('status', ['paid', 'unpaid'])->default('unpaid');
            $table->decimal('amount', 14, 2);
            $table->dateTime('date_at');
            $table->dateTime('deadline_at');
            $table->timestamps();
        });

        Schema::table('repayments', function (Blueprint $table) {
            $table->foreign('loan_request_id')->references('id')->on('loan_requests')->onUpdate('cascade')->onDelete('restrict');
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
        Schema::table('repayments', function (Blueprint $table) {
            $table->dropForeign(['loan_request_id']);
        });

        Schema::table('repayments', function (Blueprint $table) {
            $table->dropForeign(['borrower_id']);
        });

        Schema::dropIfExists('repayments');
    }
}
