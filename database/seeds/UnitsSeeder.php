<?php

use App\Models\Units;
use App\Models\UnitGroups;
use Illuminate\Database\Seeder;

class UnitsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('units')->delete();

        //weight unit
        $unit = new Units;
        $unit->fill(array(
            'title' => 'g',
            'factor' => 1,
            'default' => 1,
            'group_id' => 1,
            'disable_delete' => 1
        ));
        $unit->save();

        //capacity unit
        $unit = new Units;
        $unit->fill(array(
            'title' => 'ml',
            'factor' => 1,
            'default' => 1,
            'group_id' => 2,
            'disable_delete' => 1
        ));
        $unit->save();

        //units unit
        $unit = new Units;
        $unit->fill(array(
            'title' => 'unit',
            'factor' => 1,
            'default' => 1,
            'group_id' => 3,
            'disable_delete' => 1
        ));
        $unit->save();
    }
}
