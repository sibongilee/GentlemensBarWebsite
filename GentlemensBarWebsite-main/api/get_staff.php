<?php
// api/get_staff.php
header('Content-Type: application/json');

// Define your barbers list
$staff_members = [
    [
        'id' => 1,
        'name' => 'Barber 1'
    ],
    [
        'id' => 2,
        'name' => 'Barber 2'
    ]
];

// Return the data as a JSON response
echo json_encode($staff_members);
exit();
?>