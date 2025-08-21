<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('trackings', function (Blueprint $table) {
            $table->id();
            $table->string('tracking_number')->unique();
            $table->string('carrier');
            $table->tinyInteger('country_type'); // 1=USA, 2=Canada
            $table->string('origin_city')->nullable();
            $table->string('origin_state', 2);
            $table->string('origin_zip')->nullable();
            $table->string('destination_city')->nullable();
            $table->string('destination_state', 2);
            $table->string('destination_zip')->nullable();
            $table->date('start_date');
            $table->date('expected_delivery_date');
            $table->date('delayed_until')->nullable();
            $table->string('delay_reason')->nullable();
            $table->string('status')->default('created');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('trackings');
    }
};