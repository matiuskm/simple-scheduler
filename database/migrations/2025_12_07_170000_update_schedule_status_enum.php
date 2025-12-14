<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        $driver = Schema::getConnection()->getDriverName();

        if ($driver === 'mysql') {
            DB::statement("
                ALTER TABLE schedules
                MODIFY COLUMN status ENUM('draft', 'published', 'open', 'full', 'locked', 'completed', 'cancelled')
                NOT NULL DEFAULT 'draft'
            ");

            return;
        }

        if ($driver === 'sqlite') {
            DB::statement('PRAGMA foreign_keys=OFF;');

            Schema::table('schedules', function (Blueprint $table) {
                $table->dropUnique('unique_schedule_slot');
            });

            Schema::create('schedules_tmp', function (Blueprint $table) {
                $table->id();
                $table->string('title');
                $table->text('description')->nullable();
                $table->date('scheduled_date');
                $table->time('start_time');
                $table->time('end_time')->nullable();

                $table->foreignId('location_id')->constrained()->cascadeOnDelete();
                $table->string('status')->default('draft');
                $table->unsignedInteger('required_personnel')->default(1);
                $table->timestamps();
                $table->unique(['scheduled_date', 'start_time', 'location_id'], 'unique_schedule_slot');
            });

            DB::table('schedules')->orderBy('id')->get()->each(function ($schedule): void {
                DB::table('schedules_tmp')->insert((array) $schedule);
            });

            Schema::drop('schedules');
            Schema::rename('schedules_tmp', 'schedules');

            DB::statement('PRAGMA foreign_keys=ON;');

            return;
        }

        // Other drivers already treat enum as a string.
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        $driver = Schema::getConnection()->getDriverName();

        if ($driver === 'mysql') {
            DB::statement("
                ALTER TABLE schedules
                MODIFY COLUMN status ENUM('draft', 'published')
                NOT NULL DEFAULT 'draft'
            ");
        }
    }
};
