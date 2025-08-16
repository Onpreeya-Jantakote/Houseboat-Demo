<?php session_start(); ?>
<head>
    <link rel="stylesheet" href="stlyeloginac.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Kanit&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" 
    integrity="sha512-DTOQO9RWCH3ppGqcWaEA1BIZOC6xxalwEsw9c2QQeAIftl+Vegovlnee1c9QX4TctnWMn13TZye+giMm8e2LwA==" 
    crossorigin="anonymous" referrerpolicy="no-referrer" />
</head>
<div class="container bg">
        <div class="navBar">
            <div class="logo">
            <a href="#">
                <i class="fa-solid fa-mountain-sun"></i>
            </a>
            <a href="#">สวัสดี <?php echo htmlspecialchars($_SESSION['cus_name']); ?></a>
            </div>
                <ul>
                <li><a href="#">หน้าหลัก</a></li>
                <li><a href="#">เกี่ยวกับ</a></li>
                <li><a href="#">ติดต่อ</a></li>
                <div class="logout">
                    <a href="logout.php">ออกจากระบบ</a>
                </div>
                </ul>
            </div>
        <div class="content">
            <div class="houseboat" style="width:50%;">
                <div class="tnx">
                    <h2><i class="fas fa-map-marker-alt"></i>
                    Thailand</h2>
                </div>
                    <div class="txt">
                        <h1>เรือน</h1>
                        <h1>แพร</h1>
                    </div>
                        <p>หลีกหนีความวุ่นวายจากโลกภายนอก ให้ธรรมชาติได้บำบัด พื้นที่สีเขียว 
                            จุดไฮไลท์ไม่ได้มีแค่สายน้ำเรือนแพ ยังมีความอร่อยให้เลือกอีกมากมาย มีหลายโซนไว้บริการลูกค้า 
                            โชนล่องแพ โซนคาเฟ่ และร้านอาหาร
                        </p>
                        <a href="showroom.php"><button >จองเลย !</button></a>
        </div>
            <div class="card" style="width:50%;">
                <a href="https://www.google.com/" target="_blank">
                    <div class="box1">
                    <h2>ห้องพัก</h2>
                    </div>
                </a>
                <a href="https://www.google.com/" target="_blank">
                    <div class="box2">
                    <h2>หมูกะทะ</h2>
                    </div>
                </a>
                <a href="https://www.google.com/" target="_blank">
                    <div class="box3">
                    <h2>คาเฟ่</h2>
                    </div>
                </a>
                <a href="https://www.google.com/" target="_blank">
                    <div class="box4">
                    <h2>ล่องแพ</h2>
                    </div>
                </a>
            </div>
        </div>
        </div>