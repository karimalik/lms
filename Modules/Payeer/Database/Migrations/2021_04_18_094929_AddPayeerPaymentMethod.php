<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

class AddPayeerPaymentMethod extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::table('payment_methods')->insert([[
            'method' => 'Payeer',
            'type' => 'System',
            'active_status' => 0,
            'module_status' => 1,
            'logo' => 'public/demo/gateway/payeer.png',
            'created_at' => now(),
            'updated_at' => now(),
        ],
        ]);
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        //
    }
}
