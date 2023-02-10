<?php 
require "db.php";
global $koneksi;

?>

<?php 


// id, no_invoice, tgl_pembelian, nama_pelanggan, jenis_kelamin, saldo


    // $id = $_GET['sidx'];
    //   if($_GET['sord'] == 'asc'){
    //     $query = "SELECT * FROM penjualan ORDER BY $id asc";
    //     $hasil = mysqli_query($koneksi, $query);
    //     $json3 = [];
    //     if(mysqli_num_rows($hasil) > 0){
    //         while($baris = mysqli_fetch_assoc($hasil)){
    //           $json3[] = $baris;
    //         }
    //       }
    //       echo json_encode($json3);
    //   }elseif($_GET['sord'] == 'desc'){
    //     $query = "SELECT * FROM penjualan ORDER BY $id desc";
    //     $hasil = mysqli_query($koneksi, $query);
    //     $json3 = [];
    //     if(mysqli_num_rows($hasil) > 0){
    //         while($baris = mysqli_fetch_assoc($hasil)){
    //           $json3[] = $baris;
    //         }
    //       }
    //       echo json_encode($json3);
    //   }
    // sorting
    // konsep sorting 
    //• Sorting sebagai pengurutan data 
    // yang mana data sebelumnya tersusun secara acak menjadi data yang tersusun secara
    // teratur dengan aturan tertentu.
    // • misalnya dari data yang kecil ke yang lebih besar disebut Ascending 
    //dan yang menurun disebut dengan Descending


    //langkah pertama untuk sorting adalah simpan/tangkap halaman yang mau di sorting
      $page = $_REQUEST['page']; //untuk menyimpan nilai halaman yang diminta melalui HTTP parameter page
      $limit = $_REQUEST['rows']; //untuk menyimpan nilai halaman berapa baris dari data tabel
      $sidx = $_REQUEST['sidx']; //sidx digunakan untuk menentukan kolom yang akan diurutkan
      $sord = $_REQUEST['sord']; //sord digunakan untuk menentukan urutan pengurutan (ascending atau descending)
      //ascending itu urutan dari atas kebawah 
      //descending itu urutan dari bawah ke atas


      // ==============================================================================
      //langkah kedua lakukan pengujian terhadap nilai yang ditangkap sebagai parameter
      //===============================================================================
      if(!$sidx) $sidx =1; //Jika variabel $sidx tidak memiliki nilai, maka berikan nilai 1 pada variabel $sidx. Ini memastikan bahwa $sidx memiliki nilai minimum 1, agar tidak akan terjadi data yang diurut Nol. 
      // bagaimana kalau jika $sidx 0 kalau nilainya 0 maka program sorting tidak akan melakukan eksekusi di karenakan datanya kosong. bagaimana jika mau sorting kalau datanya saja kosong
      
      $totalrows = isset($_REQUEST['totalrows']) ? $_REQUEST['totalrows']: false;
      if($totalrows) {
        $limit = $totalrows;
      }
      //digunakan untuk memeriksa apakah nilai dari variabel $totalrows memiliki nilai atau tidak. Jika $totalrows memiliki nilai, maka nilai dari $totalrows akan ditetapkan sebagai nilai dari variabel $limit


      // dengan adanya pengujian terhadap $totalrows ini memiliki nilai yang sama dengan $limit
      $dbhost = 'localhost';
      $dbuser = 'jqgrid';
      $dbpassword = '';

      $db = mysqli_connect('localhost','root','','jqgrid') or die(mysqli_error($db));
      
      $search = "SELECT COUNT(*) AS count FROM penjualan";
      // query diatas itu artinya menghitung seluruh jumlah dari tabel penjualan
      $result = mysqli_query($db, $search);
      $row = mysqli_fetch_array($result);
      $count = $row['count']; //$count tempat simpan yang isinya adalah untuk menghitung jumlah baris
      if( $count >0 ) {
        $total_pages = ceil($count/$limit);
      } else {
        $total_pages = 0;
      }
      // jika $count/ data lebih besar dari 0 maka lakukan eksekusi true
      //ceil untuk membulatkan bilangan atas kesuatu bilangan
      //$count adalah jumlah baris
      //$limit adalah batas jumlah baris yang akan ditampilkan per halaman
      //$total pages adalah seluruh baris yang akan ditampilkan per halaman
      if ($page > $total_pages) $page=$total_pages; //$page = $total_pages
      if ($limit<0) $limit = 0; 
      $start = $limit*$page - $limit; 
      if ($start<0) $start = 0;
      $SQL = "SELECT * FROM penjualan ORDER BY $sidx $sord LIMIT $start , $limit";
      $result = mysqli_query($db,$SQL );
      $data = new stdClass();
      $data->page = $page;
      $data->total = $total_pages;
      $data->records = $count;
      $i=0;
      while($row = mysqli_fetch_array($result)) {
        $data->rows[$i]['id']=$row['id'];
        $data->rows[$i]['cell']=array($row['id'],$row['no_invoice'],$row['tgl_pembelian'], $row['nama_pelanggan'], $row['jenis_kelamin'], $row['saldo']);
          $i++;
      } 
      // echo json_encode($responce);
     


      // pagination
      // Pagination adalah proses membagi data menjadi beberapa halaman*
      // sehingga lebih mudah untuk ditampilkan dan dikelola. gunanya untuk meningkatkan kecepatan. jadi
      // pagination dalam server side hanya data yang dibutuhkan per halamanan untuk di load
      //konsep membuat pagination
