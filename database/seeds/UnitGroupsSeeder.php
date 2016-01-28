<?php

use App\Models\UnitGroups;
use Illuminate\Database\Seeder;

class UnitGroupsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('unit_groups')->delete();

        //weight unit
        $unitGroups = new UnitGroups;
        $unitGroups->fill(['title' => 'weight', 'disable_delete' => 1]);
        $unitGroups->save();

        //capacity unit
        $unitGroups = new UnitGroups;
        $unitGroups->fill(['title' => 'capacity', 'disable_delete' => 1]);
        $unitGroups->save();

        //units unit
        $unitGroups = new UnitGroups;
        $unitGroups->fill(['title' => 'units', 'disable_delete' => 1]);
        $unitGroups->save();
    }
}
