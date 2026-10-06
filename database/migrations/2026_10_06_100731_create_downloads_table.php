<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('downloads', function (Blueprint $table) {
            $table->id();
            $table->foreignId('book_id')->constrained()->cascadeOnDelete();
            // On peut stocker l'IP, l'agent utilisateur, ou d'autres infos si besoin
            $table->string('ip_address')->nullable();
            $table->text('user_agent')->nullable();
            // on stocke l'id de l'utilisateur si connecté, sinon null
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
            // on stocke aussi la date de téléchargement
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('downloads');
    }
};
