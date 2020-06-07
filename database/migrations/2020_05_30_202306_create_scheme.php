<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateScheme extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('currencies', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->integer('value')->unsigned();
        });

        Schema::create('event_types', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('charge_group');
        });

        Schema::create('events', function (Blueprint $table) {
            $table->id();
            $table->decimal('amount', 10, 2);
            $table->bigInteger('user_id')->unsigned();
            $table->index('user_id');
            $table->foreign('user_id')->references('id')->on('users');
            $table->bigInteger('currency_id')->unsigned();
            $table->index('currency_id');
            $table->foreign('currency_id')->references('id')->on('currencies');
            $table->bigInteger('type_id')->unsigned();
            $table->index('type_id');
            $table->foreign('type_id')->references('id')->on('event_types');
            $table->timestamps();
        });

        Schema::create('invoices', function (Blueprint $table) {
            $table->id();
            $table->date('month');
            $table->decimal('amount', 10, 2); // as ARS
            $table->decimal('debt_amount', 10, 2); // as ARS
            $table->bigInteger('user_id')->unsigned();
            $table->index('user_id');
            $table->foreign('user_id')->references('id')->on('users');
            $table->timestamps();
        });

        Schema::create('charges', function (Blueprint $table) {
            $table->id();
            $table->decimal('amount', 10, 2); // as ARS
            $table->decimal('debt_amount', 10, 2); // as ARS
            $table->bigInteger('user_id')->unsigned();
            $table->index('user_id');
            $table->foreign('user_id')->references('id')->on('users');
            $table->bigInteger('event_id')->unsigned();
            $table->index('event_id');
            $table->foreign('event_id')->references('id')->on('events');
            $table->bigInteger('invoice_id')->unsigned()->nullable();
            $table->index('invoice_id');
            $table->foreign('invoice_id')->references('id')->on('invoices');
            $table->timestamps();
        });

        Schema::create('payments', function (Blueprint $table) {
            $table->id();
            $table->decimal('amount', 10, 2); // as ARS
            $table->bigInteger('user_id')->unsigned();
            $table->index('user_id');
            $table->foreign('user_id')->references('id')->on('users');
            $table->timestamps();
        });

        Schema::create('payment_charges', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('payment_id')->unsigned();
            $table->index('payment_id');
            $table->foreign('payment_id')->references('id')->on('payments');
            $table->bigInteger('charge_id')->unsigned();
            $table->index('charge_id');
            $table->foreign('charge_id')->references('id')->on('charges');
            $table->timestamps();
        });

        Schema::create('user_finantial_statuses', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('user_id')->unsigned();
            $table->index('user_id');
            $table->foreign('user_id')->references('id')->on('users');
            $table->decimal('debt', 10, 2); // as ARS
            $table->timestamps();
        });

        // initialize needed categories
        DB::table('currencies')->insert([
                ['name' => 'ARS', 'value' => 1],
                ['name' => 'USD', 'value' => 70]
        ]);

        DB::table('event_types')->insert([
            ['name' => 'CLASIFICADO', 'charge_group' => 'MARKETPLACE'],
            ['name' => 'VENTA', 'charge_group' => 'MARKETPLACE'],
            ['name' => 'ENVÍO', 'charge_group' => 'MARKETPLACE'],
            ['name' => 'CRÉDITO', 'charge_group' => 'SERVICIOS'],
            ['name' => 'FIDELIDAD', 'charge_group' => 'SERVICIOS'],
            ['name' => 'PUBLICIDAD', 'charge_group' => 'SERVICIOS'],
            ['name' => 'MERCADOPAGO', 'charge_group' => 'EXTERNO'],
            ['name' => 'MERCADOSHOP', 'charge_group' => 'EXTERNO']
        ]);
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('events', function($table) {
            $table->dropForeign('events_user_id_foreign');
            $table->dropIndex('events_user_id_index');
            $table->dropForeign('events_currency_id_foreign');
            $table->dropIndex('events_currency_id_index');
            $table->dropForeign('events_type_id_foreign');
            $table->dropIndex('events_type_id_index');
        });

        Schema::table('invoices', function($table) {
            $table->dropForeign('invoices_user_id_foreign');
            $table->dropIndex('invoices_user_id_index');
        });

        Schema::table('charges', function($table) {
            $table->dropForeign('charges_event_id_foreign');
            $table->dropIndex('charges_event_id_index');
            $table->dropForeign('charges_user_id_foreign');
            $table->dropIndex('charges_user_id_index');
            $table->dropForeign('charges_invoice_id_foreign');
            $table->dropIndex('charges_invoice_id_index');
        });

        Schema::table('payments', function($table) {
            $table->dropForeign('payments_user_id_foreign');
            $table->dropIndex('payments_user_id_index');
        });

        Schema::table('payment_charges', function($table) {
            $table->dropForeign('payment_charges_payment_id_foreign');
            $table->dropIndex('payment_charges_payment_id_index');
            $table->dropForeign('payment_charges_charge_id_foreign');
            $table->dropIndex('payment_charges_charge_id_index');
        });

        Schema::table('user_finantial_statuses', function($table) {
            $table->dropForeign('user_finantial_statuses_user_id_foreign');
            $table->dropIndex('user_finantial_statuses_user_id_index');
        });

        Schema::dropIfExists('events');
        Schema::dropIfExists('currencies');
        Schema::dropIfExists('event_types');
        Schema::dropIfExists('invoices');
        Schema::dropIfExists('charges');
        Schema::dropIfExists('payments');
        Schema::dropIfExists('payment_charges');
        Schema::dropIfExists('user_finantial_statuses');
    }
}
