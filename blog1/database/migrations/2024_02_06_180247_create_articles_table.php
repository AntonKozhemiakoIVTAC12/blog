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
        Schema::create('articles', function (Blueprint $table) {
            $table->id();
            $table->string('title')->nullable(false); // Поле title теперь обязательное (Not Null)
            $table->string('slug');
            $table->text('content');
            $table->foreignId('user_id')->constrained(); // связь с таблицей пользователей
            $table->timestamps();
        });
//        DB::statement('ALTER TABLE articles ENGINE = MyISAM');
//        DB::statement('ALTER TABLE articles ADD FULLTEXT search(title, content)');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
//        DB::statement('ALTER TABLE articles DROP INDEX search');
        Schema::dropIfExists('articles');
    }
};
