<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class Add2faToUsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->boolean('two_factor_state')->default(false)->after('is_activate');
            $table->string('two_factor_method', 20)->nullable()->after('two_factor_state');
            $table->string('two_factor_code', 6)->nullable()->after('two_factor_method');
            $table->dateTime('two_factor_expires_at')->nullable()->after('two_factor_code');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('two_factor_state', 'two_factor_method', 'two_factor_code', 'two_factor_expires_at');
        });
    }
}
