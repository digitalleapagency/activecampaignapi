<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateContactTagsTable extends Migration {
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up() {
        Schema::create('active_contact_tags', function (Blueprint $table) {
            $table->id();
            $table->string('activeContactTagID', 20);
            $table->string('ContactID', 20);
            $table->string('TagID', 20);
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
        Schema::dropIfExists('active_contact_tags');
    }
}