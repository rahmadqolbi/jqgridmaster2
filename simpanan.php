<?php 
require "db.php";
global $koneksi;

?>

<?php 

   



    // sorting
    // konsep sorting 
    //• Sorting sebagai pengurutan data 
    // yang mana data sebelumnya tersusun secara acak menjadi data yang tersusun secara
    // teratur dengan aturan tertentu.
    // • misalnya dari data yang kecil ke yang lebih besar disebut Ascending 
    //dan yang menurun disebut dengan Descending


     
    //langkah pertama untuk sorting adalah simpan/tangkap halaman yang mau di sorting
  
    
    // // Koneksi ke database
    // $koneksi = mysqli_koneksiect('localhost','root','','jqgrid') or die(mysqli_error($koneksi));
    
    // // Query untuk menghitung jumlah data
    // $search = "SELECT COUNT(*) AS count FROM penjualan";
   
    // $result = mysqli_query($koneksi, $search);
    // $row = mysqli_fetch_array($result);
    // $count = $row['count']; 
    
    // // // Menghitung total halaman
    // if ($count > 0) {
    //   $total_pages = ceil($count/$limit);
    // } else {
    //   $total_pages = 0;
    // }
    
    // // // Mencegah nilai negatif pada $limit
    // if ($limit < 0) {
    //   $limit = 0; 
    // }
    
    // // // Mencari offset
    // $start = $limit * $page - $limit; 
    // if ($start < 0) {
    //   $start = 0;
    // }
    
    // // // Query untuk mendapatkan data
    // $sql = "SELECT * FROM penjualan ORDER BY $sidx $sord LIMIT $start, $limit";
    // $result = mysqli_query($koneksi, $sql);
    
    // // Memasukkan data ke dalam array
    // $sorting = [];
    // $i = 0;
    // while ($row = mysqli_fetch_assoc($result)) {
    //   $sorting[$i]['id'] = $row['id'];
    //   $sorting[$i]['no_invoice'] = $row['no_invoice'];
    //   $sorting[$i]['tgl_pembelian'] = $row['tgl_pembelian'];
    //   $sorting[$i]['nama_pelanggan'] = $row['nama_pelanggan'];
    //   $sorting[$i]['jenis_kelamin'] = $row['jenis_kelamin'];
    //   $sorting[$i]['saldo'] = $row['saldo'];
    //   $i++;
    // }
    // // Menyusun array output
    // $pagination = [];
    // $pagination['page'] = $page;
    // $pagination['total'] = $total_pages;
    // $pagination['records'] = $count;
    // $pagination['rows'] = $sorting;
    // $select = "SELECT * FROM penjualan";
    // $hasil3 = mysqli_query($koneksi, $select);
    // $json1 = 
    // if($hasil3){
    //   while($baris = mysqli_fetch_assoc($hasil3)){
    //     $json1[] = $baris;
    //   }
    // }
//     $page = isset($_REQUEST['page']) ? $_REQUEST['page'] : 1;
// $limit = isset($_REQUEST['rows']) ? $_REQUEST['rows'] : 10;
// $sidx = isset($_REQUEST['sidx']) ? $_REQUEST['sidx'] : '';
// $sord = isset($_REQUEST['sord']) ? $_REQUEST['sord'] : '';
// $offset = ($page - 1) * $limit;
// $query = "SELECT * FROM penjualan ORDER BY $sidx $sord LIMIT $limit OFFSET $offset";
// $result = mysqli_query($koneksi, $query);
// $queryCount = "SELECT COUNT(*) AS count FROM penjualan";
// $countResult = mysqli_query($koneksi, $queryCount);
// $countRow = mysqli_fetch_assoc($countResult);
// $count = $countRow['count'];
// $total_pages = ceil($count / $limit);

// $rows = array();
// while ($row = mysqli_fetch_assoc($result)) {
//   $rows[] = $row;
// }

// $rest = array();
// $rest['page'] = $page;
// $rest['total'] = $total_pages;
// $rest['records'] = $count;
// $rest['rows'] = $rows;

