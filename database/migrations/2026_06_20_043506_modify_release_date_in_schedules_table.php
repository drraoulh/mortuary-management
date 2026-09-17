<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // release_date is already nullable in the schedules create migration.
    }

    public function down(): void
    {
        //
    }
};
