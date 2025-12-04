<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // === BAGIAN 1: MASTER DATA (Independen) ===

        // 1. akademik
        Schema::create('akademik', function (Blueprint $table) {
            $table->id();
            $table->string('nama');
            $table->string('kode')->unique();
            $table->timestamps();
        });

        // 2. nonakademik
        Schema::create('nonakademik', function (Blueprint $table) {
            $table->id();
            $table->string('nama');
            $table->string('kode')->unique();
            $table->timestamps();
        });

        // 3. siswa
        Schema::create('siswa', function (Blueprint $table) {
            $table->id();
            $table->string('kode')->unique();
            $table->string('nama');
            $table->timestamps();
        });

        // 4. kelas
        Schema::create('kelas', function (Blueprint $table) {
            $table->id();
            $table->string('nama');
            $table->string('sub')->nullable();
            $table->timestamps();
        });


        // 5. semester (Sekarang mandiri, memuat data tahun ajaran)
        Schema::create('semester', function (Blueprint $table) {
            $table->id();
            $table->string('nama');
            $table->year('tahun_mulai');
            $table->year('tahun_selesai');
            $table->enum('status', ['aktif', 'nonaktif'])->default('nonaktif');
            $table->timestamps();
        });

        // 6. kriteria
        Schema::create('kriteria', function (Blueprint $table) {
            $table->id();
            $table->string('nama_kriteria');
            $table->integer('prioritas')->nullable();
            $table->decimal('bobot', 5, 4)->default(0); // Presisi bobot ditingkatkan
            $table->timestamps();
        });

        // === BAGIAN 2: DATA PROSES (INPUT) ===

        // 7. data_siswa_kelas
        Schema::create('data_siswa_kelas', function (Blueprint $table) {
            $table->id();
            $table->foreignId('id_siswa')->constrained('siswa')->onDelete('cascade');
            $table->foreignId('id_kelas')->constrained('kelas')->onDelete('cascade');
            $table->foreignId('id_semester')->constrained('semester')->onDelete('cascade');
            $table->timestamps();
        });

        // 8. data_nilai_kriteria
        Schema::create('data_nilai_kriteria', function (Blueprint $table) {
            $table->id();
            $table->foreignId('id_data_siswa_kelas')->constrained('data_siswa_kelas')->onDelete('cascade');
            $table->foreignId('id_kriteria')->constrained('kriteria')->onDelete('cascade');
            $table->decimal('nilai', 10, 5);
            $table->timestamps();
            $table->unique(['id_data_siswa_kelas', 'id_kriteria']);
        });

        // === BAGIAN 3: DATA HASIL (OUTPUT) ===

        // 9. ranking
        Schema::create('ranking', function (Blueprint $table) {
            $table->id();
            $table->foreignId('id_data_siswa_kelas')->constrained('data_siswa_kelas')->onDelete('cascade');
            $table->string('metode', 50);
            $table->float('hasil_alternatif')->default(0);
            $table->integer('ranking')->default(0);
            $table->timestamps();
        });

        // 10. analisis_perbandingan
        Schema::create('analisis_perbandingan', function (Blueprint $table) {
            $table->id();
            $table->foreignId('id_semester')->constrained('semester');
            $table->foreignId('id_kelas')->nullable()->constrained('kelas')->onDelete('set null');
            $table->string('metode', 50);
            $table->decimal('waktu_tahap_1', 10, 4)->nullable();
            $table->decimal('waktu_tahap_2', 10, 4)->nullable();
            $table->decimal('waktu_tahap_3', 10, 4)->nullable();
            $table->decimal('waktu_tahap_4', 10, 4)->nullable();
            $table->decimal('waktu_tahap_5', 10, 4)->nullable();
            $table->decimal('waktu_total', 10, 4);
            $table->decimal('spearman_rho', 8, 5)->nullable();
            $table->timestamps();
        });

        // === BAGIAN 4: AUTENTIKASI & USER ===

        // 11. users (Ditambah Role nanti di tahap 2, sementara standar dulu)
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('email')->unique();
            $table->timestamp('email_verified_at')->nullable();
            $table->string('password');
            $table->enum('role', ['admin', 'guru'])->default('guru');
            $table->rememberToken();
            $table->timestamps();
        });

        Schema::create('password_reset_tokens', function (Blueprint $table) {
            $table->string('email')->primary();
            $table->string('token');
            $table->timestamp('created_at')->nullable();
        });

        Schema::create('cache', function (Blueprint $table) {
            $table->string('key')->primary();
            $table->mediumText('value');
            $table->integer('expiration');
        });

        Schema::create('cache_locks', function (Blueprint $table) {
            $table->string('key')->primary();
            $table->string('owner');
            $table->integer('expiration');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('cache_locks');
        Schema::dropIfExists('cache');
        Schema::dropIfExists('password_reset_tokens');
        Schema::dropIfExists('users');
        Schema::dropIfExists('analisis_perbandingan');
        Schema::dropIfExists('ranking');
        Schema::dropIfExists('data_nilai_kriteria');
        Schema::dropIfExists('data_siswa_kelas');
        Schema::dropIfExists('semester'); // Hapus semester

        Schema::dropIfExists('kriteria');
        Schema::dropIfExists('kelas');
        Schema::dropIfExists('siswa');
        Schema::dropIfExists('nonakademik');
        Schema::dropIfExists('akademik');
    }
};
