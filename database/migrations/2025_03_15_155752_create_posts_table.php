<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up()
    {
        Schema::create('posts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade'); // Links to users table
            $table->text('content')->nullable(); // Post text content (nullable for media-only posts)
            $table->json('media')->nullable(); // Store images/videos as JSON array
            $table->enum('privacy', ['public', 'private'])->default('public'); // Public or private post
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('posts');
    }
};
