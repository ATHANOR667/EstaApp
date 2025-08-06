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
        Schema::create('admins', function (Blueprint $table) {
            $table->id();
            $table->uuid('matricule')->unique();
            $table->string('nom');
            $table->string('prenom');
            $table->string('email')->unique()->nullable();
            $table->string('password')->nullable();
            $table->timestamp('password_changed_at')->nullable();
            $table->string('passcode')->nullable();
            $table->string('passcode_reset_status')->nullable();
            $table->timestamp('passcode_reset_date')->nullable();
            $table->string('telephone');
            $table->string('pays')->nullable();
            $table->string('ville')->nullable();
            $table->string('photoProfil')->nullable();
            $table->string('pieceIdentiteRecto')->nullable();
            $table->string('pieceIdentiteVerso')->nullable();
            $table->softDeletes();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('admins');
    }
};
