<?php

$customers = [
    [
        'name' => 'Hermes',
        'address' => 'Medan'
    ],
    [
        'name' => 'asdhaksd',
        'address' => 'dwadawd'
    ]
];

echo json_encode($customers);

echo '<br>';
$array1 = [
    '0' => [
      'no_invoice' => 'INV0001',
      'product_code' => '1111111',
     ], 
    '1' => [
     'no_invoice' => 'INV0001',
     'product_code' => '1111112',
    ]
   ];
   
     $array2 = [
       '0' => [
         'product_code' => '1111112',
         'free_valie' => 839,
         'count' => 1240
       ],
     ];
  
     var_dump($array1);