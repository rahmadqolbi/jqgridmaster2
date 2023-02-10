<?php 
require "db.php";

    global $koneksi;
            $no_invoice = $_POST['no_invoice'];
            $nama_pelanggan = $_POST['nama_pelanggan'];
            $tgl_pembelian = $_POST['tgl_pembelian'];
    
    // query insert
    $sql = "INSERT INTO penjualan (no_invoice, nama_pelanggan, tgl_pembelian) VALUES 
    ('$no_invoice', '$nama_pelanggan', '$tgl_pembelian');";
 
    $hasil = mysqli_query($koneksi, $sql);
    $response[] = $hasil;
    if($hasil){
       $response['code'] = 1;
       $response['message'] = 'Success data insert';
    }else{
        $response['code'] =0;

        $response['message'] = "Failed! Data Not Inserted";
    }
    echo json_encode($response);



?>