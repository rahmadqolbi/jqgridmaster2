<?php
require "db.php";
// $no_invoice = $_POST['no_invoice'] ?? '';
// $sidx = $_POST['sidx'] ?? '';
// $sord = $_POST['sord'] ?? '';
// $oper = $_POST['oper'] ?? '';

$no_invoice = $_POST['no_invoice'];
$sortfield = $_POST['sidx'];
$sortorder = $_POST['sord'];
$data = array(
    'no_invoice' => $no_invoice,
    'sidx' => $sortfield,
    'sord' => $sortorder
);
$position = getWithPosition($no_invoice, $sortfield, $sortorder, $koneksi);
	
function getWithPosition($no_invoice, $sortfield, $sortorder, $koneksi)
{
    $data = mysqli_query($koneksi, "SELECT temp.position, temp.*
    FROM 
    (
        SELECT @rownum := @rownum + 1 AS position, penjualan.*
        FROM penjualan
        JOIN 
        (
            SELECT @rownum := 0
        ) rownum ORDER BY penjualan.$sortfield $sortorder
    ) temp WHERE temp.no_invoice = '". $no_invoice ."'");

    if (!$data) {
        die('Query error: ' . mysqli_error($koneksi));
    }

    $post = mysqli_fetch_assoc($data);
$pos = intval($post['position']);
return $pos;
var_dump($pos);
die;
    
}

$response = ['no_invoice' => $no_invoice, 'position' => (int)$position];
echo json_encode($response);


//koneksi ke database

// //mendapatkan parameter dari add_header.php
// $no_invoice = $_POST['no_invoice'];
// $sortfield = $_POST['sidx'];
// $sortorder = $_POST['sord'];
// // $rows = $_POST['rows'];

// //query untuk mengambil data dari database
// $query = "SELECT * FROM penjualan WHERE no_invoice < '$no_invoice' ORDER BY $sortfield $sortorder";
// $result = mysqli_query($koneksi, $query);

// //menghitung posisi data yang baru saja ditambahkan
// $position = mysqli_num_rows($result) + 1;

// //mengembalikan posisi data ke add_header.php
// echo json_encode(array('position' => $position));

?>
