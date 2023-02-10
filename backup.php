

<?php 
require "db.php";

// $getdata = mysqli_query($koneksi,"SELECT * FROM penjualan WHERE id = '$id'");

// $delete = "DELETE FROM penjualan";
// $exedelete = mysqli_query($koneksi,$delete);

// $respose = array();
// if($respose > 0)
// {
//   if ($exedelete) {
//     $respose['code'] = 1;
//     $respose['message'] = "Deleted Success";
//   }else{
//     $respose['code'] = 0;
//     $respose['message'] = "Failed to Delete";
//   }
// }else{
//   $respose['code'] = 0;
//   $respose['message'] = "Failed to Delete, data Not Found";
// }


    global $koneksi;
    if($_POST['oper'] == 'add'){
        $id = $_POST['id'];
        $no_invoice = $_POST['no_invoice'];
        $nama_pelanggan = $_POST['nama_pelanggan'];
        $tgl_pembelian = $_POST['tgl_pembelian'];

        $insert = "INSERT INTO penjualan(no_invoice,nama_pelanggan,tgl_pembelian) VALUES ('$no_invoice','$nama_pelanggan','$tgl_pembelian')";
        $exeinsert = mysqli_query($koneksi,$insert);
            $response = array();
            if($exeinsert)
            {
            $response['code'] =1;
            $response['message'] = "Success! Data Inserted";
            }else{
            $response['code'] =0;
            $response['message'] = "Failed! Data Not Inserted";
            }
        
        echo json_encode($response);
        
        }elseif($_POST['oper'] == 'edit'){
            $id = $_POST['id'];
            $no_invoice = $_POST['no_invoice'];
            $nama_pelanggan = $_POST['nama_pelanggan'];
            $tgl_pembelian = $_POST['tgl_pembelian'];
    
            $getdata = mysqli_query($koneksi,"SELECT * FROM penjualan WHERE id='$id'");
            $rows = mysqli_num_rows($getdata);
            $update = "UPDATE penjualan SET id='$id', nama_pelanggan='$nama_pelanggan',tgl_pembelian='$tgl_pembelian' WHERE id='$id'";
            $exequery = mysqli_query($koneksi,$update);
            $respose = array();
            if($rows > 0){
                if($exequery)
                {
                  $respose['code'] = 1;
                  $respose['message'] = "Updated Success";
                }else{
                  $respose['code'] = 0;
                  $respose['message'] = "Updated Failed";
                }
              
                echo json_encode($respose);
              
            }
        }elseif($_POST['oper'] == 'del'){
$id = $_POST['id'];
$getdata = mysqli_query($koneksi,"SELECT * FROM penjualan WHERE id = '$id'");
$rows = mysqli_num_rows($getdata);

$delete = "DELETE FROM penjualan WHERE id = '$id'";
$exedelete = mysqli_query($koneksi,$delete);

$respose = array();
if($rows > 0)
{
  if ($exedelete) {
    $respose['code'] = 1;
    $respose['message'] = "Deleted Success";
  }else{
    $respose['code'] = 0;
    $respose['message'] = "Failed to Delete";
  }
}else{
  $respose['code'] = 0;
  $respose['message'] = "Failed to Delete, data Not Found";
}


echo json_encode($respose);
}

      




 
        







// // if($rows === 1){
// //     $id = $_POST['id'];



// delete 

?>