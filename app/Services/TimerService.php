<?php

namespace App\Services; // Namespace-nya App\Services

class TimerService
{
    /**
     * Menyimpan waktu mulai dari sebuah tahap.
     * @var float
     */
    private $start;

    /**
     * Array untuk menyimpan hasil timing (dalam milidetik).
     * @var array
     */
    public $timings = [];

    /**
     * Memulai pencatat waktu untuk satu tahap.
     */
    public function startStage(): void // Nama methodnya startStage()
    {
        $this->start = microtime(true);
    }

    /**
     * Menghentikan pencatat waktu dan menyimpan hasilnya.
     * @param string $key Nama tahap (e.g., 'tahap_1', 'tahap_2')
     */
    public function stopStage(string $key): void // Nama methodnya stopStage()
    {
        // Pengecekan agar tidak error jika startStage() belum dipanggil
        if ($this->start) {
            $this->timings[$key] = (microtime(true) - $this->start) * 1000; // Konversi ke milidetik
            $this->start = null; // Reset timer
        }
    }

    /**
     * Mendapatkan total waktu dari semua tahap.
     * @return float
     */
    public function getTotalTime(): float
    {
        return array_sum($this->timings);
    }
}
