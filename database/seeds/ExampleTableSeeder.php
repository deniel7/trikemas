<?php

use Illuminate\Database\Seeder;

class ExampleTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
    	DB::table('examples')->delete();

    	factory(App\Example::class, 50)->create();
    }
}
