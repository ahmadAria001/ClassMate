<?php

namespace Database\Seeders;

use App\Models\Activity;
use App\Models\Certificate;
use App\Models\Civilian;
use App\Models\Complaint;
use App\Models\DocRequest;
use App\Models\Docs;
use App\Models\Documentation;
use App\Models\Dues;
use App\Models\DuesMember;
use App\Models\DuesPaymentLog;
use App\Models\FinancialAssistance;
use App\Models\News;
use App\Models\RT;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class TableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $faker = \Faker\Factory::create();

        for ($i = 1; $i <= 6; $i++) {
            RT::create([
                'number' => $i,
                'created_at' => Carbon::createFromDate($faker->dateTime())->getTimestamp(),
                'created_by' => $i,
            ]);
        }

        $createdCivilian = [];

        for ($i = 1; $i <= 6; $i++) {
            if ($i < 6) {
                array_push(
                    $createdCivilian,
                    Civilian::firstOrCreate([
                        'nik' => $i,
                        'fullName' => $faker->sentence(3),
                        'residentstatus' => $faker->randomElement(['ContractResident', 'PermanentResident']),
                        'birthplace' => $faker->city(),
                        'birthdate' => Carbon::createFromDate($faker->dateTime())->getTimestamp(),
                        'nkk' => $i,
                        'isFamilyHead' => true,
                        'rt_id' => $i,
                        'address' => $faker->address(),
                        'status' => $faker->randomElement(['Aktif', 'Meninggal', 'Pindah']),
                        'phone' => preg_replace('/[^0-9]/', ' ', $faker->phoneNumber()),
                        'religion' => $faker->randomElement(['Islam', 'Katolik', 'Hindu', 'Budha', 'Konghuan']),
                        'job' => $faker->randomElement(['Pengangguran', 'Mahasiswa', 'Petani', 'PNS', 'Sum Ting']),
                        'created_at' => Carbon::createFromDate($faker->dateTime())->getTimestamp(),
                        'created_by' => $i,
                    ])
                );
            } else {
                array_push(
                    $createdCivilian,
                    Civilian::firstOrCreate([
                        'nik' => 6,
                        'fullName' => 'admin',
                        'residentstatus' => 'PermanentResident',
                        'birthplace' => 'MLG',
                        'birthdate' => Carbon::createFromDate($faker->dateTime())->getTimestamp(),
                        'nkk' => $i,
                        'isFamilyHead' => true,
                        'rt_id' => $i,
                        'address' => $faker->address(),
                        'status' => $faker->randomElement(['Aktif', 'Meninggal', 'Pindah']),
                        'phone' => preg_replace('/[^0-9]/', ' ', $faker->phoneNumber()),
                        'religion' => $faker->randomElement(['Islam', 'Katolik', 'Hindu', 'Budha', 'Konghuan']),
                        'job' => $faker->randomElement(['Pengangguran', 'Mahasiswa', 'Petani', 'PNS', 'Sum Ting']),

                        'created_at' => Carbon::createFromDate($faker->dateTime())->getTimestamp(),
                        'created_by' => 1,
                    ])
                );
            }
        }

        for ($i = 1; $i <= 6; $i++) {
            if ($i < 6) {
                User::create([
                    'username' => $faker->word(),
                    'role' => $faker->randomElement(['RT', 'Warga']),
                    'password' => Hash::make('123'),
                    'civilian_id' => $i,
                    'created_at' => Carbon::createFromDate($faker->dateTime())->getTimestamp(),
                    'created_by' => $i,
                ]);
            } else {
                User::create([
                    'username' => 'admin',
                    'role' => 'Admin',
                    'password' => Hash::make('123'),
                    'civilian_id' => 6,
                    'created_at' => Carbon::createFromDate($faker->dateTime())->getTimestamp(),
                    'created_by' => 1,
                ]);
            }
        }

        $duesType = ['Security', 'TrashManagement', 'Event'];

        $duesTypeIndex = 0;
        $currentRT = 1;

        $createdDues = array();

        for ($i = 1; $i <= 6; $i++) {
            $createdDues["$i"] = array();
            if ($currentRT > 6) $currentRT = 1;

            foreach ($duesType as $key => $value) {
                array_push($createdDues["$i"], Dues::firstOrCreate([
                    'typeDues' => $value,
                    'description' => $faker->sentence(),
                    'amt_dues' => $faker->numberBetween(0, 5000),
                    'amt_fund' => $faker->numberBetween(0, 5000000),
                    'status' => $faker->boolean(),
                    'created_by' => $currentRT,
                    'rt_id' => $currentRT,
                    'created_at' => Carbon::createFromDate($faker->dateTime())->getTimestamp(),
                ]));
            }

            $currentRT++;
        }

        $duesTypeIndex = 1;
        $memberCreated = array();
        for ($i = 0; $i < count($createdCivilian); $i++) {

            for ($idx = 0; $idx < count($createdDues[($createdCivilian[$i])->rt_id]); $idx++) {
                if ($duesTypeIndex > 3) $duesTypeIndex = 0;

                array_push($memberCreated, DuesMember::firstOrCreate([
                    'dues' => ($createdDues[($createdCivilian[$i])->rt_id])[$idx]->id,
                    'member' => $createdCivilian[$i]->id,
                    'created_at' => Carbon::createFromDate($faker->dateTime())->getTimestamp(),
                    'created_by' => 6,
                ]));
            }
        }

        $paidBy = 1;
        $dues = 1;

        $existingDate = array();

        for ($i = 0; $i < count($memberCreated); $i++) {
            $existingDate[$memberCreated[$i]->id] = array();
        }

        for ($i = 1; $i <= 10800; $i++) {
            if ($paidBy >= 5) {
                $paidBy = 1;
            }

            if ($dues > count($memberCreated)) {
                $dues = 1;
            }

            $selectedDate = Carbon::createFromDate($faker->dateTimeBetween('-50 years', '-1 month'))->getTimestamp();


            while (in_array(date('m', $selectedDate) . " " . date('Y', $selectedDate), $existingDate)) {
                $selectedDate = Carbon::createFromDate($faker->dateTimeBetween('-50 years', '-1 month'))->getTimestamp();
            }

            array_push($existingDate["$dues"], date('m', $selectedDate) . " " . date('Y', $selectedDate));

            DuesPaymentLog::create([
                'dues_member' => $dues++,
                'amount_paid' => 5000,
                'paid_for' => $selectedDate,
                'created_at' => $selectedDate,
                'created_by' => 6,
            ]);
        }

        for ($i = 1; $i <= 5; $i++) {
            News::create([
                'title' => $faker->sentence(),
                'desc' => $faker->sentences(2, true),
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
            $docsType = ['Complaint', 'Event', 'Administration', 'Request'];

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
            'location' => $faker->address(),
            'created_by' => 1,
        ]);

        Complaint::create([
            'docs_id' => 1,
            'created_at' => Carbon::createFromDate($faker->dateTime())->getTimestamp(),
            'created_by' => 1,
        ]);

        Documentation::create([
            'docs_id' => 3,
            'contentType' => 'Administration',
            'contentDesc' => $faker->sentence(6, true),
            'created_at' => Carbon::createFromDate($faker->dateTime())->getTimestamp(),
            'created_by' => 1,
        ]);

        DocRequest::create([
            'docs_id' => 4,
            'request_by' => 1,
            'created_at' => Carbon::createFromDate($faker->dateTime())->getTimestamp(),
            'created_by' => 1,
        ]);
    }
}
