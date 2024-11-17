@extends('_template')

@section('title')
    List By HPL
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
                <div class="table-responsive">
                    <table id="table-list-by-hpl" class="table table-striped table-hover">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>name</th>
                                <th>start</th>
                                <th>finish</th>
                                <th>location</th>
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
    
    <!-- Modal -->
    <div class="modal fade" id="keteranganModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-fullscreen-md-down modal-dialog-scrollable" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">Modal title</h5>
                    <button type="button" class="close" data-bs-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    ...
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
                        // if(res.data){
                        //     for(let site_name in res.data){
                        //         // console.log(site_name, res.data[site_name]);
                        //         let pic_list = res.data[site_name];

                        //         let pic_html_contents = '';
                        //         pic_list.forEach(a => {
                        //             // console.log(a);
                        //             pic_html_contents += `
                        //                 <tr>
                        //                     <td style="min-width: 7%;">${a.person.name}</td>
                        //                     <td style="min-width: 7%;">${a.person.country_code} ${Utils.formatPhoneWithDash(a.person.phone)}</td>
                        //                     <td>${a.jabatan? a.jabatan: ''}${a.instansi? ` - ${a.instansi}`: ''}</td>
                        //                     <td style="min-width: 7%;">
                        //                         <button type="button" onclick="viewKeterangan('${btoa(a.desc)}')" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#keteranganModal" >
                        //                             View
                        //                         </button>
                        //                     </td>
                        //                 </tr>
                        //             `;
                        //         });

                        //         $('#data-body').append(`
                        //             <tr>
                        //                 <td>${no++}</td>
                        //                 <td>
                        //                     ${site_name}
                        //                     <a href="#" onclick="cpEdit('${site_name}');" class="ml-2"><i class="fas fa-pen"></i></a>
                        //                 </td>
                        //                 <td colspan="3">
                        //                     <table class="table table-bordered t able-striped" style="margin: 0">
                        //                         <tr>
                        //                             <th>Name</th>
                        //                             <th>Phone</th>
                        //                             <th>Jabatan/Instansi</th>
                        //                             <th>Keterangan</th>
                        //                         </tr>
                        //                         ${pic_html_contents}
                        //                     </table>
                        //                 </td>
                        //             </tr>
                        //         `);

                        //         Utils.initializeBootstrapTooltips();
                        //     }

                        //     dtTableObj = new DataTable('#table-list-by-hpl');
                        // }
                    }
                }
            });
        }
        
        function viewKeterangan(base64text){
            let modal = $('#keteranganModal');
            modal.find('.modal-title').html(``);

            modal.find('.modal-body').html(atob(base64text));
        }
    </script>
@endsection