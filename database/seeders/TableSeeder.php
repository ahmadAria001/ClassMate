<?php

namespace Database\Seeders;

use App\Models\Activity;
use App\Models\Certificate;
use App\Models\Civilian;
use App\Models\Complaint;
use App\Models\Docs;
use App\Models\Documentation;
use App\Models\Dues;
use App\Models\DuesPaymentLog;
use App\Models\EnvirontmentInfo;
use App\Models\Family;
use App\Models\FinancialAssistance;
use App\Models\RT;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Seeder;

class TableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $faker = \Faker\Factory::create();

        for ($i = 1; $i <= 5; $i++) {
            RT::create([
                'created_at' => Carbon::createFromDate($faker->dateTime())->getTimestamp(),
                'created_by' => $i,
            ]);
        }

        for ($i = 1; $i <= 5; $i++) {
            Family::create([
                'nkk' => $i,
                'rt_id' => $i,
                'created_at' => Carbon::createFromDate($faker->dateTime())->getTimestamp(),
                'created_by' => $i,
            ]);
        }


        for ($i = 1; $i <= 5; $i++) {
            Civilian::create([
                'fullName' => $faker->sentence(3),
                'birthplace' => $faker->city(),
                'birthdate' => Carbon::createFromDate($faker->dateTime())->getTimestamp(),
                'family_id' => $i,
                'created_at' => Carbon::createFromDate($faker->dateTime())->getTimestamp(),
                'created_by' => $i,
            ]);
        }

        for ($i = 1; $i <= 5; $i++) {
            User::create([
                'username' => $faker->word(),
                'role' => $faker->randomElement(['RT', 'Warga']),
                'civilian_id' => $i,
                'created_at' => Carbon::createFromDate($faker->dateTime())->getTimestamp(),
                'created_by' => $i,
            ]);
        }

        $duesType = ['Security', 'TrashManagement', 'Event'];

        for ($i = 1; $i <= 3; $i++) {
            Dues::create([
                'typeDues' => $duesType[$i - 1],
                'description' => $faker->sentence(),
                'amt_dues' => $faker->numberBetween(0, 5000),
                'amt_fund' => $faker->numberBetween(0, 5000000),
                'status' => $faker->boolean(),
                'rt_id' => $i,
                'created_at' => Carbon::createFromDate($faker->dateTime())->getTimestamp(),
                'created_by' => $i,
            ]);
        }


        for ($i = 1; $i <= 3; $i++) {
            DuesPaymentLog::create([
                'dues_id' => $i,
                'paid_by' => $i,
                'amount_paid' => $faker->numberBetween(0, 5000),
                'exchange' => 0,
                'debt' => 0,
                'created_at' => Carbon::createFromDate($faker->dateTime())->getTimestamp(),
                'created_by' => $i,
            ]);
        }

        for ($i = 1; $i <= 5; $i++) {
            EnvirontmentInfo::create([
                'env_condition' => $faker->sentence(),
                'desc' => $faker->sentences(2, true),
                'general_facility' => $faker->sentence(),
                'created_at' => Carbon::createFromDate($faker->dateTime())->getTimestamp(),
                'created_by' => $i,
            ]);
        }

        for ($i = 1; $i <= 5; $i++) {
            FinancialAssistance::create([
                'request_by' => $i,
                'tanggungan' => $faker->sentence(),
                'created_at' => Carbon::createFromDate($faker->dateTime())->getTimestamp(),
                'created_by' => $i,
            ]);
        }

        for ($i = 1; $i <= 5; $i++) {
            Certificate::create([
                'request_by' => $i,
                'desc' => $faker->sentence(),
                'created_at' => Carbon::createFromDate($faker->dateTime())->getTimestamp(),
                'created_by' => $i,
            ]);
        }

        for ($i = 1; $i <= 4; $i++) {
            $docsType = ['Complaint', 'Dues', 'Event', 'Administration'];

            Docs::create([
                'type' => $docsType[$i - 1],
                'description' => $faker->sentence(),
                'created_at' => Carbon::createFromDate($faker->dateTime())->getTimestamp(),
                'created_by' => $i,
            ]);
        }

        Activity::create([
            'docs_id' => 2,
            'name' => $faker->title(),
            'startDate' => Carbon::createFromDate($faker->dateTimeBetween())->getTimestamp(),
            'endDate' => Carbon::createFromDate($faker->dateTimeBetween())->getTimestamp(),
            'created_at' => Carbon::createFromDate($faker->dateTime())->getTimestamp(),
            'created_by' => 1,
        ]);

        Complaint::create([
            'docs_id' => 1,
            'created_at' => Carbon::createFromDate($faker->dateTime())->getTimestamp(),
            'created_by' => 1,
        ]);


        Documentation::create([
            'docs_id' => 4,
            'contentType' => 'Administration',
            'contentDesc' => $faker->sentence(6, true),
            'created_at' => Carbon::createFromDate($faker->dateTime())->getTimestamp(),
            'created_by' => 1,
        ]);
    }
}
