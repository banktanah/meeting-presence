@extends('_template')

@section('title')
    Meeting Presence
@endsection

@section('header_imports')
    <link rel="stylesheet" href="//cdn.datatables.net/2.1.2/css/dataTables.dataTables.min.css">
    
    <!-- https://github.com/szimek/signature_pad -->
    <script src="https://cdn.jsdelivr.net/npm/signature_pad@4.1.7/dist/signature_pad.umd.min.js"></script>
@endsection

@section('content')
    <div class="col-md-12">
        <div class="card">
            <div class="card-header">
                <h5>Daftar Peserta</h5>
                <span class="d-block m-t-5"></span>
            </div>
            <div class="card-body table-border-style">
                <div class="table-responsive">
                    <table id="table-meeting-member" class="table table-striped table-hover">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Nama</th>
                                <th>Waktu TTD</th>
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
    
    <style>
        #canvas_sign{
            outline: black 3px solid;
        }
    </style>

    <!-- Modal -->
    <div class="modal fade" id="modal_presence" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
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
                        <div class="col-4 text-right pl-3">Nama :</div>
                        <div id="member_name" class="col-8"></div>
                    </div>
                    <div class="row mt-5">
                        <div class="col-12 text-center">
                            <canvas id="canvas_sign"></canvas>
                        </div>
                        <div class="col-12 text-center mt-2">
                            <button type="button" onclick="clearSignature()" class="btn btn-danger">Clear</button>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <div class="row" style="width: 100%;">
                        <div class="col-12">
                            <button id="btn_save_presence" data-meeting-member-id="" type="button" onclick="savePresence()" class="btn btn-success float-start" data-bs-toggle="modal" data-bs-target="#modal_presence">Ok</button>
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
        var meeting = null;
        var signaturePad = null;

        $(document).ready(function () {
            loadData();
            
            signaturePad = new SignaturePad(document.getElementById('canvas_sign'));
        });

        function loadData(){
            $.ajax({
                url: `${baseUrl}/api/meeting/get/{{$meeting_id}}`,
                type: "get",
                data: {},
                success: (res) => {
                    if(res.code == 0){
                        console.log(res);
                        $('#data-body').html('');
                        let no = 1;
                        if(res.data){
                            meeting = res.data;
                            if(res.data.members.length > 0){
                                res.data.members.forEach(a => {
                                    $('#data-body').append(`
                                        <tr>
                                            <td>${no++}</td>
                                            <td>${a.name}</td>
                                            <td>${a.attend_at? Utils.simpleDateFormat(new Date(a.attend_at), true): ''}</td>
                                            <td>
                                                <a href="#" onclick="presence(${a.meeting_member_id})" class="fas fa-right-to-bracket" data-bs-toggle="modal" data-bs-target="#modal_presence"></a>
                                            </td>
                                        </tr>
                                    `);
                                });
                            }

                            dtTableObj = new DataTable('#table-meeting-member');
                        }
                    }
                }
            });
        }

        function presence(meeting_member_id){
            signaturePad?.clear();

            let modal = $('#modal_presence');
            modal.find('.modal-title').html(meeting?.name);

            let memberData = meeting.members.find(a => a.meeting_member_id == meeting_member_id);

            modal.find('#member_name').html(memberData?.name);
            let btnSavePresence = modal.find('#btn_save_presence');
            btnSavePresence.attr('data-meeting-member-id', meeting_member_id);

            btnSavePresence.removeClass('d-none');

            if(memberData?.attend_at){
                if(memberData?.digital_signature){
                    signaturePad.fromDataURL(memberData.digital_signature);
                }
            }
        }

        function savePresence(){
            let modal = $('#modal_presence');
            let btnSavePresence = modal.find('#btn_save_presence');
            let meetingMemberId = btnSavePresence.attr('data-meeting-member-id');

            console.log('savePresence', meetingMemberId);

            let signatureData = signaturePad.toData();
            console.log('savePresence - signatureData', signatureData);

            let signatureBase64 = null;
            if(signatureData.length > 0){
                signatureBase64 = signaturePad.toDataURL("image/png");
            }
            console.log('savePresence - signatureBase64', signatureBase64);

            $.ajax({
                url: `${baseUrl}/api/meeting/presence`,
                type: "post",
                data: {
                    meeting_member_id: meetingMemberId,
                    signature: signatureBase64
                },
                success: (res) => {
                    if(res.code == 0){
                        console.log(res);
                        
                        loadData();
                    }
                }
            });
        }

        function clearSignature(){
            signaturePad?.clear();
        }
    </script>
@endsection