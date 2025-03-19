<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('first_name')->nullable();
            $table->string('last_name')->nullable();
            $table->text('description')->nullable();
            $table->date('date_of_birth')->nullable();
            $table->string('language')->nullable();
            $table->string('location')->nullable();
            $table->string('relationship_status')->nullable();
            $table->string('profile_picture')->nullable();
            $table->string('banner_picture')->nullable();
        });
    }

    public function down()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn([
                'first_name', 'last_name', 'description', 'date_of_birth',
                'language', 'location', 'relationship_status',
                'profile_picture', 'banner_picture'
            ]);
        });
    }
};
