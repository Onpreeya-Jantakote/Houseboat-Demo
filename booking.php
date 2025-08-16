<!DOCTYPE html>
<html lang="en">
<head>
    <?php require 'server.php'; ?>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Kanit&display=swap');

        body {
            font-family: 'Kanit', sans-serif;
            background-color: #f4f4f4;
            margin: 0;
            padding: 0;
            display: flex;
            align-items: center;
            justify-content: center;
            height: 100vh;
        }

        form {
            background-color: #fff;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            width: 300px;
            text-align: center;
        }

        h1 {
            color: #333;
        }

        div {
            margin-bottom: 15px;
        }

        label {
            display: block;
            margin-bottom: 5px;
            color: #555;
        }

        input, select {
            width: 100%;
            padding: 8px;
            box-sizing: border-box;
            border: 1px solid #ccc;
            border-radius: 4px;
            margin-top: 5px;
        }

        input[type="submit"] {
            background-color: #3498db;
            font-family: 'Kanit', sans-serif;
            color: #fff;
            cursor: pointer;
        }

        input[type="submit"]:hover {
            font-family: 'Kanit', sans-serif;
            background-color: #2980b9;
        }

        a {
            display: block;
            text-align: center;
            color: #333;
            text-decoration: none;
            margin-top: 15px;
        }

        .greeting {
            color: #333;
            margin-bottom: 15px;
        }
        .greeting-container {
            display: flex;
            align-items: center;
        }
        .login-link {
            background-color: #3498db;
            color: #fff;
            padding: 10px 20px;
            text-decoration: none;
            border-radius: 5px;
            transition: background-color 0.3s ease;
        }

        .login-link:hover {
            background-color: #2980b9;
        }
    </style>
    <title>จองห้องพัก</title>
</head>
<body>
    <?php
    if (isset($_SESSION['logged_in']) && $_SESSION['logged_in']) {
    } else {
        header("Location: login.php");
        exit();
    }
    ?>

    <form name="bookingForm" action="booking_action.php" method="post">
        <h1>จองห้องพัก</h1>
        <div>
            <label for="rm_id">หมายเลขห้อง</label>
            <select name="rm_id" id="rm_id" required>
                <?php
                $sql = "SELECT rm_id FROM room
                WHERE rm_id NOT IN (SELECT DISTINCT rm_id FROM detailbooking)";
                $result = mysqli_query($conn, $sql);

                while ($row = mysqli_fetch_assoc($result)) {
                    echo "<option value='" . $row['rm_id'] . "'>" . $row['rm_id'] . "</option>";
                }

                mysqli_close($conn);
                ?>
            </select>
        </div>

        <div>
            <label for="check_in">วันที่เช็คอิน</label>
            <input type="date" name="check_in" id="check_in" required>
        </div>
        <div>
            <label for="check_out">วันที่เช็คเอาท์</label>
            <input type="date" name="check_out" id="check_out" required>
        </div>
        <div>
            <label for="amount">จำนวนคนเข้าพัก</label>
            <select name="amount" id="amount" required>
                <option value="1">1</option>
                <option value="2">2</option>
                <option value="3">3</option>
                <option value="4">4</option>
            </select>
        </div>
        <div>
            <input type="submit" value="จองห้องพัก">
        </div>
        <a href="home.php" class="login-link">กลับสู่หน้าหลัก</a>
    </form>
</body>
</html>
