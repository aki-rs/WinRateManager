<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;

class stagesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run()
    {
        $path = database_path('seeders/data/stages.csv');
        $file = fopen($path, 'r');
        $header = fgetcsv($file); // ヘッダーをスキップ

        while (($row = fgetcsv($file)) !== FALSE) {
            DB::table('stages')->insert([
                'id' => $row[0],
                'name' => $row[1],
            ]);
        }

        fclose($file);
    }
}
