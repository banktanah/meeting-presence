<?php

namespace App\Services;

use App\Exceptions\ApiException;
use App\Models\Meeting;
use App\Models\MeetingDocument;
use App\Models\ExternalParticipant;
use App\Models\MeetingMember;
use App\Models\Monitor\ContactPerson as ContactPersonModel;
use App\Models\Monitor\Person;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class MeetingService
{
    public function __construct(){

    }

    public function list(){
        $columns = [
            'meeting_id',
            'name',
            'description',
            'location',
            'scheduled_start',
            'scheduled_finish',
            'started_at',
            'finished_at',
            'meeting_type_id',
            'code',
            'created_at',
            'updated_at',
        ];

        if(Schema::hasColumn('meeting', 'attendance_closed_at')){
            $columns []= 'attendance_closed_at';
        }

        $rs = Meeting::query()
            ->select($columns)
            ->where('is_deleted', 0)
            ->withCount([
                'members as members_count' => function ($q) {
                    $q->where('is_deleted', 0);
                },
                'members as attended_members_count' => function ($q) {
                    $q->where('is_deleted', 0);
                    $q->whereNotNull('attend_at');
                },
            ])
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

    private function getActiveMeeting(string $meeting_id)
    {
        $meeting = Meeting::query()
            ->where('meeting_id', $meeting_id)
            ->where('is_deleted', 0)
            ->first();

        if(empty($meeting)){
            throw new ApiException("Meeting with the specified id does not exists");
        }

        return $meeting;
    }

    private function assertAttendanceIsOpen(string $meeting_id)
    {
        $meeting = $this->getActiveMeeting($meeting_id);

        if(!empty($meeting->attendance_closed_at)){
            throw new ApiException("Absensi meeting sudah ditutup oleh admin");
        }

        return $meeting;
    }

    public function closeAttendance(string $meeting_id)
    {
        try {
            DB::beginTransaction();

            $meeting = $this->getActiveMeeting($meeting_id);
            $meeting->attendance_closed_at = Carbon::now();
            $meeting->save();

            DB::commit();

            return $meeting;
        } catch (\Throwable $e) {
            DB::rollback();
            throw $e;
        }
    }

    public function openAttendance(string $meeting_id)
    {
        try {
            DB::beginTransaction();

            $meeting = $this->getActiveMeeting($meeting_id);
            $meeting->attendance_closed_at = null;
            $meeting->save();

            DB::commit();

            return $meeting;
        } catch (\Throwable $e) {
            DB::rollback();
            throw $e;
        }
    }

    public function listMember(string $meeting_id_or_code){
        $meeting = Meeting::query()
            ->select('meeting_id')
            ->where(function($q) use ($meeting_id_or_code){
                $q->where('meeting_id', $meeting_id_or_code);
                $q->orWhere('code', $meeting_id_or_code);
            })
            ->where('is_deleted', 0)
            ->first();

        if(empty($meeting)){
            throw new ApiException("Meeting with the specified id or code does not exists");
        }

        $rs = MeetingMember::query()
            ->where('meeting_id', $meeting->meeting_id)
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

    public function searchExternalParticipants(string $keyword)
    {
        $keyword = trim($keyword);
        if(strlen($keyword) < 2){
            return [];
        }

        $normalizedKeyword = $this->normalizeExternalValue($keyword);
        $phoneKeyword = $this->normalizePhone($keyword);

        return ExternalParticipant::query()
            ->select([
                'external_participant_id',
                'name',
                'instansi',
                'jabatan',
                'phone',
                'email',
                'last_seen_at',
            ])
            ->where('is_deleted', 0)
            ->where(function($query) use ($keyword, $normalizedKeyword, $phoneKeyword) {
                $query->where('normalized_name', 'like', "%$normalizedKeyword%");
                $query->orWhere('instansi', 'like', "%$keyword%");
                $query->orWhere('jabatan', 'like', "%$keyword%");
                $query->orWhere('email', 'like', "%$keyword%");

                if(!empty($phoneKeyword)){
                    $query->orWhere('phone', 'like', "%$phoneKeyword%");
                }
            })
            ->orderBy('last_seen_at', 'DESC')
            ->orderBy('updated_at', 'DESC')
            ->limit(10)
            ->get();
    }

    private function sanitizeMemberInput(array $row)
    {
        foreach(['id_number', 'name', 'instansi', 'jabatan', 'phone', 'email'] as $field){
            if(array_key_exists($field, $row) && is_string($row[$field])){
                $row[$field] = trim($row[$field]);
            }
        }

        if(!empty($row['external_participant_id'])){
            $row['external_participant_id'] = intval($row['external_participant_id']);
        }

        if(!empty($row['phone'])){
            $row['phone'] = $this->normalizePhone($row['phone']);
        }

        if(!empty($row['email'])){
            $row['email'] = strtolower($row['email']);
        }

        return $row;
    }

    private function findExistingMeetingMember($meeting_id, array $row)
    {
        $query = MeetingMember::query()
            ->where('meeting_id', $meeting_id)
            ->where('is_deleted', 0);

        if(!empty($row['id_number'])){
            return $query->where('id_number', $row['id_number'])->first();
        }

        if(!empty($row['external_participant_id'])){
            return $query->where('external_participant_id', $row['external_participant_id'])->first();
        }

        return $query->where(function($q) use ($row) {
            if(!empty($row['phone'])){
                $q->orWhere('phone', $row['phone']);
            }

            if(!empty($row['email'])){
                $q->orWhere('email', $row['email']);
            }

            if(!empty($row['name'])){
                $q->orWhere(function($q2) use ($row) {
                    $q2->where('name', $row['name']);
                    if(!empty($row['instansi'])){
                        $q2->where('instansi', $row['instansi']);
                    }
                });
            }
        })->first();
    }

    private function upsertExternalParticipant(array $row)
    {
        if(empty($row['name'])){
            return null;
        }

        $participant = null;
        if(!empty($row['external_participant_id'])){
            $participant = ExternalParticipant::query()
                ->where('external_participant_id', $row['external_participant_id'])
                ->where('is_deleted', 0)
                ->first();
        }

        if(empty($participant)){
            $participant = $this->findExternalParticipant($row);
        }

        if(empty($participant)){
            $participant = new ExternalParticipant();
        }

        $participant->fill([
            'name' => $row['name'],
            'normalized_name' => $this->normalizeExternalValue($row['name']),
            'instansi' => $row['instansi'] ?? null,
            'jabatan' => $row['jabatan'] ?? null,
            'phone' => $row['phone'] ?? null,
            'email' => $row['email'] ?? null,
            'last_seen_at' => Carbon::now(),
            'is_deleted' => 0,
        ]);
        $participant->save();

        return $participant;
    }

    private function findExternalParticipant(array $row)
    {
        return ExternalParticipant::query()
            ->where('is_deleted', 0)
            ->where(function($q) use ($row) {
                if(!empty($row['phone'])){
                    $q->orWhere('phone', $row['phone']);
                }

                if(!empty($row['email'])){
                    $q->orWhere('email', $row['email']);
                }

                if(!empty($row['name'])){
                    $q->orWhere(function($q2) use ($row) {
                        $q2->where('normalized_name', $this->normalizeExternalValue($row['name']));
                        if(!empty($row['instansi'])){
                            $q2->where('instansi', $row['instansi']);
                        }
                    });
                }
            })
            ->first();
    }

    private function normalizeExternalValue($value)
    {
        $value = trim((string) $value);
        $value = preg_replace('/\s+/', ' ', $value);

        return strtoupper($value);
    }

    private function normalizePhone($value)
    {
        return preg_replace('/[^0-9+]/', '', (string) $value);
    }

    public function addMember($meeting_id, $members){
        try {
            DB::beginTransaction();

            $this->assertAttendanceIsOpen($meeting_id);
            
            $results = [];
            if(!empty($members)){
                foreach($members as $row){
                    $row = $this->sanitizeMemberInput($row);
                    $existing = $this->findExistingMeetingMember($meeting_id, $row);

                    if(empty($row['id_number'])){
                        $participant = $this->upsertExternalParticipant($row);
                        if(!empty($participant)){
                            $row['external_participant_id'] = $participant->external_participant_id;
                        }
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
                        $existing->fill($row);
                        $existing->save();
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

            if(!empty($input['meeting_id'])){
                $this->assertAttendanceIsOpen($input['meeting_id']);
            }

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

            $this->assertAttendanceIsOpen($mm->meeting_id);

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

            $file_inputs = $input['files'];
            $rs_docs = MeetingDocument::where('meeting_id', $input['meeting_id'])->get();

            foreach($file_inputs as $file){
                $existing = false;
                foreach($rs_docs as $doc){
                    $data = null;
                    if($doc->filename == $file['filename']){
                        $data = MeetingDocument::where('meeting_docs_id', $doc->meeting_docs_id)->first();
                        $data->extension = $file['extension'];
                        $data->base64data = $file['base64data'];
                        $data->save();
                        $existing = true;
                        break;
                    }
                }

                if(!$existing){
                    $data = new MeetingDocument();
                    $data->meeting_id = $input['meeting_id'];
                    $data->fill($file);
                    $data->save();
                }
            }

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
