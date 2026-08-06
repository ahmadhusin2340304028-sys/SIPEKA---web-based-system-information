<?php

namespace Database\Seeders;

use App\Models\Bidang;
use App\Models\Role;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class RoleSeeder extends Seeder
{
    public function run(): void
    {
        // 6 role operasional, satu-satu terhubung ke bidangnya (dipakai untuk hak input realisasi)
        foreach (Bidang::all() as $bidang) {
            Role::updateOrCreate(
                ['slug' => Str::slug($bidang->nama)],
                ['nama' => $bidang->nama, 'bidang_id' => $bidang->id]
            );
        }

        // Role struktural / eksekutif (tidak input realisasi, hanya melihat)
        $struktural = [
            'Admin',
            'Kepala Dinas',
            'Kepala Bidang Sosial',
            'Kepala Bidang Pemberdayaan Masyarakat',
            'Kepala Sub Bagian Perencanaan',
            'Kepala Sub Bagian Kepegawaian',
            'Sekretaris',
        ];

        foreach ($struktural as $nama) {
            Role::updateOrCreate(
                ['slug' => Str::slug($nama)],
                ['nama' => $nama, 'bidang_id' => null]
            );
        }
    }
}
