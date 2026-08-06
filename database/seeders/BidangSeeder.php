<?php

namespace Database\Seeders;

use App\Models\Bidang;
use Illuminate\Database\Seeder;

class BidangSeeder extends Seeder
{
    public function run(): void
    {
        $data = [
            ['nama' => 'Perencanaan dan Keuangan', 'kode' => 'perencanaan', 'kelompok' => 'Sekretariat'],
            ['nama' => 'Umum dan Kepegawaian', 'kode' => 'kepegawaian', 'kelompok' => 'Sekretariat'],
            ['nama' => 'Rehabilitasi Sosial', 'kode' => 'rehabilitasi', 'kelompok' => 'Bidang Sosial'],
            ['nama' => 'Perlindungan dan Jaminan Sosial', 'kode' => 'perlindungan', 'kelompok' => 'Bidang Sosial'],
            ['nama' => 'Pemberdayaan Sosial', 'kode' => 'pemberdayaan-sosial', 'kelompok' => 'Bidang Sosial'],
            ['nama' => 'Pemberdayaan Masyarakat', 'kode' => 'pemberdayaan-masyarakat', 'kelompok' => 'Bidang PM'],
        ];

        foreach ($data as $row) {
            Bidang::updateOrCreate(['kode' => $row['kode']], $row);
        }
    }
}
