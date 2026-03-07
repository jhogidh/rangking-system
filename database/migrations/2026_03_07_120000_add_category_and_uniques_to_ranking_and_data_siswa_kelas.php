<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasColumn('ranking', 'kategori')) {
            Schema::table('ranking', function (Blueprint $table) {
                $table->string('kategori', 30)->default('semua')->after('metode');
            });
        }

        DB::table('ranking')
            ->whereNull('kategori')
            ->orWhere('kategori', '')
            ->update(['kategori' => 'semua']);

        if (!$this->indexExists('ranking', 'ranking_data_metode_kategori_unique')) {
            Schema::table('ranking', function (Blueprint $table) {
                $table->unique(
                    ['id_data_siswa_kelas', 'metode', 'kategori'],
                    'ranking_data_metode_kategori_unique'
                );
            });
        }

        if (!$this->indexExists('data_siswa_kelas', 'data_siswa_kelas_unique_siswa_kelas_semester')) {
            Schema::table('data_siswa_kelas', function (Blueprint $table) {
                $table->unique(
                    ['id_siswa', 'id_kelas', 'id_semester'],
                    'data_siswa_kelas_unique_siswa_kelas_semester'
                );
            });
        }
    }

    public function down(): void
    {
        if ($this->indexExists('data_siswa_kelas', 'data_siswa_kelas_unique_siswa_kelas_semester')) {
            Schema::table('data_siswa_kelas', function (Blueprint $table) {
                $table->dropUnique('data_siswa_kelas_unique_siswa_kelas_semester');
            });
        }

        if ($this->indexExists('ranking', 'ranking_data_metode_kategori_unique')) {
            Schema::table('ranking', function (Blueprint $table) {
                $table->dropUnique('ranking_data_metode_kategori_unique');
            });
        }

        if (Schema::hasColumn('ranking', 'kategori')) {
            Schema::table('ranking', function (Blueprint $table) {
                $table->dropColumn('kategori');
            });
        }
    }

    private function indexExists(string $table, string $indexName): bool
    {
        $databaseName = DB::connection()->getDatabaseName();
        $rows = DB::select(
            'SELECT 1 FROM information_schema.statistics WHERE table_schema = ? AND table_name = ? AND index_name = ? LIMIT 1',
            [$databaseName, $table, $indexName]
        );

        return !empty($rows);
    }
};
