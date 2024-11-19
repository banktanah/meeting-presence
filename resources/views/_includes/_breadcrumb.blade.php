<?php
    $breadcrumbs = [
        'home' => ['Home' => null],
        'cp-list-by-hpl' => ['Daftar Contact Person' => null, 'By Site / Lokasi' => null],
        'cp-list-by-ins' => ['Daftar Contact Person' => null, 'By Instansi' => null],
        'cp-input' => ['Input' => null, 'Contact Person' => null],
        'lap-list' => ['Daftar Laporan' => null, 'List' => null],
        'lap-input' => ['Input/Upload' => null, 'Laporan' => null]
    ];
?>

<!-- [ breadcrumb ] start -->
<div class="page-header">
    <div class="page-block">
        <div class="row align-items-center">
            <div class="col-md-6">
                <div class="page-header-title">
                    <h5 class="m-b-10">Meeting-Presence</h5>
                </div>
                <ul class="breadcrumb">
                    <?php
                        $routeName = request()->route()->getName();
                        $maps = !empty($breadcrumbs[$routeName])? $breadcrumbs[$routeName]: [request()->path() => null];
                        foreach($maps as $title => $url){
                            $tag = !empty($url)? "<a href='$url'>$title</a>": $title;
                            echo "<li class='breadcrumb-item'>$tag</li>";
                        }
                    ?>
                </ul>
            </div>
        </div>
    </div>
</div>
<!-- [ breadcrumb ] end -->