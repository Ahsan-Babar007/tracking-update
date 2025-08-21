<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('tracking_events', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tracking_id')->constrained()->cascadeOnDelete();
            $table->dateTime('event_date');
            $table->string('location_city');
            $table->string('location_state', 2);
            $table->string('status');  // e.g., 'in_transit'
            $table->string('message');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('tracking_events');
    }
};