<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Schema;

class ModelerSeeder extends Seeder
{
    public function run(): void
    {
        Schema::withoutForeignKeyConstraints(fn() => $this->call(config('modeler.seeders', [])));
    }
}
