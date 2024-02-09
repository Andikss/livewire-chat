<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('messages', function (Blueprint $table) {
            $table->uuid('id')->primary()->default(Str::uuid())->unique();

            $table->uuid('conversation_id');
            $table->foreign('conversation_id')->references('id')->on('conversation')->cascadeOnDelete();

            $table->uuid('sender_id')->nullable();
            $table->foreign('sender_id')->references('id')->on('users')->nullOnDelete();

            $table->uuid('receiver_id')->nullable();
            $table->foreign('receiver_id')->references('id')->on('users')->nullOnDelete();

            $table->timestamp('read_at')->nullable();

            $table->timestamp('receiver_deleted_at')->nullable();
            $table->timestamp('sender_deleted_at')->nullable();

            $table->text('body')->nullable();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('messages');
    }
};