$page = $_GET['page']; // get the requested page
$limit = $_GET['rows']; // get how many rows we want to have into the grid
$sidx = $_GET['sidx']; // get index row - i.e. user click to sort
$sord = $_GET['sord']; // get the direction


$result = mysqli_query($koneksi,"SELECT COUNT(*) AS count FROM penjualan");
$row = mysqli_fetch_array($result);
$count = $row['count'];

// echo "count: $count<br>";
if( $count > 0 ) { 
  $total_pages = ceil($count/$limit);
//$total_pages = ceil($count/1);
} else {
  $total_pages = 0; 
} 
// echo "total page: $total_pages<br>";
if ($page > $total_pages) 
{
  $page=$total_pages; 
}

$start = $limit*$page - $limit; 

$responce = new stdClass();

// global search
$query = "SELECT * FROM penjualan";
$query_result = "SELECT COUNT(*) AS count FROM penjualan";
// $querySearch = "SELECT * FROM penjualan";
// $query_resultSearch = "SELECT COUNT(*) AS count FROM penjualan";


if(isset($_GET['global_search']))
{
  $search = $_GET['global_search'];
  $query .= " WHERE id LIKE '%$search%' OR no_invoice LIKE '%$search%' OR tgl_pembelian LIKE '%$search%' OR nama_pelanggan LIKE '%$search%' OR jenis_kelamin LIKE '%$search%' OR saldo LIKE '%$search%' ";
  $query_result .= " WHERE id LIKE '%$search%' OR no_invoice LIKE '%$search%' OR tgl_pembelian LIKE '%$search%' OR nama_pelanggan LIKE '%$search%' OR jenis_kelamin LIKE '%$search%' OR saldo LIKE '%$search%'";

  // filter dari pencarian
  // if(isset($_GET['global_search']) && $_GET['_search'] == 'true')
  // {
  //   $filter = json_decode($_GET['filters'], true);

  //   if(!empty($filter['rules'])) 
  //   {
  //     $i = 1;
  //     foreach ($filter['rules'] as $rules) {
  //       if ($i === 1) {
  //         $query .= " WHERE $rules[field] LIKE '%$rules[data]%' ";
  //         $query_result .= " WHERE $rules[field] LIKE '%$rules[data]%'";
  //       } else {
  //         $query .= " AND $rules[field] LIKE '%$rules[data]%'";
  //         $query_result .= " AND $rules[field] LIKE '%$rules[data]%'";
  //       }

  //       $i++;
  //     }
  //   }
  // }
  // $query .= " ORDER BY $sidx $sord";
  // $query_result .= " ORDER BY $sidx $sord";
  // $result = mysqli_query($koneksi, $query_result);
  // if (!$result) {
  //   die("Query error: " . mysqli_error($koneksi));
  // }
  // $row = mysqli_fetch_array($result);
  // $count = $row['count'];
  // if( $count > 0 ) { 
  //   $total_pages = ceil($count/$limit);
  // } else {
  //   $total_pages = 1; 
  // } 

  // if ($page > $total_pages) {
  //   $page=$total_pages; 
  // }

  // $start = $limit*$page - $limit; 
  // //letak salahnya
  // // $query .= " LIMIT $start , $limit";
  // $sql = mysqli_query($koneksi, $query);

  // if (!$sql) {
  //   die("Query error: " . mysqli_error($koneksi));
  // }
  // $responce = new stdClass();
}








// filters
if($filters = isset($_GET['filters']) ? json_decode($_GET['filters'], true) : [])
{
  $filter = json_decode($_GET['filters'], true);

  if(!empty($filter['rules'])) 
  {
    $i = 1;
    foreach ($filter['rules'] as $rules) {
      if ($i === 1) {
        $query .= " WHERE $rules[field] LIKE '%$rules[data]%' ";
        $query_result .= " WHERE $rules[field] LIKE '%$rules[data]%'";
      } else {
        $query .= " AND $rules[field] LIKE '%$rules[data]%'";
        $query_result .= " AND $rules[field] LIKE '%$rules[data]%'";
      }

      $i++;
    }
  }
}
// pagination filters
  $sql = mysqli_query($koneksi, $query);
  $result = mysqli_query($koneksi, $query_result);
  $row = mysqli_fetch_array($result);
  $count = $row['count'];
  if( $count > 0 ) { 
    $total_pages = ceil($count/$limit);
  } else {
    $total_pages = 1; 
  } 
  if ($page > $total_pages) 
  {
    $page=$total_pages; 
  }

  $start = $limit*$page - $limit; 

  $responce = new stdClass();
  


