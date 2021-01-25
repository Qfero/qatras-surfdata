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
            $table->string('visitor_ip', 512)->nullable();
            $table->string('visitor_country', 512)->nullable();
            $table->string('visitor_region', 512)->nullable();
            $table->string('visitor_city', 512)->nullable();
            $table->string('visitor_zip', 512)->nullable();
            $table->string('visitor_latitude', 512)->nullable();
            $table->string('visitor_longitude', 512)->nullable();
            $table->string('visitor_timezone', 512)->nullable();
            $table->string('visitor_isp', 512)->nullable();
            $table->string('visitor_org', 512)->nullable();
            $table->string('visitor_as', 512)->nullable();
            $table->string('visitor_userAgent', 512)->nullable();
            $table->string('visitor_userAgentPattern', 512)->nullable();
            $table->string('visitor_browser', 512)->nullable();
            $table->string('visitor_browserVersion', 512)->nullable();
            $table->string('visitor_platform', 512)->nullable();
            $table->string('visitor_platformVersion', 512)->nullable();
            $table->string('visitor_date', 512)->nullable();
            $table->string('visitor_day', 512)->nullable();
            $table->string('visitor_month', 512)->nullable();
            $table->string('visitor_year', 512)->nullable();
            $table->string('visitor_hour', 512)->nullable();
            $table->string('visitor_minute', 512)->nullable();
            $table->string('visitor_seconds', 512)->nullable();
            $table->string('visitor_referer', 512)->nullable();
            $table->string('visitor_page', 512)->nullable();
            $table->string('visitor_closed', 512)->nullable();
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
