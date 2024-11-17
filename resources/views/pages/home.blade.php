@extends('_template')

@section('title')
    Dashboard
@endsection

@section('header_imports')
    <script src="https://cdn.jsdelivr.net/npm/signature_pad@4.1.7/dist/signature_pad.umd.min.js"></script>
@endsection

@section('content')
    <style>
        #canvas_sign{
            outline: black 3px solid;
        }
    </style>

    <div class="row">
        <div class="col-12 justify-content-center">Welcome, <?php //echo request()->session()->get('user')->name ?></div>
        <!-- <div class="col-4 float-right">Welcome, </div>
        <div class="col-8 "><?php //echo request()->session()->get('user')->name ?></div> -->
    </div>
    <div class="row">
        <div class="col-12 justify-content-center">
            <canvas id="canvas_sign"></canvas>
        </div>
    </div>
@endsection

@section('post_script')
    <script>
        $(document).ready(function () {
            const signaturePad = new SignaturePad(document.getElementById('canvas_sign'));

            // $.ajax({
            //     url: `${baseUrl}/api/perolehan/list-hpl`,
            //     type: "get",
            //     data: {},
            //     success: (res) => {
            //         if(res.code == 0){
            //             // console.log('list-hpl', res);
            //             $('#datalist_site').html('');
            //             $content = '';
            //             res.data.forEach(a => {
            //                 $content += `<option value="${a.site_name}">
            //                     ${a.provinsi?.provinsi_nama}, ${a.provinsi?.kota?.kota_nama}, ${a.kecamatan}, ${a.kelurahan}
            //                 </option>
            //                 `;
            //             });
            //             $('#datalist_site').html($content);
            //             listHpl = res.data;

            //             if(edit_site_name){
            //                 $('[name="site_name"]').val(edit_site_name);
            //             }
            //             $('[name="site_name"]').trigger("change");
            //             if(edit_site_name){
            //                 // $('[name="site_name"]').prop("disabled", true);
            //                 $('[name="site_name"]').prop("readonly", true);
            //             }
            //         }
            //     }
            // });
        });
    </script>
@endsection
