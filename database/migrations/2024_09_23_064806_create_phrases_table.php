<?php

use App\Enums\StatusEnum;
use App\Models\Translation;
use App\Models\TranslationFile;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('phrases', function (Blueprint $table) {
            $table->id();
            $table->uuid();
            $table->foreignId('translation_id');
            $table->foreignId('translation_file_id');
            $table->string('key');
            $table->string('group');
            $table->text('value')->nullable();
            $table->string('status')->default(StatusEnum::active->value);
            $table->json('parameters')->nullable();
            $table->text('note')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('phrases');
    }
};
