<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('assignments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('employee_id')->nullable();
            $table->string('unit');
            $table->string('ndreq_st');
            $table->string('no_st');
            $table->date('date_st');
            $table->string('no_spd');
            $table->date('date_spd');
            $table->date('departure_date');
            $table->date('return_date');
            $table->string('dipa_search');
            $table->string('implementation_tasks');
            $table->string('business_trip_reason');
            $table->string('destionation_office');
            $table->string('city_origin');
            $table->string('destination_city_1');
            $table->string('destination_city_2')->nullable();
            $table->string('destination_city_3')->nullable();
            $table->string('transport');
            $table->string('signature');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('assignments');
    }
};
