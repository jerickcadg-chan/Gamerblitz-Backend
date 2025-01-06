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
        Schema::create('client_themes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('client_id')->constrained('clients')->onDelete('cascade');
            $table->string('mg_bg')->nullable();
            $table->string('mg_fg')->nullable();
            $table->string('mg_fg_alt')->nullable();
            $table->string('mg_fg_error')->nullable();
            $table->string('mg_accent_1')->nullable();
            $table->string('mg_accent_2')->nullable();
            $table->string('mg_accent_3')->nullable();
            $table->string('mg_accent_4')->nullable();
            $table->string('mg_accent_5')->nullable();
            $table->string('mg_accent_6')->nullable();
            $table->string('mg_border')->nullable();
            $table->string('mg_scrollbar')->nullable();
            $table->string('mg_status_waiting')->nullable();
            $table->string('fg_btn')->nullable();
            $table->string('fg_btn_secondary')->nullable();
            $table->string('fg_btn_outline')->nullable();
            $table->string('mg_bg_1')->nullable();
            $table->string('mg_bg_2')->nullable();
            $table->string('mg_bg_3')->nullable();
            $table->string('mg_bg_4')->nullable();
            $table->string('mg_bg_5')->nullable();
            $table->string('mg_bg_accent_1')->nullable();
            $table->string('bg_btn')->nullable();
            $table->string('bg_btn_secondary')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('client_themes');
    }
};
