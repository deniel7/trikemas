<?php

use Illuminate\Database\Seeder;

class RoleTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run()
    {
        DB::table('roles')->truncate();

        DB::table('roles')->insert([
            'name' => 'administrator',
            'display_name' => 'Administrator',
            'description' => 'Administrator get full access.',
            'created_by' => 1,
            'updated_by' => 1,
            'created_at' => Date('Y-m-d H:i:s'),
            'updated_at' => Date('Y-m-d H:i:s'),
        ]);
    }
}
