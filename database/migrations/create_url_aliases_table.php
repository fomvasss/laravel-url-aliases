<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateUrlAliasesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('url_aliases', function (Blueprint $table) {
            $table->increments('id');
            $table->string('system_path')->nullable();          // system/article/1
            $table->string('aliased_path')->unique();           // article/some-slug-article
            $table->string('type', 25)->nullable();      // null (is alias) | 301 | 302
            $table->string('model_type')->nullable();
            $table->integer('model_id')->nullable();

            $table->index(['model_type', 'model_id']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('url_aliases');
    }
}
