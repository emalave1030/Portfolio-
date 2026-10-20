<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('experiences', function (Blueprint $table) {
            $table->id();
            $table->string('role');
            $table->string('company');
            $table->string('location');
            $table->date('start_date');

            // Null marks the current role.
            $table->date('end_date')->nullable();

            // Display-only payload: rendered as a list, never queried.
            $table->json('bullets');

            $table->integer('sort_order')->default(0);
            $table->timestamps();

            $table->index('sort_order');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('experiences');
    }
};
