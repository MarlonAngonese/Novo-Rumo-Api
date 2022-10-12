<?php

namespace Database\Seeders;

use App\Models\Garbage;
use Illuminate\Database\Seeder;

class GarbageSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Garbage::factory(2)->create();
    }
}
