<?php
$servername = "localhost";
$username = "root";
$password = ""; 
$dbname = "fitnessplus";

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

$fingerprint = $_POST['fingerprint'] ?? '';
if ($fingerprint) {
    $sql = "INSERT INTO members (fingerprint_template) VALUES (?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $fingerprint);
    $stmt->execute();
    echo "Fingerprint saved!";
    $stmt->close();
} else {
    echo "No fingerprint data received.";
}
$conn->close();

// $member_id = $_POST['member_id'] ?? '';
// $fingerprint = $_POST['fingerprint'] ?? '';

// if ($member_id && $fingerprint) {
//     $sql = "SELECT user_id FROM members WHERE user_id = ?";
//     $stmt = $conn->prepare($sql);
//     if ($stmt) {
//         $stmt->bind_param("i", $member_id);
//         $stmt->execute();
//         $stmt->store_result();

//         if ($stmt->num_rows > 0) {
//             $stmt->close();
//             $sql = "UPDATE members SET fingerprint_template = ? WHERE user_id = ?";
//             $stmt = $conn->prepare($sql);
//             if ($stmt) {
//                 $stmt->bind_param("si", $fingerprint, $member_id);
//                 $stmt->execute();
//                 echo "Fingerprint updated!";
//                 $stmt->close();
//             } else {
//                 echo "Error preparing update statement.";
//             }
//         } else {
//             $stmt->close();
//             $sql = "INSERT INTO members (user_id, fingerprint_template) VALUES (?, ?)";
//             $stmt = $conn->prepare($sql);
//             if ($stmt) {
//                 $stmt->bind_param("is", $member_id, $fingerprint);
//                 $stmt->execute();
//                 echo "Fingerprint saved!";
//                 $stmt->close();
//             } else {
//                 echo "Error preparing insert statement.";
//             }
//         }
//     } else {
//         echo "Error preparing select statement.";
//     }
// } else {
//     echo "No member ID or fingerprint data received.";
// }
// $conn->close();
?>