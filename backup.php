
<?php
    include('../db.php');
	$Invoice = $_GET['Invoice'];
	$sortfield = $_GET['sidx']; 
    $sortorder = $_GET['sord'];
	
	$position = getWithPosition($Invoice, $sortfield, $sortorder, $connect);
	
    function getWithPosition($Invoice, $sortfield, $sortorder, $connect)
	{
		$data = "SELECT temp.position, temp.* 
			FROM (SELECT @rownum := @rownum + 1 AS 
			position, penjualan.* FROM penjualan 
			JOIN (SELECT @rownum := 0) rownum" ;

		$filters = [];
		if(isset($_GET['filter'])) 
		{
			$filters = json_decode($_GET['filter'], true);
			$totalrules = count($filters['rules']); 
			
			if (isset($filters))
			{
				for ($i=0; $i<$totalrules; $i++) 
				{	
					$filterdata = $filters['rules'][$i]["data"];
					$filterfield = $filters['rules'][$i]["field"];

					if ($i == 0) 
					{
						$data .= " WHERE $filterfield LIKE '%$filterdata%'";
					}
					else if ($i > 0) 
					{
						$data .= " AND $filterfield LIKE '%$filterdata%'";
					}
				}
			}
		}

		$globalsearch = [];
		if(isset($_GET['globalsearch']))
		{   
			$globalsearch = $_GET['globalsearch']; 
			if (isset($globalsearch))
			{
				$field = ['Invoice', 'Nama', 'Tgl', 'Jeniskelamin', 'Saldo'];
				for ($i=0; $i<count($field); $i++)
				{
					if ($i == 0)
					{
						$data .= " WHERE $field[$i] LIKE '%$globalsearch%'";
					}
					else if ($i > 0)
					{
						$data .= " OR $field[$i] LIKE '%$globalsearch%'";
					}
				}
			}
		}
		$data .= " ORDER BY penjualan.$sortfield $sortorder
				) temp WHERE temp.Invoice = '". $Invoice ."'";
		
		$filter = mysqli_query($connect, $data);

		$post = mysqli_fetch_assoc($filter);
		$pos = $post['position'];
		return $pos;
	}
	$response = ['Invoice' => $Invoice, 'position' => $position];
    echo json_encode($response);
?>