if($_GET['_search'] == 'false' OR empty($_GET['global_search'])) 
{
  $query .= " ORDER BY $sidx $sord LIMIT $start , $limit";
  // $query_result .= " ORDER BY $sidx $sord LIMIT $start , $limit";
  $sql = mysqli_query($koneksi, $query); 	
  // echo "count: $count<br>";
  if( $count > 0 ) { 
    $total_pages = ceil($count/$limit);
  //$total_pages = ceil($count/1);
  } else {
    $total_pages = 1; 
  } 
  // echo "total page: $total_pages<br>";
  if ($page > $total_pages) 
  {
    $page=$total_pages; 
  }
  $start = $limit*$page - $limit; 

  $responce = new stdClass();
}
 



 

//  $responce->tes = $query;
 $responce->page = $page;
 $responce->total = $total_pages;
 $responce->records = $count; 
 $responce->count = $count;
 $i=0;
 $result = mysqli_query($koneksi, $query);
 if (!$result) {
     die('Query error: ' . mysqli_error($koneksi));
 }
while($row = mysqli_fetch_array($result))
{
   // $data_header[] = $data; 
  $responce->rows[$i]['id']=$row['id'];
  $responce->rows[$i]['cell']=array($row['id'],$row['no_invoice'],date('d-m-Y', strtotime($row['tgl_pembelian'])),$row['nama_pelanggan'],$row['jenis_kelamin'],$row['saldo'],""); $i++;
}

echo json_encode($responce);
                  // $sql = "SELECT * FROM penjualan WHERE id LIKE '%$search_query%'";
                
                  // $result = mysqli_query($koneksi, $sql);
                  // if (mysqli_num_rows($result) > 0) {
                  //     // tampilkan hasil pencarian
                  //     while($row = mysqli_fetch_assoc($result)){
                  //       $row['id'];
                  //       $row['no_invoice'];
                  //     }
                  // } else {
                  //     // tampilkan pesan bahwa tidak ditemukan hasil pencarian
                  //     mysqli_error($koneksi);
             
                 
                 
    
 
   
      
      

      
      
      
     
    
    
      // search
//       $filters = json_decode($_REQUEST['filters'], true);
//       if ($filters) {
//           $rules = $filters['rules'];
//           $search_value = $rules[0]['data'];
//       } else {
//           $search_value = '';
//       }
      
//       $sql = "SELECT * FROM penjualan WHERE id LIKE '%$search_value%' OR no_invoice LIKE '%$search_value%' OR tgl_pembelian LIKE '%$search_value%' OR nama_pelanggan LIKE '%$search_value%' OR jenis_kelamin LIKE '%$search_value%' OR saldo LIKE '%$search_value%'";
//       $result = mysqli_query($koneksi, $sql);
//       $data = array();
//     while ($row = mysqli_fetch_assoc($result)) {
//    $data[] = $row;
//   }
// echo json_encode($data);

// ...
// get data from database



























       
// Koneksi ke database

// Query SQL untuk mencari data


// Eksekusi query


// Menyimpan hasil query dalam array

// Mengembalikan hasil query dalam format JSON
// header("Content-type: application/json");
// echo json_encode($data);
     


// if (! $sidx)
//     $sidx = 1;

// $result = mysqli_query($koneksi, "SELECT COUNT(*) AS count FROM penjualan");
// $row = mysqli_fetch_array($result);

// $count = $row['count'];
// if ($count > 0 && $limit > 0) {
//     $total_pages = ceil($count / $limit);
// } else {
//     $total_pages = 0;
// }
// if ($page > $total_pages)
//     $page = $total_pages;
// $start = $limit * $page - $limit;
// if ($start < 0)
//     $start = 0;

