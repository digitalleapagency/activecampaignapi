<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateContactEventsTable extends Migration {
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up() {
        Schema::create('active_contact_events', function (Blueprint $table) {
            $table->id();
            $table->string('ContactID', 20);
            $table->string('event');
            $table->text('eventdata');
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
        Schema::dropIfExists('active_contact_events');
    }
}