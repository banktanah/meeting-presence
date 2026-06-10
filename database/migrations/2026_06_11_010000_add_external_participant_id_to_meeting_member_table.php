<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('meeting_member', function (Blueprint $table) {
            if(!Schema::hasColumn('meeting_member', 'external_participant_id')){
                $table->unsignedBigInteger('external_participant_id')->nullable()->after('meeting_id')->index();
            }
        });
    }

    public function down()
    {
        Schema::table('meeting_member', function (Blueprint $table) {
            if(Schema::hasColumn('meeting_member', 'external_participant_id')){
                $table->dropIndex(['external_participant_id']);
                $table->dropColumn('external_participant_id');
            }
        });
    }
};
