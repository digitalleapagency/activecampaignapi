<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTagsTable extends Migration {
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up() {
        Schema::create('active_tags', function (Blueprint $table) {
            $table->id();
            $table->string('activeTagID', 20);
            $table->string('tag');
            $table->string('tagType');
            $table->text('description');
            $table->enum('status', array('AC', 'IN', 'DL'))->default('AC');
            $table->timestamp('created_at')->useCurrent();
            $table->timestamp('updated_at')->useCurrent()->useCurrentOnUpdate();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down() {
        Schema::dropIfExists('active_tags');
    }
}