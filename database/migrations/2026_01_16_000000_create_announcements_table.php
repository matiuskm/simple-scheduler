<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('announcements', function (Blueprint $table) {
            $table->id();
            $table->text('message');
            $table->timestamp('start_at');
            $table->timestamp('end_at');
            $table->string('background_color', 20)->default('#1E88E5');
            $table->boolean('is_enabled')->default(true);
            $table->timestamps();

            $table->index(['is_enabled', 'start_at', 'end_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('announcements');
    }
};
