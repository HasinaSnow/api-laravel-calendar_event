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
        Schema::create('event_task', function (Blueprint $table) {

            $table->foreignId('event_id')->constrained()->constrained()->onDelete('cascade');
            $table->foreignId('task_id')->constrained()->constrained()->onDelete('cascade');

            $table->boolean('check')->default(false);
            $table->datetime('check_at')->nullable()->default(null);
            $table->datetime('expiration')->nullable()->default(null);

            $table->unsignedBigInteger('attribute_to')->nullable( )->default(null);
            $table->datetime('attribute_at')->nullable()->default(null);
            $table->unsignedBigInteger('created_by');
            $table->unsignedBigInteger('updated_by')->nullable();
            $table->foreign('attribute_to')->references('id')->on('users');
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
        Schema::dropIfExists('event_task');
    }
};
