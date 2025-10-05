<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSkBatchProcessingTables extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // Create sk_batch_process table
        Schema::connection('mysql_queue')->create('sk_batch_process', function (Blueprint $table) {
            $table->id();
            $table->string('urut', 50);
            $table->year('tahun_sk');
            $table->integer('total_karyawan');
            $table->integer('processed_count')->default(0);
            $table->integer('success_count')->default(0);
            $table->integer('failed_count')->default(0);
            $table->enum('status', ['pending', 'processing', 'completed', 'failed'])->default('pending');
            $table->text('passphrase_encrypted')->nullable();
            $table->date('tgl_ttd')->nullable();
            $table->datetime('estimated_completion')->nullable();
            $table->datetime('started_at')->nullable();
            $table->datetime('completed_at')->nullable();
            $table->string('created_by', 50);
            $table->string('current_karyawan_id', 50)->nullable();
            $table->string('current_karyawan_name')->nullable();
            $table->timestamps();
            
            $table->index(['status']);
            $table->index(['urut', 'tahun_sk']);
        });

        // Create sk_tte_progress table
        Schema::connection('mysql_queue')->create('sk_tte_progress', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('batch_id');
            $table->string('kd_karyawan', 50);
            $table->string('karyawan_name');
            $table->enum('status', ['pending', 'processing', 'success', 'failed'])->default('pending');
            $table->text('error_message')->nullable();
            $table->string('id_dokumen', 100)->nullable();
            $table->string('path_dokumen', 500)->nullable();
            $table->datetime('processed_at')->nullable();
            $table->timestamps();
            
            $table->foreign('batch_id')->references('id')->on('sk_batch_process')->onDelete('cascade');
            $table->index(['batch_id', 'status']);
            $table->index(['kd_karyawan']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::connection('mysql_queue')->dropIfExists('sk_tte_progress');
        Schema::connection('mysql_queue')->dropIfExists('sk_batch_process');
    }
}
