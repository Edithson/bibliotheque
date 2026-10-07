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
        Schema::create('books', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->string('slug')->unique();
            $table->text('description')->nullable();
            $table->text('excerpt')->nullable();
            $table->string('author')->nullable();
            $table->integer('price')->default(0); // En FCFA (0 = Gratuit)
            $table->string('cover_color')->nullable(); // couleur de couverture en hexadécimal
            $table->integer('cover_width')->default(50);
            $table->integer('cover_height')->default(200);
            $table->string('file_path')->nullable(); // Chemin privé du fichier PDF
            $table->integer('nbr_pages')->default(1);
            $table->integer('publish_year')->nullable()->default(2026);
            $table->boolean('is_published')->default(true)->index();
            $table->foreignId('category_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('updated_by_user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();

            $table->index(['is_published', 'price']);
            $table->index('user_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('books');
    }
};
