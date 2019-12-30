<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddPostIntroductionToPostsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $table = config('nova-blog.table', 'nova_blog_posts');

        Schema::table($table, function (Blueprint $table) {
            $table->string('post_introduction')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        $table = config('nova-blog.table', 'nova_blog_posts');

        Schema::table($table, function (Blueprint $table) {
            $table->dropColumn('post_introduction');
        });
    }
}
