<?php

use Illuminate\Database\Seeder;

class StatusKaryawanSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run()
    {
        DB::table('status_karyawans')->truncate();

        $records = [
        [
            'keterangan' => 'Karyawan Kontrak',
            'created_at' => Date('Y-m-d H:i:s'),
            'updated_at' => Date('Y-m-d H:i:s'),
        ],

        [
            'keterangan' => 'Karyawan Harian',
            'created_at' => Date('Y-m-d H:i:s'),
            'updated_at' => Date('Y-m-d H:i:s'),
        ],

        [
            'keterangan' => 'Karyawan Staff',
            'created_at' => Date('Y-m-d H:i:s'),
            'updated_at' => Date('Y-m-d H:i:s'),
        ],

        ];

        foreach ($records as $record) {
            DB::table('status_karyawans')->insert($record);
        }
    }
}
