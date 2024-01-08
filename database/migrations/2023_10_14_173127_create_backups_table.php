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
            $table->integer('identity_number');
            $table->string('user_id')->nullable();
            $table->string('ppk')->nullable();
            $table->string('head_officer')->nullable();
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
            $table->enum('tagging_status', ['default', 'canceled', 'online'])->nullable();
            $table->enum('plt', ['plh', 'kosong'])->nullable();
            $table->string('plh')->nullable();
            //===============
            $table->string('disbursement')->nullable();
            $table->string('no_spyt')->nullable();
            $table->longText('implementation_tasks')->nullable();
            $table->longText('business_trip_reason')->nullable();
            $table->string('destination_office')->nullable();
            $table->string('city_origin')->nullable();
            $table->string('destination_city_1')->nullable();
            $table->string('destination_city_2')->nullable();
            $table->string('destination_city_3')->nullable();
            $table->string('destination_city_4')->nullable();
            $table->string('destination_city_5')->nullable();
            $table->string('transportation')->nullable();//Kendaraan umum, Kendaraan dinas
            $table->string('signature')->nullable();
            
            //tambahan dari assignment
            $table->string('jabPeg')->nullable();
            $table->string('pangkatPeg')->nullable();
            $table->string('golPeg')->nullable();
            $table->string('nip_peg')->nullable();
            $table->string('nip_ppk')->nullable();
            $table->string('employee')->nullable();
            
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
