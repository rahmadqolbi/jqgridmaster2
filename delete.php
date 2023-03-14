<?php 
// query update
require "db.php";

$delete = "DELETE FROM penjualan where no_invoice=$no_invoice";
$exedelete = mysqli_query($koneksi,$delete);

$respose = array();

  if ($exedelete) {
    $respose['code'] = 1;
    $respose['message'] = "Deleted Success";
  }else{
    $respose['code'] = 0;
    $respose['message'] = "Failed to Delete";
  }

echo json_encode($respose);

?>