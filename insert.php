
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

$filters = isset($_POST['filters']) ? json_decode($_POST['filters'], true) : null;
$global_search = isset($_POST['globalSearch']) ? json_decode($_POST['globalSearch'], true) : null;

$position = getWithPosition($no_invoice, $sortfield, $sortorder, $filters, $global_search, $koneksi);
function getWithPosition($no_invoice, $sortfield, $sortorder, $filters, $global_search, $koneksi)
{

    $where = '';
    $global = [];
    if($global_search){
        $global[] = ("no_invoice LIKE '%$global_search%' OR tgl_pembelian LIKE '%$global_search%' OR nama_pelanggan LIKE '%$global_search%' OR jenis_kelamin LIKE '%$global_search%' OR saldo LIKE '%$global_search%' ");
        $where .= ' WHERE ' . implode(' AND ', $global);
    }
    

    if (isset($filters) && isset($filters['rules']) && is_array($filters['rules'])) {
        foreach ($filters['rules'] as $f) {
            $f = (array) $f;
            $sql = sprintf("%s LIKE '%%%s%%'", $f['field'], $f['data']);
            if ($where != '') {
                $where .= ' AND ';
            } else {
                $where .= ' WHERE ';
            }
            $where .= $sql;
        }
    }

    $data = "SELECT temp.position, temp.*
        FROM 
        (
            SELECT @rownum := @rownum + 1 AS position, penjualan.*
            FROM penjualan
            JOIN 
            (
                SELECT @rownum := 0
            ) rownum
            $where
            ORDER BY penjualan.$sortfield $sortorder
        ) temp WHERE temp.no_invoice = '$no_invoice' LIMIT 1";

    $result = mysqli_query($koneksi, $data);
    if (!$result) {
        die('Query error: ' . mysqli_error($koneksi));
    }
    $post = mysqli_fetch_assoc($result);
    $pos = intval($post['position']);
    return $pos;
}


$response = ['no_invoice' => $no_invoice, 'position' => $position];
echo json_encode($response);

?>

