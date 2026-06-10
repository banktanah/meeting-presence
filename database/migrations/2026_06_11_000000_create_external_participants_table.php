<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('external_participants', function (Blueprint $table) {
            $table->bigIncrements('external_participant_id');
            $table->string('name');
            $table->string('normalized_name')->nullable()->index();
            $table->string('instansi')->nullable();
            $table->string('jabatan')->nullable();
            $table->string('phone')->nullable()->index();
            $table->string('email')->nullable()->index();
            $table->timestamp('last_seen_at')->nullable();
            $table->boolean('is_deleted')->default(false)->index();
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('external_participants');
    }
};
