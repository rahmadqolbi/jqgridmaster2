
<?php

require "db.php";
$no_invoice = $_POST['no_invoice'];
$sortfield = $_POST['sidx'];
$sortorder = $_POST['sord'];

$data = array(
    'no_invoice' => $no_invoice,
    'sidx' => $sortfield,
    'sord' => $sortorder,
);

// $filters = isset($_POST['filters']) ? json_decode($_POST['filters'], true) : null;
// $global_search = isset($_POST['globalSearch']) ? json_decode($_POST['globalSearch'], true) : null;


$filters = isset($_POST['filters']) ? json_decode($_POST['filters'], true) : null;
$global_search = isset($_POST['global_search']) ? $_POST['global_search'] : null;

$position = getWithPosition($no_invoice, $sortfield, $sortorder, $filters, $global_search, $koneksi);

function getWithPosition($no_invoice, $sortfield, $sortorder, $filters, $global_search, $koneksi)
{
    $where = '';

    if (!empty($global_search)) {
        $where .= " AND (no_invoice LIKE '%$global_search%' OR tgl_pembelian LIKE '%$global_search%' OR nama_pelanggan LIKE '%$global_search%' OR jenis_kelamin LIKE '%$global_search%' OR saldo LIKE '%$global_search%')";
    }

    if (!empty($filters)) {
        $rules = isset($filters['rules']) ? $filters['rules'] : null;
        if (!empty($rules)) {
            foreach ($rules as $f) {
                $field = $f['field'];
                $data = $f['data'];
                $where .= " AND $field LIKE '%$data%'";
            }
        }
    }

    $data = "SELECT temp.position, temp.*
             FROM (
                 SELECT @rownum := @rownum + 1 AS position, penjualan.*
                 FROM penjualan
                 JOIN (
                     SELECT @rownum := 0
                 ) rownum
                 WHERE 1=1 $where
                 ORDER BY penjualan.$sortfield $sortorder
             ) temp
             WHERE temp.no_invoice = '$no_invoice' LIMIT 1";

    $result = mysqli_query($koneksi, $data);
    if (!$result) {
        die('Query error: ' . mysqli_error($koneksi));
    }
    $post = mysqli_fetch_assoc($result);
    $pos = intval($post['position']);
    return $pos;
}

$position = getWithPosition($no_invoice, $sortfield, $sortorder, $filters, $global_search, $koneksi);

$response = ['no_invoice' => $no_invoice, 'position' => $position];
echo json_encode($response);

?>




