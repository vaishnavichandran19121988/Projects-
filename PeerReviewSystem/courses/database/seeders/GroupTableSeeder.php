<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class GroupTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // Insert some sample groups into the 'groups' table
        DB::table('groups')->insert([
            ['name' => 'Group A', 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Group B', 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Group C', 'created_at' => now(), 'updated_at' => now()],
        ]);
    }
}
