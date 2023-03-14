<?php 
require "db.php";
$start = $_GET['start'];
$limit = $_GET['limit'];
$page = $_GET['page'];
$sidx = $_GET['sidx']; 
$sord = $_GET['sord']; 
$filters = isset($_GET['filters']) ? json_decode($_GET['filters'], true) : null;
// $global_search = isset($_GET['global_search']) ? $_GET['global_search'] : null;
$global_search = isset($_GET['global_search']) ? json_decode($_GET['global_search'], true) : null;


// Hitung nilai offset
$offset = $start - 1;
$limit = $limit - $start + 1;
// Menentukan nilai awal untuk variabel $sql
$sql = "SELECT * FROM penjualan ";

if($filters = isset($_GET['filters']) ? json_decode($_GET['filters'], true) : [])
{
  $filters = json_decode($_GET['filters'], true);

  if(!empty($filters['rules'])) 
  {
    $i = 1;
    foreach ($filters['rules'] as $rules) {
      if ($i === 1) {
        $sql .= " WHERE $rules[field] LIKE '%$rules[data]%' ";
      } else {
        $sql .= " AND $rules[field] LIKE '%$rules[data]%'";
      }

      $i++;
    }
  }
}

if (!empty($global_search)) {
 
  $sql .= "no_invoice LIKE '%$global_search%' OR tgl_pembelian LIKE '%$global_search%' OR nama_pelanggan LIKE '%$global_search%' OR jenis_kelamin LIKE '%$global_search%' OR saldo LIKE '%$global_search%'";
}

    
if (!empty($sidx) && !empty($sord)) {
  $sql .= " ORDER BY $sidx $sord";
}

if (isset($page) && isset($limit)) 
{
    $sql .= " LIMIT $offset, $limit";  
}



$data = array();
$hasil = mysqli_query($koneksi, $sql);
if (!$hasil) {
  die("Query error: " . mysqli_error($koneksi));
}

if($hasil) {
while ($baris = mysqli_fetch_array($hasil)) {
  // Ubah format tanggal menjadi 'd-M-Y'
  $tgl_pembelian = date('d-m-Y', strtotime($baris['tgl_pembelian']));
  $saldo = number_format($baris['saldo'], 0, ',', '.');
  
  // Tambahkan data ke dalam array
  $data[] = [
    'no_invoice' => $baris['no_invoice'],
    'tgl_pembelian' => $tgl_pembelian,
    'nama_pelanggan' => $baris['nama_pelanggan'],
    'jenis_kelamin' => $baris['jenis_kelamin'],
    'saldo' =>  $saldo
  ];
  }
}else {
  die("Query error: " . mysqli_error($koneksi));
}

$jsonData = json_encode($data);
// Tampilkan hasil
// echo $jsonData;

// var_dump($global_search);
// var_dump($start, $limit, $page, $offset);
// var_dump($sql);
// var_dump($data);
// die;
?>
<?php require "report/stireport_config.inc"; ?>
<!DOCTYPE html>
<html>
<head>
  <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
  <title>Customers Report</title>
  <link rel="stylesheet" type="text/css" href="stimulsoft-report/2021.3.6/css/stimulsoft.viewer.office2013.whiteblue.css">
  <link rel="stylesheet" type="text/css" href="stimulsoft-report/2021.3.6/css/stimulsoft.designer.office2013.whiteblue.css">
  <script type="text/javascript" src="stimulsoft-report/2021.3.6/scripts/stimulsoft.reports.js"></script>
  <script type="text/javascript" src="stimulsoft-report/2021.3.6/scripts/stimulsoft.viewer.js"></script>
  <script type="text/javascript" src="stimulsoft-report/2021.3.6/scripts/stimulsoft.dashboards.js"></script>
  <script type="text/javascript" src="stimulsoft-report/2021.3.6/scripts/stimulsoft.designer.js"></script>
  <script type="text/javascript">
    function Start() {
      Stimulsoft.Base.StiLicense.loadFromFile("stimulsoft-report/2021.3.6/stimulsoft/license.key");

      var viewer = new Stimulsoft.Viewer.StiViewer(null, "StiViewer", false)
      var report = new Stimulsoft.Report.StiReport()

      var options = new Stimulsoft.Designer.StiDesignerOptions()
      options.appearance.fullScreenMode = true

      // var designer = new Stimulsoft.Designer.StiDesigner(options, "Designer", false)

      var dataSet = new Stimulsoft.System.Data.DataSet("Data")

      viewer.renderHtml('content')
      report.loadFile('report/CustomersList1.mrt')

      report.dictionary.dataSources.clear()

      dataSet.readJson(<?php echo $jsonData ?>)

      report.regData(dataSet.dataSetName, '', dataSet)
      report.dictionary.synchronize()

      viewer.report = report
      designer.renderHtml("content")
      designer.report = report
    }

    function afterPrint() {
      if (confirm('Tutup halaman?')) {
        window.close()
      }
    }
  </script>
</head>
<body onload="Start()" onafterprint="afterPrint()">
  <div id="content"></div>
</body>
</html>
