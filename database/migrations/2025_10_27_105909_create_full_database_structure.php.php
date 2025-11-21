    <?php

    use Illuminate\Database\Migrations\Migration;
    use Illuminate\Database\Schema\Blueprint;
    use Illuminate\Support\Facades\Schema;

    return new class extends Migration
    {
        /**
         * Run the migrations.
         * * Urutan 'up' ini SANGAT PENTING untuk foreign key constraints.
         */
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

            // 5. tahun_ajaran
            Schema::create('tahun_ajaran', function (Blueprint $table) {
                $table->id();
                $table->year('tahun_mulai');
                $table->year('tahun_selesai');
                $table->enum('status', ['aktif', 'nonaktif'])->default('aktif');
                $table->timestamps();
            });

            // 7. kriteria
            Schema::create('kriteria', function (Blueprint $table) {
                $table->id();
                $table->string('nama_kriteria');
                $table->double('bobot')->default(0);
                $table->timestamps();
            });

            // === BAGIAN 2: DATA RELASI (Dependen) ===

            // 6. semester (Tergantung 'tahun_ajaran')
            Schema::create('semester', function (Blueprint $table) {
                $table->id();
                $table->string('nama');
                $table->foreignId('id_tahun_ajaran')->constrained('tahun_ajaran')->onDelete('cascade');
                $table->timestamps();
            });

            // 8. data_siswa_kelas (Jantung/Pivot)
            // (Tergantung 'siswa', 'kelas', 'semester')
            Schema::create('data_siswa_kelas', function (Blueprint $table) {
                $table->id();
                $table->foreignId('id_siswa')->constrained('siswa')->onDelete('cascade');
                $table->foreignId('id_kelas')->constrained('kelas')->onDelete('cascade');
                $table->foreignId('id_semester')->constrained('semester')->onDelete('cascade');
                $table->timestamps();
            });

            // 9. data_nilai_kriteria (Wadah Nilai Import Excel)
            // (Tergantung 'data_siswa_kelas', 'kriteria')
            Schema::create('data_nilai_kriteria', function (Blueprint $table) {
                $table->id();
                $table->foreignId('id_data_siswa_kelas')->constrained('data_siswa_kelas')->onDelete('cascade');
                $table->foreignId('id_kriteria')->constrained('kriteria')->onDelete('cascade');
                $table->decimal('nilai', 10, 5); // Presisi tinggi untuk nilai Excel
                $table->timestamps();
                $table->unique(['id_data_siswa_kelas', 'id_kriteria']);
            });

            // === BAGIAN 3: DATA HASIL (OUTPUT) ===

            // 10. ranking (Hasil per siswa)
            // (Tergantung 'data_siswa_kelas')
            Schema::create('ranking', function (Blueprint $table) {
                $table->id();
                $table->foreignId('id_data_siswa_kelas')->constrained('data_siswa_kelas')->onDelete('cascade');
                $table->string('metode', 50);
                $table->float('hasil_alternatif')->default(0);
                $table->integer('ranking')->default(0);
                $table->timestamps();
            });

            // 11. analisis_perbandingan (Hasil Statistik)
            // (Tergantung 'semester', 'kelas')
            Schema::create('analisis_perbandingan', function (Blueprint $table) {
                $table->id();
                $table->foreignId('id_semester')->constrained('semester');
                $table->foreignId('id_kelas')->nullable()->constrained('kelas')->onDelete('set null'); // <-- id_kelas sudah digabung
                $table->string('metode', 50);
                $table->decimal('waktu_tahap_1', 10, 4)->nullable();
                $table->decimal('waktu_tahap_2', 10, 4)->nullable();
                $table->decimal('waktu_tahap_3', 10, 4)->nullable();
                $table->decimal('waktu_tahap_4', 10, 4)->nullable();
                $table->decimal('waktu_tahap_5', 10, 4)->nullable(); // Untuk Borda
                $table->decimal('waktu_total', 10, 4);
                $table->decimal('spearman_rho', 8, 5)->nullable();
                $table->timestamps();
            });

            // === BAGIAN 4: AUTENTIKASI (BREEZE) ===

            // 12. users
            Schema::create('users', function (Blueprint $table) {
                $table->id();
                $table->string('name');
                $table->string('email')->unique();
                $table->timestamp('email_verified_at')->nullable();
                $table->string('password');
                $table->rememberToken();
                $table->timestamps();
            });

            // 13. password_reset_tokens
            Schema::create('password_reset_tokens', function (Blueprint $table) {
                $table->string('email')->primary();
                $table->string('token');
                $table->timestamp('created_at')->nullable();
            });

            // === BAGIAN 5: CACHE (LOGIN RATE LIMIT) ===

            // 14. cache
            Schema::create('cache', function (Blueprint $table) {
                $table->string('key')->primary();
                $table->mediumText('value');
                $table->integer('expiration');
            });

            // 15. cache_locks
            Schema::create('cache_locks', function (Blueprint $table) {
                $table->string('key')->primary();
                $table->string('owner');
                $table->integer('expiration');
            });
        }

        /**
         * Reverse the migrations.
         * * Urutan 'down' adalah KEBALIKAN PERSIS dari 'up'.
         */
        public function down(): void
        {
            // Hapus Bagian 5
            Schema::dropIfExists('cache_locks');
            Schema::dropIfExists('cache');

            // Hapus Bagian 4
            Schema::dropIfExists('password_reset_tokens');
            Schema::dropIfExists('users');

            // Hapus Bagian 3
            Schema::dropIfExists('analisis_perbandingan');
            Schema::dropIfExists('ranking');

            // Hapus Bagian 2
            Schema::dropIfExists('data_nilai_kriteria');
            Schema::dropIfExists('data_siswa_kelas');
            Schema::dropIfExists('semester');

            // Hapus Bagian 1
            Schema::dropIfExists('kriteria');
            Schema::dropIfExists('tahun_ajaran');
            Schema::dropIfExists('kelas');
            Schema::dropIfExists('siswa');
            Schema::dropIfExists('nonakademik');
            Schema::dropIfExists('akademik');
        }
    };
