<?php

namespace App\Services;

use App\Models\Meeting;
use App\Models\MeetingMember;
use App\Models\Monitor\ContactPerson as ContactPersonModel;
use App\Models\Monitor\Person;
use App\Models\Masters\Room;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

class RoomService
{
    public function __construct(){

    }

    public function list(){
        $rs = Room::get();

        return $rs;
    }

    public function get(string $meeting_id){
        $rs = Meeting::
            with('members')
            ->where('meeting_id', $meeting_id)
            ->first();

        return $rs;
    }

    public function add($input){
        try {
            DB::beginTransaction();

            $newData= new Room();
            $newData->fill($input);
            $newData->save();
        
            DB::commit();
        } catch (\Throwable $e) {
            DB::rollback();
            throw $e;
        }
    }

    public function update($input){
        try {
            DB::beginTransaction();

            $meeting = Meeting::
                where('meeting_id', $input['meeting_id'])
                ->first();

            if(empty($meeting)){
                throw new \Exception("Meeting with the specified id does not exists");
            }

            $meeting->fill($input);
            $meeting->save();

            DB::commit();
        } catch (\Throwable $e) {
            DB::rollback();
            throw $e;
        }
    }
}
