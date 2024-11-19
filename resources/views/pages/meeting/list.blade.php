@extends('_template')

@section('title')
    Meeting List
@endsection

@section('header_imports')
    <link rel="stylesheet" href="//cdn.datatables.net/2.1.2/css/dataTables.dataTables.min.css">
@endsection

@section('content')
    <div class="col-md-12">
        <div class="card">
            <div class="card-header">
                <h5>List Meeting</h5>
                <span class="d-block m-t-5"></span>
            </div>
            <div class="card-body table-border-style">
                <div class="row"></div>
                <div class="table-responsive">
                    <table id="table-meeting-list" class="table table-striped table-hover">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Topik</th>
                                <th>Start</th>
                                <th>Jumlah Peserta</th>
                                <th>Lokasi</th>
                                <th></th>
                            </tr>
                        </thead>
                        <tbody id="data-body">
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Meeting-setting Modal -->
    <div class="modal fade" id="modal_meeting_setting" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-fullscreen-md-down modal-dialog-scrollable" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">Modal title</h5>
                    <button type="button" class="close" data-bs-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="row form-group">
                        <div></div>
                    </div>
                    <div class="row">
                        <div class="col-4">Name</div>
                        <div class="col-8">
                            <input id="meeting_name" type="text" value=""/>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-4">Scheduled Start</div>
                        <div class="col-8">
                            <input id="meeting_start" type="text" value=""/>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-4">Scheduled Finish</div>
                        <div class="col-8">
                            <input id="meeting_finish" type="text" value=""/>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-4">Location</div>
                        <div class="col-8">
                            <textarea id="meeting_location"></textarea>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-12">
                            <label for="anonymous_attend">Anonymous Attendance</label>
                            <input id="anonymous_attend" type="checkbox" value="">
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <div class="row" style="width: 100%;">
                        <div class="col-12">
                            <button id="btn_save_setting" data-meeting-id="" type="button" onclick="saveMeetingSetting()" class="btn btn-success float-start" data-bs-toggle="modal" data-bs-target="#modal_presence">Save</button>
                            <button type="button" class="btn btn-secondary float-end" data-bs-dismiss="modal">Close</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Meeting-member Modal -->
    <div class="modal fade" id="modal_meeting_member" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-fullscreen-md-down modal-dialog-scrollable" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">Modal title</h5>
                    <button type="button" class="close" data-bs-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-12 text-center">
                            <div class="table-responsive">
                                <table id="table-meeting-member" class="table table-striped table-hover">
                                    <thead>
                                        <tr>
                                            <th>#</th>
                                            <th>Nama</th>
                                            <th>Status</th>
                                        </tr>
                                    </thead>
                                    <tbody id="data-body">
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                    <div class="row form-group">
                        <div class="col-4">Name</div>
                        <div class="col-8">
                            <input type="text" class="form-control">
                        </div>
                    </div>
                    <div class="row form-group">
                        <div class="col-12">
                            <div class="row">
                                <div class="col-4">NIP/NIK</div>
                                <div class="col-8">
                                    <input type="text" class="form-control">
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-4">Role</div>
                                <div class="col-8">
                                    <input type="text" class="form-control">
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-12">
                                    <button class="btn btn-success">Add Member</button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <div class="row" style="width: 100%;">
                        <div class="col-12">
                            <button type="button" class="btn btn-secondary float-end" data-bs-dismiss="modal">Close</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('footer_imports')
    <script src="//cdn.datatables.net/2.1.2/js/dataTables.min.js"></script>
@endsection

@section('post_script')
    <script>
        console.log('post_script');

        var baseUrl = "{{ url('') }}";
        var dtTableObj = null;
        var meetings = [];

        $(document).ready(function () {
            loadData();
        });

        function loadData(){
            $.ajax({
                url: `${baseUrl}/api/meeting/list`,
                type: "get",
                data: {},
                success: (res) => {
                    if(res.code == 0){
                        console.log(res);
                        $('#data-body').html('');
                        let no = 1;
                        if(res.data){
                            meetings = res.data;
                            res.data.forEach(a => {
                                let attendingMember = 
                                $('#data-body').append(`
                                    <tr>
                                        <td>${no++}</td>
                                        <td>${a.name}</td>
                                        <td>${Utils.simpleDateFormat(new Date(a.scheduled_start), true)}</td>
                                        <td>${a.members.filter(b => b.attend_at != null).length+'/'+a.members.length}</td>
                                        <td>${a.location}</td>
                                        <td>
                                            <a href="#" onclick="meetingSetting('${a.meeting_id}');" class="ml-2"><i class="fas fa-gear" data-bs-toggle="modal" data-bs-target="#modal_meeting_setting"></i></a>
                                            <a href="#" onclick="meetingViewMember('${a.meeting_id}');" class="ml-2"><i class="fas fa-user" data-bs-toggle="modal" data-bs-target="#modal_meeting_member"></i></a>
                                            <a href="${baseUrl+'/meeting/presence/'+a.meeting_id}" target="_blank" class="ml-2"><i class="fas fa-right-to-bracket"></i></a>
                                        </td>
                                    </tr>
                                `);
                            });

                            dtTableObj = new DataTable('#table-meeting-list');
                        }
                    }
                }
            });
        }
        
        function meetingAdd(){
            let modal = $('#modal_meeting_setting');
            
            let meetingData = meetings.find(a => meeting_id = meeting_id);
            modal.find('.modal-title').html(`Add new meeting`);

            modal.find('#btn_save_setting').attr('data-meeting-id', '');
        }
        
        function meetingSetting(meeting_id){
            let modal = $('#modal_meeting_setting');
            
            let meetingData = meetings.find(a => meeting_id = meeting_id);
            modal.find('.modal-title').html(`Setting - ${meetingData?.name}`);

            modal.find('#btn_save_setting').attr('data-meeting-id', meeting_id);
        }

        function meetingViewMember(meeting_id){
            let modal = $('#modal_meeting_member');

            let meetingData = meetings.find(a => meeting_id = meeting_id);
            modal.find('.modal-title').html(`Member - ${meetingData?.name}`);

            let tableMember = modal.find('#table-meeting-member').find('#data-body');
            tableMember.html('');

            if(meetingData?.members.length > 0){
                let no = 1;
                meetingData.members.forEach(a => {
                    tableMember.append(`
                        <tr>
                            <td>${no++}</td>
                            <td>${a.name}</td>
                            <td>${a.attend_at? 'Hadir': 'Belum/Tidak Hadir'}</td>
                        </tr>
                    `);
                });

                new DataTable('#table-meeting-member');
            }
        }

        function saveMeetingSetting(){
            let modal = $('#modal_meeting_setting');

            let btnSave = modal.find('#btn_save_setting');
            let meetingId = btnSave.attr('data-meeting-id');


        }
    </script>
@endsection