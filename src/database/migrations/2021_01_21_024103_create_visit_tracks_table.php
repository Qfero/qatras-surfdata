<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateVisitTracksTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('visittracks', function (Blueprint $table) {
            $table->id();
            $table->string('visitor_ip', 20)->nullable();
            $table->string('visitor_country', 3)->nullable();
            $table->string('visitor_region', 50)->nullable();
            $table->string('visitor_city', 50)->nullable();
            $table->string('visitor_zip', 20)->nullable();
            $table->decimal('visitor_latitude', 11, 7)->nullable();
            $table->decimal('visitor_longitude', 11, 7)->nullable();
            $table->string('visitor_timezone', 50)->nullable();
            $table->string('visitor_isp', 50)->nullable();
            $table->string('visitor_org', 50)->nullable();
            $table->string('visitor_as', 50)->nullable();
            $table->string('visitor_userAgent', 1024)->nullable();
            $table->string('visitor_userAgentPattern', 256)->nullable();
            $table->enum('visitor_is_bot', array('Y', 'N'))->nullable();
            $table->string('visitor_browser', 50)->nullable();
            $table->string('visitor_browserVersion', 20)->nullable();
            $table->string('visitor_platform', 20)->nullable();
            $table->string('visitor_platformVersion', 20)->nullable();
            $table->dateTime('visitor_date')->nullable();
            $table->integer('visitor_day')->nullable();
            $table->integer('visitor_month')->nullable();
            $table->integer('visitor_year')->nullable();
            $table->integer('visitor_hour')->nullable();
            $table->integer('visitor_minute')->nullable();
            $table->integer('visitor_seconds')->nullable();
            $table->string('visitor_referer', 256)->nullable();
            $table->string('visitor_page', 256)->nullable();
            $table->enum('visitor_closed', array('Y', 'N'))->nullable();
        });

    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('visittracks');
    }
}
