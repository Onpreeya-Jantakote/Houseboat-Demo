<!DOCTYPE html>
<html lang="en">
<head>
    <?php require 'server.php'; ?>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <style>
        body {
            font-family: 'Kanit', sans-serif;
            background-color: #f4f4f4;
            margin: 0;
            padding: 0;
            display: flex;
            align-items: center;
            justify-content: center;
            height: 100vh;
            text-align: center;
        }

        .confirmation-container {
            background-color: #fff;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            width: 300px;
        }

        h2, p {
            color: #333;
            margin-bottom: 10px;
        }
        .back-to-home {
            font-family: 'Kanit', sans-serif;
            display: inline-block;
            padding: 10px 20px;
            background-color: #3498db;
            color: #fff;
            text-decoration: none;
            border-radius: 5px;
            transition: background-color 0.3s ease;
        }

        .back-to-home:hover {
            font-family: 'Kanit', sans-serif;
            background-color: #2980b9;
        }
    </style>
    <title>Booking Confirmation</title>
</head>
<body>
    <?php
    $bookingData = $_SESSION["booking_data"];

    $checkIn = $bookingData["check_in"];
    $checkOut = $bookingData["check_out"];
    $amount = $bookingData["amount"];
    $selectedRoom = $bookingData["rm_id"];
    $cusName = $bookingData["cus_name"];
    $cusTel = $bookingData["cus_tel"];

    $sql = $conn->prepare("INSERT INTO detailbooking (rm_id, check_in, check_out, amount, cus_name, cus_tel) VALUES (?, ?, ?, ?, ?, ?)");
    $sql->bind_param('ssssss', $selectedRoom, $checkIn, $checkOut, $amount, $cusName, $cusTel);

    if ($sql->execute()) {
        $detailID = $sql->insert_id;

        $selectSql = "SELECT * FROM detailbooking WHERE rm_id = ?";
        $selectStatement = $conn->prepare($selectSql);
        $selectStatement->bind_param('s', $selectedRoom);

        if ($selectStatement->execute()) {
            $selectResult = $selectStatement->get_result();

            if ($selectResult && $selectResult->num_rows > 0) {
                $data = $selectResult->fetch_assoc();
                ?>
                <div class="confirmation-container">
                    <h2>รายละเอียดการจอง</h2>
                    <p>หมายเลขการจอง : <?php echo $detailID; ?></p>
                    <p>หมายเลขห้อง : <?php echo $selectedRoom; ?></p>
                    <p>วันที่เช็คอิน : <?php echo $checkIn; ?></p>
                    <p>วันที่เช็คเอาท์ : <?php echo $checkOut; ?></p>
                    <p>จำนวนคนเข้าพัก : <?php echo $amount; ?></p>
                    <p>ชื่อ : <?php echo $cusName; ?></p>
                    <p>เบอร์โทร : <?php echo $cusTel; ?></p>
                    <a href="home.php" class="back-to-home">กลับสู่หน้าหลัก</a>
                </div>
                <?php
            } else {
                echo "<p>ไม่พบข้อมูล</p>";
            }
        } else {
            echo "<p>ไม่มีข้อมูลในฐานข้อมูล</p>";
        }

        $selectStatement->close();
    } else {
        echo "<p>เพิ่มข้อมูลไม่สำเร็จ</p>";
    }

    unset($_SESSION["booking_data"]);
    ?>
</body>
</html>
