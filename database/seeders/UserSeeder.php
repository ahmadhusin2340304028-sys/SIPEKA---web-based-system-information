<?php

namespace Database\Seeders;

use App\Models\Role;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Mengganti password plaintext ('dinsos123' langsung dibandingkan '===')
     * pada aplikasi lama dengan hashing bcrypt melalui Hash::make().
     */
    public function run(): void
    {
        $default = 'dinsos123';

        $users = [
            ['username' => 'admin', 'name' => 'Administrator', 'role_slug' => 'admin'],
            ['username' => 'kadis', 'name' => 'Kepala Dinas', 'role_slug' => 'kepala-dinas'],
            ['username' => 'staff perencanaan', 'name' => 'Staff Perencanaan', 'role_slug' => 'perencanaan-dan-keuangan'],
            ['username' => 'staff umum', 'name' => 'Staff Umum', 'role_slug' => 'umum-dan-kepegawaian'],
            ['username' => 'staff resos', 'name' => 'Staff Rehabilitasi Sosial', 'role_slug' => 'rehabilitasi-sosial'],
            ['username' => 'staff linjamsos', 'name' => 'Staff Linjamsos', 'role_slug' => 'perlindungan-dan-jaminan-sosial'],
            ['username' => 'staff dayasos', 'name' => 'Staff Dayasos', 'role_slug' => 'pemberdayaan-sosial'],
            ['username' => 'staff PM', 'name' => 'Staff Pemberdayaan Masyarakat', 'role_slug' => 'pemberdayaan-masyarakat'],
            ['username' => 'kabid sosial', 'name' => 'Kepala Bidang Sosial', 'role_slug' => 'kepala-bidang-sosial'],
            ['username' => 'kasubbag perencanaan', 'name' => 'Kasubbag Perencanaan', 'role_slug' => 'kepala-sub-bagian-perencanaan'],
            ['username' => 'kabid pm', 'name' => 'Kepala Bidang Pemberdayaan Masyarakat', 'role_slug' => 'kepala-bidang-pemberdayaan-masyarakat'],
            ['username' => 'kasubbag kepegawaian', 'name' => 'Kasubbag Kepegawaian', 'role_slug' => 'kepala-sub-bagian-kepegawaian'],
            ['username' => 'Sekretaris', 'name' => 'Sekretaris', 'role_slug' => 'sekretaris'],
        ];

        foreach ($users as $u) {
            $role = Role::where('slug', $u['role_slug'])->first();

            if (! $role) {
                continue;
            }

            User::updateOrCreate(
                ['username' => $u['username']],
                [
                    'name' => $u['name'],
                    'password' => Hash::make($default),
                    'role_id' => $role->id,
                ]
            );
        }
    }
}
