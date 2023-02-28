

<?php 
require "db.php";

    global $koneksi;
    if($_POST['oper'] == 'add'){
        $id = $_POST['id'];
        $no_invoice = isset($_POST['no_invoice']) ? strtoupper($_POST['no_invoice']) : null;
        $nama_pelanggan = strtoupper($_POST['nama_pelanggan']);
        $jenis_kelamin = $_POST['jenis_kelamin'];
        $saldo = $_POST['saldo'];
        $saltik = str_replace('.','',$saldo);
        $tgl_pembelian = $_POST['tgl_pembelian'];
        $newDate = date("Y-m-d", strtotime($tgl_pembelian));
        $insert = "INSERT INTO penjualan(no_invoice,nama_pelanggan,jenis_kelamin,saldo, tgl_pembelian) VALUES ('$no_invoice','$nama_pelanggan','$jenis_kelamin','$saltik','$newDate')";
        $last_id = mysqli_insert_id($koneksi);
        $exeinsert = mysqli_query($koneksi,$insert);
            $response = array();
            if($exeinsert)
            {
            $response['code'] =1;
            $response['message'] = "Success! Data Inserted";
            $response['last_id'] = mysqli_insert_id($koneksi);
            }else{
            $response['code'] =0;
            $response['message'] = "Failed! Data Not Inserted";
            }
        
        // echo json_encode($response);
        
        }elseif($_POST['oper'] == 'edit'){
            $id = $_POST['id'];
            $no_invoice = strtoupper($_POST['no_invoice']);
            $nama_pelanggan = strtoupper($_POST['nama_pelanggan']);
            $jenis_kelamin = $_POST['jenis_kelamin'];
            $saldo = $_POST['saldo'];
            $saltik = str_replace('.','',$saldo);
            $tgl_pembelian = $_POST['tgl_pembelian'];
            $newDate = date("Y-m-d", strtotime($tgl_pembelian));
            $getdata = mysqli_query($koneksi,"SELECT * FROM penjualan WHERE id='$id'");
            $rows = mysqli_num_rows($getdata);
            $update = "UPDATE penjualan SET id='$id', nama_pelanggan='$nama_pelanggan',jenis_kelamin='$jenis_kelamin', saldo='$saltik', tgl_pembelian='$newDate' WHERE id='$id'";
            $exequery = mysqli_query($koneksi,$update);
            $response = array();
            if($rows > 0){
                if($exequery)
                {
                  $response['code'] = 1;
                  $response['message'] = "Updated Success";
                }else{
                  $response['code'] = 0;
                  $response['message'] = "Updated Failed";
                }
              
                
              
            }
        }elseif($_POST['oper'] == 'del'){
$id = $_POST['id'];
$getdata = mysqli_query($koneksi,"SELECT * FROM penjualan WHERE id = '$id'");
$rows = mysqli_num_rows($getdata);

$delete = "DELETE FROM penjualan WHERE id = '$id'";
$exedelete = mysqli_query($koneksi,$delete);

$response = array();
if($rows > 0)
{
  if ($exedelete) {
    $response['code'] = 1;
    $response['message'] = "Deleted Success";
  }else{
    $response['code'] = 0;
    $response['message'] = "Failed to Delete";
  }
}else{
  $response['code'] = 0;
  $response['message'] = "Failed to Delete, data Not Found";
}

$response['no_invoice'] = $no_invoice;

echo json_encode($response);
}


?>

<?php 



?>

<?php 


?>