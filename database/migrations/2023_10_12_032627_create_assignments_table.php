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
            $table->integer('identity_number');
            $table->foreignId('user_id')->nullable(); // data dari pegawai
            $table->foreignId('ppk')->nullable(); // data dari user hanya dengan role ppk
            $table->foreignId('head_officer')->nullable();
            // $table->foreignId('unit_id');//UMUM, P2, PERBEN, PKC, KIP
            $table->string('unit')->nullable();
            $table->string('ndreq_st')->nullable();
            $table->string('no_st')->nullable();
            $table->string('nomor_st')->nullable();
            $table->date('date_st')->nullable();
            $table->string('no_spd')->nullable();
            $table->date('date_spd')->nullable();
            $table->date('departure_date')->nullable();
            $table->date('return_date')->nullable();
            $table->string('dipa_search')->nullable();//Kantor, Kantor lain
            $table->enum('tagging_status', ['canceled', 'online'])->nullable();
            $table->enum('plt', ['plh', 'kosong'])->nullable();
            $table->string('plh');
            //===============
            $table->string('disbursement')->nullable();
            $table->string('no_spyt')->nullable();
            $table->string('implementation_tasks')->nullable();
            $table->string('business_trip_reason')->nullable();
            $table->string('destination_office')->nullable();
            $table->string('city_origin')->nullable();
            $table->string('destination_city_1')->nullable();
            $table->string('destination_city_2')->nullable();
            $table->string('destination_city_3')->nullable();
            $table->string('destination_city_4')->nullable();
            $table->string('destination_city_5')->nullable();
            $table->string('transportation')->nullable();//Kendaraan umum, Kendaraan dinas
            $table->string('signature')->nullable();
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
