<?php

namespace App\Services;

use App\Models\Meeting;
use App\Models\MeetingMember;
use App\Models\Monitor\ContactPerson as ContactPersonModel;
use App\Models\Monitor\Person;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

class MeetingService
{
    public function __construct(){

    }

    public function list(){
        $rs = Meeting::
            with('members')
            ->get();

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

            $scheduled_start = Carbon::createFromFormat('d/m/Y', '11/06/1990');
            $scheduled_finish = Carbon::createFromFormat('d/m/Y', '11/06/1990');

            $overlap_meeting = Meeting::
                where('location', $input['location'])
                ->where(function($q) use ($scheduled_start, $scheduled_finish){
                    $q->where(function($q2) use ($scheduled_start){
                        $q2->where('scheduled_start', '<=', $scheduled_start);
                        $q2->where($scheduled_start, '<=', 'scheduled_finish');
                    });
                    $q->orWhere(function($q2) use ($scheduled_finish){
                        $q2->where('scheduled_start', '<=', $scheduled_finish);
                        $q2->where($scheduled_finish, '<=', 'scheduled_finish');
                    });
                })
                ->first();

            if(!empty($overlap_meeting)){
                throw new \Exception("Meeting within the specified schedule is already exists");
            }

            $newMeeting = new Meeting();
            $newMeeting->fill($input);
            $newMeeting->save();
        
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

    public function listMember(string $meeting_id){
        $rs = MeetingMember::where('meeting_id', $meeting_id)
            ->get();

        return $rs;
    }

    public function getMemberDetail(string $meeting_member_id){
        $rs = MeetingMember::where('meeting_member_id', $meeting_member_id)
            ->first();

        $rs->makeVisible(['digital_signature', 'photo']);

        return $rs;
    }

    public function addMember($input){
        try {
            DB::beginTransaction();

            $existing_member = MeetingMember::
                where('meeting_id', $input['meeting_id'])
                ->where('id_number', $input['id_number'])
                ->first();

            if(!empty($existing_member)){
                throw new \Exception("Member with the specified id_number is already exists");
            }

            $newMember = new MeetingMember();
            $newMember->fill($input);
            $newMember->save();
        
            DB::commit();
        } catch (\Throwable $e) {
            DB::rollback();
            throw $e;
        }
    }

    public function attend($input){
        try {
            DB::beginTransaction();

            $mm = MeetingMember::
                where('meeting_member_id', $input['meeting_member_id'])
                ->first();

            if(empty($mm)){
                throw new \Exception("Member with the specified id does not exists");
            }

            $input['attend_at'] = Carbon::now();

            $mm->fill($input);
            $mm->save();
        
            DB::commit();
        } catch (\Throwable $e) {
            DB::rollback();
            throw $e;
        }
    }

    // public function deleteContactPerson($site_name, $phone){
    //     try {
    //         DB::beginTransaction();

    //         $existing_cp = ContactPersonModel::
    //             where('site_name', $site_name)
    //             ->where('phone', $phone)
    //             ->first();

    //         if(empty($existing_cp)){
    //             throw new \Exception("Contact Person with specified site_name and phone cannot be found");
    //         }

    //         ContactPersonModel::where('site_name',$site_name)->where('phone',$phone)->delete();

    //         DB::commit();
    //     } catch (\Throwable $e) {
    //         DB::rollback();
    //         throw $e;
    //     }
    // }

    // public function findContactByPersonNameOrPhone($keyword){
    //     return ContactPersonModel::
    //         with('person')
    //         ->whereHas('person', function($a) use($keyword){
    //             $a
    //             ->where('name', 'like', "%$keyword%")
    //             ->orWhere('phone', 'like', "%$keyword%")
    //             ;
    //         })
    //         ->with('instansi')
    //         ->with('jabatan')
    //         ->get();
    // }
}
