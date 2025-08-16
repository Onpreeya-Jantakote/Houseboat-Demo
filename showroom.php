<?php require 'server.php'; 
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ห้องพักทั้งหมด</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
   
    <style>
         @import url('https://fonts.googleapis.com/css2?family=Kanit&display=swap');
        body {
            font-family: 'Kanit', sans-serif;
            background-color: #f4f4f4;
        }

        .container {
            margin-top: 50px;
        }

        table {
            width: 100%;
            margin-top: 20px;
            border-collapse: collapse;
        }

        th, td {
            border: 1px solid #ddd;
            padding: 10px;
            text-align: left;
        }

        th {
            background-color: #3498db;
            color: #fff;
        }

        img {
            max-width: 100%;
            height: auto;
        }

        button {
            background-color: #3498db;
            font-family: 'Kanit', sans-serif;
            color: #fff;
            padding: 10px 20px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
        }

        button:hover {
            background-color: #2980b9;
            font-family: 'Kanit', sans-serif;
        }
    </style>
</head>
<body>
    <div class="container">
    <a href="booking.php"><button>จองห้องพัก</button></a>
        <table class="table table-striped">
            <tr>
                <th>หมายเลขห้อง</th>
                <th>ประเภท</th>
                <th>บริการ</th>
                <th>ราคา</th>
                <th>ภาพรวม</th>
            </tr>
            <?php
            $sql = "SELECT room.rm_id, typeroom.tr_name, room.description, room.price
                    FROM room
                    INNER JOIN typeroom ON room.tr_id = typeroom.tr_id";
            $result = mysqli_query($conn, $sql);

            while ($row = mysqli_fetch_array($result)) {
            ?>
                <tr>
                    <td><?= $row["rm_id"] ?></td>
                    <td><?= $row["tr_name"] ?></td>
                    <td><?= nl2br($row["description"]) ?></td>
                    <td><?= number_format($row["price"], 2) ?></td>
                    <td><img src="img/<?= $row["rm_id"] ?>.png" width="250" height="250"></td>
                </tr>
            <?php
            }
            mysqli_close($conn);
            ?>
        </table>
       
    </div>
</body>
</html>
