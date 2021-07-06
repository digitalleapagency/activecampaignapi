<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateContactsTable extends Migration {
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up() {
        Schema::create('active_contacts', function (Blueprint $table) {
            $table->id();
            $table->string('activeContactID', 20);
            $table->string('firstName');
            $table->string('lastName');
            $table->string('email')->unique();
            $table->string('phone_number', 10);
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
        Schema::dropIfExists('active_contacts');
    }
}