// $paging = "SELECT id, no_invoice, nama_pelanggan FROM penjualan ORDER BY $sidx $sord LIMIT $start , $limit";
// $result = mysqli_query($koneksi, $paging) or die("Couldn't execute query." . mysqli_error($koneksi));

// $i = 0;
// while ($row = mysqli_fetch_array($result)) {
//     $responce->rows[$i]['id'] = $row['id'];
//     $responce->rows[$i]['cell'] = array(
//         $row['id'],
//         $row['no_invoice'],
//         $row['tgl_pembelian'],
//         $row['nama_pelanggan']
//     );
//     $i ++;
// }
// echo json_encode($responce);
        // $page = $_GET['page']; 
        // $limit = $_GET['rows']; 
        // $sidx = $_GET['sidx']; 
        // $sord = $_GET['sord']; 
        
        // if(!$sidx) $sidx =1; 
        
        // $result = mysqli_query($koneksi, "SELECT COUNT(*) AS count FROM penjualan");
        // $row = mysqli_fetch_array($result,MYSQLI_ASSOC);
        // $count = $row['count'];
        
        // if( $count >0 ) {
        //    $total_pages = ceil($count/$limit);
        // } else {
        //    $total_pages = 0;
        // }
        
        // if ($page > $total_pages) $page=$total_pages;
        
        // $start = $limit*$page - $limit; 
        
        // $SQL = "SELECT * FROM penjualan ORDER BY $sidx $sord LIMIT $start , $limit";
        // $result = mysqli_query($koneksi, $SQL ) or die("Couldn't execute query.".mysqli_error());
        
        // $responce->page = $page;
        // $responce->total = $total_pages;
        // $responce->records = $count;
        // $i=0;
        // while($row = mysqli_fetch_array($result,MYSQLI_ASSOC)) {
        //    $responce->rows[$i]['id']=$row['id'];
        //    $responce->rows[$i]['cell']=array($row['id'],$row['no_invoice'],$row['nama_pelanggan']);
        //    $i++;
        // }        
        // echo json_encode($responce);
      


      // if($_GET['sidx'] == $id){
      //   $sql = "SELECT * FROM penjualan where '%$id%'";
      //   $hasil = mysqli_query($koneksi, $sql);
      //   $json = [];
      //   if(mysqli_num_rows($hasil) > 0){
      //     while($baris = mysqli_fetch_assoc($hasil)){
      //       $json[] = $baris;
      //     }
      //   }
      //   echo json_encode($json);
      // }
      

     
      // kode untuk melakukan pagination, sorting, dan pengambilan data dari database
      
      // kode untuk mengirimkan data ke jqGrid dalam format JSON
    





      // $result = mysqli_query($koneksi, "SELECT COUNT(*) AS count FROM penjualan");
      // $row = mysqli_fetch_assoc($result);
      // $count = $row['count'];
      // $limit = $row['rows'];
      // if ($count > 0) {
      //     $total_pages = ceil($count/$limit);
      // } else {
      //     $total_pages = 0;
      // }
      
      // if ($page > $total_pages) {
      //     $page = $total_pages;
      // }
      
      // $start = $limit * $page - $limit;
      
      // $query = "SELECT * FROM penjualan ORDER BY $sidx $sord LIMIT $start, $limit";
      // $result = mysqli_query($koneksi, $query);
      
      // $response = new stdClass;
      // $response->page = $page;
      // $response->total = $total_pages;
      // $response->records = $count;
      
      // $i = 0;
      // while ($row = mysqli_fetch_assoc($result)) {
      //     $response->rows[$i]['id'] = $row['id'];
      //     $response->rows[$i]['cell'] = array($row['id'], $row['no_invoice'], $row['tgl_pembelian'],
      //     $row['nama_pelanggan'], $row['jenis_kelamin'], $row['saldo']);
      //     $i++;
      // }



    // jQuery("#grid_id").jqGrid('filterToolbar', {
    //     stringResult: true,
    // }); 
?>