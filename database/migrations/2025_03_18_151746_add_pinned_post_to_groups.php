<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
{
    Schema::table('groups', function (Blueprint $table) {
        $table->unsignedBigInteger('pinned_post_id')->nullable()->after('id');
        $table->foreign('pinned_post_id')->references('id')->on('group_posts')->onDelete('set null');
    });
}

public function down()
{
    Schema::table('groups', function (Blueprint $table) {
        $table->dropForeign(['pinned_post_id']);
        $table->dropColumn('pinned_post_id');
    });
}


    
    
};
