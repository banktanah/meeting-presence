<?php

namespace App\Services;

use App\Exceptions\ApiException;
use App\Models\Meeting;
use App\Models\MeetingDocument;
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
            where('is_deleted', 0)
            ->with(['members' => function ($q) {
                $q->where('is_deleted', 0); 
            }])
            ->orderBy('created_at', 'DESC')
            ->get();

        return $rs;
    }

    public function get(string $meeting_id_or_code){
        $rs = Meeting::
            where(function($q) use ($meeting_id_or_code){
                $q->where('meeting_id', $meeting_id_or_code);
                $q->orWhere('code', $meeting_id_or_code);
            })
            ->with(['members' => function ($q) {
                $q->where('is_deleted', 0); 
                $q->orderBy('created_at', 'DESC');
            }])
            ->with(['docs' => function ($q) {
                $q->where('is_deleted', 0); 
            }])
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
                        $q2->where('scheduled_finish', '>', $scheduled_start);
                    });
                    $q->orWhere(function($q2) use ($scheduled_finish){
                        $q2->where('scheduled_start', '<=', $scheduled_finish);
                        $q2->where('scheduled_finish', '>', $scheduled_finish);
                    });
                })
                ->first();

            // if(!empty($overlap_meeting)){
            //     throw new ApiException("Meeting within the specified schedule is already exists");
            // }

            $input['meeting_id'] = $this->generateMeetingId();
            $input['code'] = $this->generateMeetingCode();

            if(empty($input['meeting_type_id'])){
                $input['meeting_type_id'] = 1;
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

    private function generateMeetingId(){
        $date=date_create();
        $meetingIdPrefix = 'M'.date_format($date,"ymd");

        $lastMeeting = Meeting::where('meeting_id', 'like', $meetingIdPrefix.'%')->orderBy('meeting_id', 'DESC')->first();

        $meetingNumberToday = 1;
        if(!empty($lastMeeting)){
            $tempArr = explode($meetingIdPrefix, $lastMeeting->meeting_id);
            $meetingNumberToday = intval($tempArr[1]);
            $meetingNumberToday++;
        }

        $newMeetingId = $meetingIdPrefix.str_pad($meetingNumberToday, 4, "0", STR_PAD_LEFT);

        return $newMeetingId;
    }

    private function generateMeetingCode() {
        $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ!#_@';
        $characters_length = strlen($characters);
        $random_string = '';
    
        // Generate random characters until the string reaches desired length
        $length_of_string = 8;
        for ($i = 0; $i < $length_of_string; $i++) {
            $random_index = random_int(0, $characters_length - 1);
            $random_string .= $characters[$random_index];
        }
    
        return $random_string;
    }

    public function update($input){
        try {
            DB::beginTransaction();

            $meeting = Meeting::
                where('meeting_id', $input['meeting_id'])
                ->first();

            if(empty($meeting)){
                throw new ApiException("Meeting with the specified id does not exists");
            }

            $meeting->fill($input);
            $meeting->save();

            DB::commit();
        } catch (\Throwable $e) {
            DB::rollback();
            throw $e;
        }
    }

    public function listMember(string $meeting_id_or_code){
        $meeting = $this->get($meeting_id_or_code);

        if(empty($meeting)){
            throw new ApiException("Meeting with the specified id or code does not exists");
        }

        $rs = MeetingMember::
            where('meeting_id', $meeting->meeting_id)
            ->where('is_deleted', 0)
            ->orderBy('created_at', 'DESC')
            ->get();

        $rs->makeVisible(['digital_signature', 'photo']);

        return $rs;
    }

    public function getMemberDetail(string $meeting_member_id){
        $rs = MeetingMember::where('meeting_member_id', $meeting_member_id)
            ->first();

        $rs->makeVisible(['digital_signature', 'photo']);

        return $rs;
    }

    public function addMember($meeting_id, $members){
        try {
            DB::beginTransaction();
            
            $results = [];
            if(!empty($members)){
                foreach($members as $row){
                    $existing = null;
                    if(!empty($row['id_number'])){
                        $existing = MeetingMember::
                            where('meeting_id', $meeting_id)
                            ->where('id_number', $row['id_number'])
                            ->first();
                    }
                    
                    $row['meeting_id'] = $meeting_id;
                    $meeting_member_id = null;
                    if(empty($existing)){
                        $newMember = new MeetingMember();
                        $newMember->fill($row);
                        $newMember->save();
                        $meeting_member_id = $newMember->meeting_member_id;
                        $row['status'] = 'success';
                    }else{
                        $meeting_member_id = $existing->meeting_member_id;
                        $row['status'] = 'already_exist';
                    }
                    $row['meeting_member_id'] = $meeting_member_id;

                    $results []= $row;
                }
            }

            DB::commit();

            return $results;
        } catch (\Throwable $e) {
            DB::rollback();
            throw $e;
        }
    }

    public function updateMember($input){
        try {
            DB::beginTransaction();

            $existing = MeetingMember::
                where('meeting_member_id', $input['meeting_member_id'])
                ->first();

            if(empty($existing)){
                throw new ApiException("Meeting-Member with the specified id does not exists");
            }

            $existing->fill($input);
            $existing->save();

            DB::commit();
        } catch (\Throwable $e) {
            DB::rollback();
            throw $e;
        }
    }

    public function attend($input){
        try {
            DB::beginTransaction();

            // $mm = MeetingMember::select('*')
            //     ->where('meeting_member_id', !empty($input['meeting_member_id'])? $input['meeting_member_id']: '')
            //     ->orWhere(function($q) use ($input){
            //         $q->where('meeting_id', $input['meeting_id']);
            //         if()
            //         $q->where(function($q2) use($input){
            //             $q2->where('id_number', $scheduled_start);
            //             $q2->orWhere('id_number', $scheduled_start);
            //         });
            //     })
            //     ->first();

            $queryable = MeetingMember::select('*');
            if(!empty($input['meeting_member_id'])){
                $queryable = $queryable->where('meeting_member_id', $input['meeting_member_id']);
            }else{
                $queryable = $queryable->where('meeting_id', $input['meeting_id']);
                if(!empty($input['id_number'])){
                    $queryable = $queryable->where('id_number', $input['id_number']);
                }else if(!empty($input['name'])){
                    $queryable = $queryable->where('name', $input['name']);
                }else if(!empty($input['phone'])){
                    $queryable = $queryable->where('phone', $input['phone']);
                }else if(!empty($input['email'])){
                    $queryable = $queryable->where('email', $input['email']);
                }else{
                    throw new ApiException("No proper identification fields given for signing attendance", 1);
                }
            }

            $mm = $queryable->first();

            if(empty($mm)){
                throw new ApiException("Member with the specified id does not exists");
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

    public function addDocument($input){
        try {
            DB::beginTransaction();

            $rs_docs = MeetingDocument::where('meeting_id', $input['meeting_id'])->get();

            $data = null;
            foreach($rs_docs as $doc){
                if($doc->filename == $input['filename']){
                    $data = $doc;
                    break;
                }
            }

            if(!empty($data)){
                $data->extension = $input['extension'];
                $data->base64data = $input['base64data'];
            }else{
                $data = new MeetingDocument();
                $data->fill($input);
            }

            $data->save();

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
