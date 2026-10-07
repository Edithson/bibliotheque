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
        Schema::create('types', function (Blueprint $table) {
            $table->id();
            $table->string('name')->unique();
            $table->timestamps();
        });

        // Insert standard system roles in fixed order (1: guest, 2: auteur, 3: gerant, 4: admin)
        DB::table('types')->insert([
            ['id' => 1, 'name' => 'guest', 'created_at' => now(), 'updated_at' => now()],
            ['id' => 2, 'name' => 'auteur', 'created_at' => now(), 'updated_at' => now()],
            ['id' => 3, 'name' => 'gerant', 'created_at' => now(), 'updated_at' => now()],
            ['id' => 4, 'name' => 'admin', 'created_at' => now(), 'updated_at' => now()],
        ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('types');
    }
};
