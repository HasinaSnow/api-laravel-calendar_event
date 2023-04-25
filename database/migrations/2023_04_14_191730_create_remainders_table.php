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
        Schema::create('remainders', function (Blueprint $table) {
            $table->id();

            $table->date('expiration')->nullable()->default(null);
            $table->integer('rate')->default(80);
            $table->integer('amount')->default(0);
            $table->text('infos')->nullable();

            // $table->foreignId('payment_id')->constrained()->onDelete('cascade');

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
        Schema::dropIfExists('remainders');
    }
};
