<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateDomainsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('domains', function (Blueprint $table) {
            $table->id();
            $table->string('name')->unique()->comment('域名');
            $table->string('owner')->nullable()->comment('所有者');
            $table->string('registrar')->comment('注册服务商');
            $table->string('whois_server')->nullable()->comment('whois 服务器');
            $table->json('states')->comment('域名状态');
            $table->json('name_servers')->nullable()->comment('DNS服务器');
            $table->timestamp('creation_date')->nullable();
            $table->timestamp('expiration_date')->nullable();
            $table->mediumText('raw_data')->comment('Raw Data');
            $table->timestamp('updated_at')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('domains');
    }
}
