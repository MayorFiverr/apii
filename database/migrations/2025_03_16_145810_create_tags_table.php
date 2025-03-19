<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up() {
        Schema::create('tags', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade'); // The user who was tagged
            $table->foreignId('post_id')->nullable()->constrained()->onDelete('cascade'); // If tagged in a post
            $table->foreignId('comment_id')->nullable()->constrained()->onDelete('cascade'); // If tagged in a comment
            $table->timestamps();
        });
    }

    public function down() {
        Schema::dropIfExists('tags');
    }
};
