<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddPaypalColumnsOnTransactionPaymentsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('transaction_payments', function (Blueprint $table) {
            $table->smallInteger('mode')->unsigned()->after('transaction_id');

            $table->string('paypal_order_id')
                ->after('mode')
                ->nullable();

            $table->text('paypal_response_json')
                ->after('paypal_order_id')
                ->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('transaction_payments', function (Blueprint $table) {
            //
        });
    }
}
