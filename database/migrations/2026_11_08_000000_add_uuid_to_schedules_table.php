<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('schedules', function (Blueprint $table) {
            $table->uuid('uuid')->nullable()->unique()->after('id');
        });

        DB::table('schedules')->whereNull('uuid')->pluck('id')->each(
            fn ($id) => DB::table('schedules')->where('id', $id)->update(['uuid' => (string) Str::uuid()])
        );
    }

    public function down(): void
    {
        Schema::table('schedules', function (Blueprint $table) {
            $table->dropColumn('uuid');
        });
    }
};
