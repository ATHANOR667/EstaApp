<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('contrats', function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(\App\Models\Prestation::class);
            $table->text('content');
            $table->boolean('signature_artiste_representant')->nullable();
            $table->boolean('signature_contractant')->nullable();
            $table->text('motif')->nullable();
            $table->string('status')->default('draft');
            $table->string('docusign_envelope_id')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('contrats');
    }
};