//       Terima request dari client, termasuk informasi tentang halaman saat ini dan jumlah data per halaman.

// Terima data dari database atau sumber data lainnya.

// Hitung jumlah total halaman yang dibutuhkan. Ini bisa dilakukan dengan membagi jumlah total data dengan jumlah data per halaman.

// Ambil hanya data yang dibutuhkan untuk halaman saat ini
      if (isset($_GET['pagenum']) && isset($_GET['pagesize'])) {
        $pagenum = $_GET['pagenum'];
        $pagesize = $_GET['pagesize'];
      } else {
        $pagenum = 0;
        $pagesize = 10;
      }
      $start = $pagenum * $pagesize;
      //pagenum menentukan nomor halaman yang saat ini ditampilkan
      //pagesize menentukan jumlah data yang ditampilkan per halaman
      if (isset($mysqli) && $mysqli instanceof mysqli) {
        $query = "SELECT SQL_CALC_FOUND_ROWS id, no_invoice, tgl_pembelian, nama_pelanggan, jenis_kelamin, saldo FROM penjualan LIMIT $start, $pagesize";
        // limit digunakan untuk membatasi jumlah data yang diambil, dengan nilai $start sebagai offset dan $pagesize sebagai jumlah data yang diambil
        // SQL_CALC_FOUND_ROWS digunakan untuk memastikan jumlah baris yang ditemukan dalam tabel (termasuk baris yang tidak diambil) akan dapat ditemukan setelah query dieksekusi.
        $result = $mysqli->prepare($query);
        $result->bind_param('ii', $start, $pagesize);
        $result->execute();
        /* bind result variables */
        $result->bind_result($id, $no_invoice, $tgl_pembelian, $nama_pelanggan, $jenis_kelamin, $saldo);
        /* fetch values */
        while ($result->fetch())
        {
          $customers[] = array(
            'id' => $id,
            'no_invoice' => $no_invoice,
            'tgl_pembelian' => $tgl_pembelian,
            'nama_pelanggan' => $nama_pelanggan,
            'jenis_kelamin' => $jenis_kelamin,
            'saldo' => $saldo
          );
        }

        $result = $mysqli->prepare("SELECT FOUND_ROWS()");
        $result->execute();
        $result->bind_result($total_rows);
        $result->fetch();
        $data[] = array(
          'TotalRows' => $total_rows,
          'Rows' => $customers
        );

        
        // echo json_encode($data);
        // kode dibawah adalah untuk searching filter toolbars
      }elseif($filters = isset($_REQUEST['filters']) ? json_decode($_REQUEST['filters'], true) : []){
        $filterData = $filters;
        if (isset($filterData['rules']) && !empty($filterData['rules'])) {
            $groupOp = $filters['groupOp'];
            $rules = $filters['rules'];
            $query = "SELECT * FROM `penjualan` WHERE ";
            $i = 0;
            foreach ($rules as $rule) {
                $field = $rule['field'];
                $op = $rule['op'];
                $data = $rule['data'];
                if ($op == "eq") {
                    $op = "=";
                }
                if ($i == 0) {
                  $query .= "`$field` LIKE '%$data%'";
              } else {
                  $query .= " $groupOp `$field` LIKE '%$data%'";
              }
                $i++;
            }
            
            $result = mysqli_query($koneksi, $query);
            $data = array();
            while ($row = mysqli_fetch_assoc($result)) {
                $data[] = $row;
            }
            
            
        }
        
    }
    echo json_encode($data);
      
      
      
      
      
      
     
    
    
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