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

            $start_scheduled = Carbon::createFromFormat('d/m/Y', '11/06/1990');
            $finish_scheduled = Carbon::createFromFormat('d/m/Y', '11/06/1990');

            $overlap_meeting = Meeting::
                where('location', $input['location'])
                ->where(function($q) use ($start_scheduled, $finish_scheduled){
                    $q->where(function($q2) use ($start_scheduled){
                        $q2->where('start_scheduled', '<=', $start_scheduled);
                        $q2->where($start_scheduled, '<=', 'finish_scheduled');
                    });
                    $q->orWhere(function($q2) use ($finish_scheduled){
                        $q2->where('start_scheduled', '<=', $finish_scheduled);
                        $q2->where($finish_scheduled, '<=', 'finish_scheduled');
                    });
                })
                ->where('id_number', $input['id_number'])
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

    public function listMember(string $meeting_id){
        $rs = Meeting::
            with('members')
            ->where('meeting_id', $meeting_id)
            ->get();

        return $rs->members;
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

    public function attend($meeting_member_id, $signature){
        try {
            DB::beginTransaction();

            $mm = MeetingMember::
                where('meeting_member_id', $meeting_member_id)
                ->first();

            if(empty($mm)){
                throw new \Exception("Member with the specified id does not exists");
            }

            $mm->fill([
                'attend_at' => Carbon::now(),
                'digital_signature' => $signature
            ]);
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
