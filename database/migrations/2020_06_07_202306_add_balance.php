<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class AddBalance extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('user_finantial_statuses', function (Blueprint $table) {
            $table->renameColumn('debt', 'balance');
        });
        DB::update('update user_finantial_statuses set balance = balance * -1');
        Schema::table('payments', function (Blueprint $table) {
            $table->decimal('uncharged', 10, 2);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('user_finantial_statuses', function (Blueprint $table) {
            $table->renameColumn('balance', 'debt');
        });
        DB::update('update user_finantial_statuses set debt = debt * -1');
        Schema::table('payments', function (Blueprint $table) {
            $table->dropColumn('uncharged', 10, 2);
        });
    }
}
