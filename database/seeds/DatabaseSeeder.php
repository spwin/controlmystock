<?php

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $this->call('UnitGroupsSeeder');
        $this->command->info('Unit Groups table seeded!');
        $this->call('UnitsSeeder');
        $this->command->info('Units table seeded!');
        $this->call('UsersSeeder');
        $this->command->info('Users table seeded!');
    }
}
