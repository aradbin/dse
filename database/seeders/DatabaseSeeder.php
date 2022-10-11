<?php

namespace Database\Seeders;

use App\Models\Account;
use App\Models\Contact;
use App\Models\Organization;
use App\Models\User;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        Account::create([
            'code' => 'DSE',
            'name' => 'Dhaka Stock Exchange'
        ]);

        User::create([
            'first_name' => 'Arad',
            'last_name' => 'Bin',
            'email' => 'aradbin@gmail.com',
            'password' => 'aradbin',
            'owner' => true,
        ]);
    }
}
