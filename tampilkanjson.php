<?php 
require "db.php";

?>
<!DOCTYPE html>
<html>

<head>
    <meta content="text/html; charset=utf-8" />
    <title>My First Grid</title>
    <link rel="stylesheet" type="text/css" media="screen" href="css/jquery-ui.css" />
    <link rel="stylesheet" type="text/css" media="screen" href="css/trirand/ui.jqgrid.css" />
    <script src="js/jquery.min.js" type="text/javascript"></script>
    <script src="js/trirand/i18n/grid.locale-en.js" type="text/javascript"></script>
    <script src="js/trirand/jquery.jqGrid.min.js" type="text/javascript"></script>
</head>

<body>

    <table id="grid_id">

</table>
        <!-- <tr>
            <th>No Invoice</th>
            <th>Nama Pelanggan</th>
            <th>Tanggal Pembelian</th>
        </tr> -->
        <?php 
    global $koneksi;

    $sql = "SELECT * FROM penjualan";
    $hasil = mysqli_query($koneksi, $sql);
    $penjualan = [];
    if($hasil){
        while($baris = mysqli_fetch_assoc($hasil)){
            $penjualan[] = $baris;
        }
    }

    ?>
   
    <script>
   let penjualan = JSON.parse(`<?= json_encode($penjualan);?>`);
   console.log(penjualan);
        //  fungsi json_encode mengubah data array menjadi data string
        // fungsi JSON.parse untuk mengubah teks menjadi data object
        // menampilkan database ke browser php
        // $("#grid_id").jqGrid({
        //     datatype: 'local',
        //     data: penjualan,
        //     colModel: [{
        //             name: 'no_invoice',
        //             label: 'Nomor Invoice'
        //         },
        //         {
        //             name: 'nama_pelanggan',
        //             label: 'Nama Pelanggan'
        //         },
        //         {
        //             name: 'tgl_pembelian',
        //             label: 'Tanggal Pembelian'
        //         }
        //     ],
        //     caption : 'Users Grid',
        //   height: 'auto',
        //   rowNum: 5,
        //   pager: '#pager'
        // });
        
        // menampilkan data ke browser dari data json
        $("#grid_id").jqGrid({
        datatype: 'json',
        url : 'data.json',
        colModel: [
            {name: 'judul.nomorInvoice', label : 'Nomor Invoice'},
            {name: 'judul.namaPelanggan', label : 'Nama Pelanggan'},
            {name: 'judul.tanggalPembelian', label : 'Tanggal Pembelian'}

        ]
        });


       
    </script>
</body>

</html>

