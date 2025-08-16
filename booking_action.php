<?php
require 'server.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $checkIn = $_POST["check_in"];
    $checkOut = $_POST["check_out"];
    $amount = $_POST["amount"];
    $selectedRoom = $_POST["rm_id"];

    if (isset($_SESSION['cus_user'])) {
        $username = $_SESSION['cus_user'];

        // Use the customer data from the login session
        $cusName = $_SESSION['cus_name'];
        $cusTel = $_SESSION['cus_tel'];

        $_SESSION["booking_data"] = array(
            "rm_id" => $selectedRoom,
            "check_in" => $checkIn,
            "check_out" => $checkOut,
            "amount" => $amount,
            "cus_name" => $cusName,
            "cus_tel" => $cusTel,
        );

        header("Location: detailbooking.php");
        exit();
    } else {
        // Redirect to login page if not logged in
        header("Location: login.php");
        exit();
    }
}
?>
