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
        Schema::create('backups', function (Blueprint $table) {
            $table->id();
            $table->string('user_id')->nullable();
            $table->string('input_name');
            // $table->foreignId('unit_id');//UMUM, P2, PERBEN, PKC, KIP
            $table->string('kk_name');
            $table->string('unit');
            $table->string('ndreq_st');
            $table->string('no_st');
            $table->string('nomor_st');
            $table->date('date_st');
            $table->string('no_spd');
            $table->date('date_spd');
            $table->date('departure_date');
            $table->date('return_date');
            $table->string('dipa_search');//Kantor, Kantor lain
            $table->enum('tagging_status', ['canceled', 'online'])->nullable();
            $table->enum('plt', ['plh', 'kosong']);
            //===============
            $table->string('disbursement');
            $table->string('no_spyt')->nullable();
            $table->string('implementation_tasks');
            $table->string('business_trip_reason');
            $table->string('destination_office');
            $table->string('city_origin');
            $table->string('destination_city_1');
            $table->string('destination_city_2')->nullable();
            $table->string('destination_city_3')->nullable();
            $table->string('transportation');//Kendaraan umum, Kendaraan dinas
            $table->string('signature');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('backups');
    }
};
