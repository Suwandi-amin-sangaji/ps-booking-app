<?php

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

return new class extends Migration
{
    public function up()
    {
        Schema::create('time_slots', function (Blueprint $table) {
            $table->id();
            $table->string('slot');
            $table->time('start_time');
            $table->time('end_time');
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        // Insert default time slots
        DB::table('time_slots')->insert([
            ['slot' => 'Pagi', 'start_time' => '09:00:00', 'end_time' => '11:00:00', 'is_active' => true, 'created_at' => now(), 'updated_at' => now()],
            ['slot' => 'Siang', 'start_time' => '12:00:00', 'end_time' => '14:00:00', 'is_active' => true, 'created_at' => now(), 'updated_at' => now()],
            ['slot' => 'Sore', 'start_time' => '15:00:00', 'end_time' => '17:00:00', 'is_active' => true, 'created_at' => now(), 'updated_at' => now()],
            ['slot' => 'Malam', 'start_time' => '18:00:00', 'end_time' => '20:00:00', 'is_active' => true, 'created_at' => now(), 'updated_at' => now()],
        ]);
    }

    public function down()
    {
        Schema::dropIfExists('time_slots');
    }
};