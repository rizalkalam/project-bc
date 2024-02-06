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
            $table->enum('tagging_status', ['default', 'canceled', 'online'])->nullable();
            $table->enum('plt', ['plh', 'kosong'])->nullable();
            $table->string('plh');
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

            //untuk memberikan tanda kalau user_id sdh kosong
            $table->enum('employee_status', ['core', 'blank'])->default('core')->nullable();

            //untuk memberikan tanda kalau surat sudah ada pada data inti
            $table->enum('availability_status', ['available', 'not_yet'])->default('available')->nullable();

            //untuk memberikan tanda kalau ppk_id sdh kosong
            $table->enum('ppk_status', ['active', 'non-active'])->default('active')->nullable();

            //untuk memberikan tanda kalau head_officer_id sdh kosong
            $table->enum('head_officer_status', ['active', 'non-active'])->default('active')->nullable();

            //untuk mengatasi id user/pegawai sudah tidak tersedia
            $table->string('jabPeg')->nullable();
            $table->string('pangkatPeg')->nullable();
            $table->string('golPeg')->nullable();
            $table->string('nip_peg')->nullable();
            $table->string('nip_ppk')->nullable();
            $table->string('employee')->nullable();
            $table->string('nama_pej')->nullable();
            $table->string('nama_ppk')->nullable();

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
