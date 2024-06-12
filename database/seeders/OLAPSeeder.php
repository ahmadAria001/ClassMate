<?php

namespace Database\Seeders;

use App\Models\Civilian;
use App\Models\Dues;
use App\Models\DuesMember;
use App\Models\DuesPaymentLog;
use App\Models\RT;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;

class OLAPSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        if (!RT::first()) { 
            $path = database_path('seeders\SQL\RT.sql'); // Adjust the path as needed
            $query = File::get($path);
            DB::unprepared($query);
        }
        if (!Civilian::first()) { 
            $path = database_path('seeders\SQL\CIVILIAN.sql');
            $query = File::get($path);
            DB::unprepared($query);
        }
        if (!Dues::first()) { 
            $path = database_path('seeders\SQL\DUES.sql'); // Adjust the path as needed
            $query = File::get($path);
            DB::unprepared($query);
        }
        if(!DuesMember::first()){
            $path = database_path('seeders\SQL\DUES MEMBER.sql');
            $query = File::get($path);
            DB::unprepared($query);
        }

        if(!DuesPaymentLog::first()){
            $path = database_path('seeders\SQL\DUESPAYMENT.sql');
            $query = File::get($path);
            DB::unprepared($query);
        }
    }
}
