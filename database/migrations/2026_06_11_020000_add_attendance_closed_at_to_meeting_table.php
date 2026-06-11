<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('meeting', function (Blueprint $table) {
            if(!Schema::hasColumn('meeting', 'attendance_closed_at')){
                $table->timestamp('attendance_closed_at')->nullable()->after('finished_at')->index();
            }
        });
    }

    public function down()
    {
        Schema::table('meeting', function (Blueprint $table) {
            if(Schema::hasColumn('meeting', 'attendance_closed_at')){
                $table->dropIndex(['attendance_closed_at']);
                $table->dropColumn('attendance_closed_at');
            }
        });
    }
};
