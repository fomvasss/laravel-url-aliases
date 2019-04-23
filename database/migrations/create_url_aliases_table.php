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
            $table->string('source')->index();
            $table->string('alias')->index();
            $table->string('locale')->nullable();

            $table->string('type', 5)->nullable(); // null - is alias | 301 | 302
            $table->string('model_type')->nullable();
            $table->integer('model_id')->nullable();
            $table->string('locale_bound')->nullable();

            $table->index(['source', 'locale']);
            $table->unique(['alias', 'locale']);
            $table->unique(['model_type', 'model_id']);
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
