<?php

use Illuminate\Database\Seeder;

class KaryawansSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run()
    {
        DB::table('karyawans')->truncate();

        $records = [
        [
            'status_karyawan_id' => 1,
            'nik' => 000001,
            'nama' => 'Deniel',
            'alamat' => 'Jl. Buah Batu no.12 Bandung',
            'phone' => '081212345678',
            'lulusan' => 'S1',
            'tgl_masuk' => Date('Y-m-d H:i:s'),
            'nilai_upah' => 1000000,
            'uang_makan' => 10000,
            'uang_lembur' => 20000,
            'norek' => '0851169317',
            'tunjangan' => 1000000,
        ],

        [
            'status_karyawan_id' => 2,
            'nik' => 000002,
            'nama' => 'Jerry',
            'alamat' => 'Jl. Buah Batu no.12 Bandung',
            'phone' => '081212345678',
            'lulusan' => 'S1',
            'tgl_masuk' => Date('Y-m-d H:i:s'),
            'nilai_upah' => 1000000,
            'uang_makan' => 10000,
            'uang_lembur' => 20000,
            'norek' => '0851169317',
            'tunjangan' => 1000000,
        ],

        [
            'status_karyawan_id' => 3,
            'nik' => 000003,
            'nama' => 'Akrian',
            'alamat' => 'Jl. Buah Batu no.12 Bandung',
            'phone' => '081212345678',
            'lulusan' => 'S1',
            'tgl_masuk' => Date('Y-m-d H:i:s'),
            'nilai_upah' => 1000000,
            'uang_makan' => 10000,
            'uang_lembur' => 20000,
            'norek' => '0851169317',
            'tunjangan' => 1000000,
        ],

        ];

        foreach ($records as $record) {
            DB::table('karyawans')->insert($record);
        }
    }
}
