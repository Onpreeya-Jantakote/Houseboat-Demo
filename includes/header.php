<?php
/**
 * Common Header Template
 */
if (!defined('BASE_URL')) {
    require_once __DIR__ . '/../config/config.php';
}
require_once __DIR__ . '/functions.php';
?>
<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= isset($pageTitle) ? e($pageTitle) . " - " . SITE_NAME : SITE_NAME ?></title>
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous">
    <!-- Google Fonts: Kanit -->
    <link href="https://fonts.googleapis.com/css2?family=Kanit:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <!-- Font Awesome 6 -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css"/>
    <!-- Swiper CSS -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.css"/>
    
    <style>
        * {
            font-family: 'Kanit', sans-serif;
        }
        body {
            background-color: #f8f9fa;
            color: #333;
        }
        .custom-bg {
            background-color: #1a237e;
        }
        .custom-bg:hover {
            background-color: #111755;
        }
        .availability-form {
            margin-top: -50px;
            z-index: 2;
            position: relative;
        }
        @media screen and (max-width: 575px) {
            .availability-form {
                margin-top: 25px;
                padding: 0 15px;
            }
        }
        .map-container {
            overflow: hidden;
            padding-bottom: 56.25%;
            position: relative;
            height: 0;
        }
        .map-container iframe {
            left: 0;
            top: 0;
            height: 100%;
            width: 100%;
            position: absolute;
        }
        .room-img {
            height: 220px;
            object-fit: cover;
            width: 100%;
        }
        .navbar-brand img {
            height: 40px;
            width: auto;
        }
    </style>
</head>
<body>
<?php require_once __DIR__ . '/navbar.php'; ?>
<div class="container mt-3">
    <?php renderFlashMessages(); ?>
</div>
