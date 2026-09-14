<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // user_id already exists in the payments table.
        // Nothing to add here.
    }

    public function down(): void
    {
        // Nothing to remove because this migration
        // did not create the column.
    }
};