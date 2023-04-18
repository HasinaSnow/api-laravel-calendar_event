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
        Schema::create('events', function (Blueprint $table) {
            $table->id();

            $table->date('date');
            $table->boolean('audience');
            
            // relation one to many
            $table->foreignId('client_id')->constrained();
            $table->foreignId('category_id')->constrained();
            $table->foreignId('confirmation_id')->constrained();
            $table->foreignId('place_id')->constrained();
            $table->foreignId('type_id')->constrained();
            $table->foreignId('pack_id')->constrained();
            
            $table->unsignedBigInteger('created_by');
            $table->unsignedBigInteger('updated_by')->nullable();
            $table->foreign('created_by')->references('id')->on('users');
            $table->foreign('updated_by')->references('id')->on('users');
            
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('events');
    }
};
