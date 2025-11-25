<?php
session_start();
require_once 'functions/db_connection.php';

// Khi người dùng ấn nút Đăng ký
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['register'])) {
    handleRegister();
}

function handleRegister()
{
    $conn = getDbConnection(); // lấy kết nối từ file db_connection.php

    $username = trim($_POST['username'] ?? '');
    $password = trim($_POST['password'] ?? '');
    $confirm_password = trim($_POST['confirm_password'] ?? '');
    $display_name = trim($_POST['display_name'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $phone = trim($_POST['phone'] ?? '');
    $birthday = trim($_POST['birthday'] ?? '');

    // Kiểm tra dữ liệu đầu vào
    if (empty($username) || empty($password) || empty($confirm_password) || empty($display_name) || empty($email) || empty($phone) || empty($birthday)) {
        $_SESSION['error'] = '⚠️ Vui lòng nhập đầy đủ thông tin!';
        header('Location: register.php');
        exit();
    }

    if ($password !== $confirm_password) {
        $_SESSION['error'] = '❌ Mật khẩu nhập lại không khớp!';
        header('Location: register.php');
        exit();
    }

    // Kiểm tra độ dài mật khẩu (ít nhất 6 ký tự)
    if (strlen($password) < 6) {
        $_SESSION['error'] = '❌ Mật khẩu phải có ít nhất 6 ký tự!';
        header('Location: register.php');
        exit();
    }

    // Kiểm tra username hoặc email đã tồn tại
    $checkStmt = $conn->prepare("SELECT id FROM login WHERE username = ? OR email = ?");
    $checkStmt->bind_param("ss", $username, $email);
    $checkStmt->execute();
    $checkStmt->store_result();

    if ($checkStmt->num_rows > 0) {
        $_SESSION['error'] = '⚠️ Tên đăng nhập hoặc email đã tồn tại!';
        $checkStmt->close();
        $conn->close();
        header('Location: register.php');
        exit();
    }
    $checkStmt->close();

    // ❌ Không mã hóa mật khẩu theo yêu cầu của bạn
    $hashedPassword = $password;

    // Chuẩn bị câu lệnh INSERT
    $stmt = $conn->prepare("INSERT INTO login (username, password, display_name, email, phone, birthday, role) 
                            VALUES (?, ?, ?, ?, ?, ?, 'user')");
    if (!$stmt) {
        $_SESSION['error'] = "❌ Lỗi SQL: " . $conn->error;
        header('Location: register.php');
        exit();
    }

    $stmt->bind_param("ssssss", $username, $hashedPassword, $display_name, $email, $phone, $birthday);

    if ($stmt->execute()) {
        $_SESSION['success'] = '✅ Đăng ký thành công! Hãy đăng nhập.';
    } else {
        $_SESSION['error'] = '❌ Lỗi khi đăng ký: ' . $stmt->error;
    }

    $stmt->close();
    $conn->close();
    header('Location: register.php');
    exit();
}
?>
<!DOCTYPE html>
<html lang="vi">

<head>
    <meta charset="UTF-8">
    <meta name="description" content="Anime Template">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Đăng ký | Anime</title>

    <!-- Google Font -->
    <link href="https://fonts.googleapis.com/css2?family=Oswald:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Mulish:wght@300;400;500;600;700;800;900&display=swap" rel="stylesheet">

    <!-- Css Styles -->
    <link rel="stylesheet" href="css/bootstrap.min.css" type="text/css">
    <link rel="stylesheet" href="css/font-awesome.min.css" type="text/css">
    <link rel="stylesheet" href="css/elegant-icons.css" type="text/css">
    <link rel="stylesheet" href="css/style.css" type="text/css">
</head>

<body>
    <!-- Header -->
    <header class="header">
        <div class="container">
            <div class="row">
                <div class="col-lg-2">
                    <div class="header__logo">
                        <a href="./index.php"><img src="img/logo.png" alt=""></a>
                    </div>
                </div>
                <div class="col-lg-8">
                    <nav class="header__menu mobile-menu">
                        <ul>
                            <li><a href="./index.php">Trang chủ</a></li>
                            <li><a href="./categories.php">Thể loại</a></li>
                            <li><a href="./blog.php">Blog</a></li>
                            <li><a href="#">Liên hệ</a></li>
                        </ul>
                    </nav>
                </div>
                <div class="col-lg-2">
                    <div class="header__right">
                        <a href="#" class="search-switch"><span class="icon_search"></span></a>
                        <a href="./login.php"><span class="icon_profile"></span></a>
                    </div>
                </div>
            </div>
            <div id="mobile-menu-wrap"></div>
        </div>
    </header>

    <!-- Breadcrumb -->
    <section class="normal-breadcrumb set-bg" data-setbg="img/normal-breadcrumb.jpg">
        <div class="container text-center">
            <div class="normal__breadcrumb__text">
                <h2>Đăng ký</h2>
                <p>Tạo tài khoản mới để tham gia Anime Blog!</p>
            </div>
        </div>
    </section>

    <!-- Register Section -->
    <section class="login spad">
        <div class="container">
            <div class="row">
                <div class="col-lg-6">
                    <div class="login__form">
                        <h3>Đăng ký tài khoản</h3>

                        <?php if (isset($_SESSION['error'])): ?>
                            <div class="alert alert-danger"><?= $_SESSION['error']; unset($_SESSION['error']); ?></div>
                        <?php elseif (isset($_SESSION['success'])): ?>
                            <div class="alert alert-success"><?= $_SESSION['success']; unset($_SESSION['success']); ?></div>
                        <?php endif; ?>

                        <form action="register.php" method="POST">
                            <div class="input__item">
                                <input type="text" name="username" placeholder="Tên đăng nhập" required>
                                <span class="icon_profile"></span>
                            </div>
                            <div class="input__item">
                                <input type="text" name="display_name" placeholder="Tên hiển thị (khi đăng nhập)" required>
                                <span class="icon_profile"></span>
                            </div>
                            <div class="input__item">
                                <input type="email" name="email" placeholder="Gmail" required>
                                <span class="icon_mail"></span>
                            </div>
                            <div class="input__item">
                                <input type="text" name="phone" placeholder="Số điện thoại" required>
                                <span class="icon_phone"></span>
                            </div>
                            <div class="input__item">
                                <input type="date" name="birthday" required>
                                <span class="icon_calendar"></span>
                            </div>
                            <div class="input__item">
                                <input type="password" name="password" placeholder="Mật khẩu" required minlength="6">
                                <span class="icon_lock"></span>
                            </div>
                            <div class="input__item">
                                <input type="password" name="confirm_password" placeholder="Nhập lại mật khẩu" required minlength="6">
                                <span class="icon_lock"></span>
                            </div>
                            <button type="submit" name="register" class="site-btn">Đăng ký ngay</button>
                        </form>

                        <a href="login.php" class="forget_pass">Đã có tài khoản? Đăng nhập ngay</a>
                    </div>
                </div>

                <div class="col-lg-6">
                    <div class="login__register">
                        <h3>Chào mừng đến Anime Blog</h3>
                        <p>Hãy tham gia cộng đồng và chia sẻ đam mê của bạn cùng mọi người!</p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Footer Section Begin -->
    <footer class="footer">
        <div class="page-up">
            <a href="#" id="scrollToTopButton"><span class="arrow_carrot-up"></span></a>
        </div>
        <div class="container">
            <div class="row">
                <div class="col-lg-3">
                    <div class="footer__logo">
                        <a href="./index.php"><img src="img/logo.png" alt=""></a>
                    </div>
                </div>
                <div class="col-lg-6">
                    <div class="footer__nav">
                        <ul>
                            <li class="active"><a href="./index.php">Homepage</a></li>
                            <li><a href="./categories.php">Categories</a></li>
                            <li><a href="./blog.php">Our Blog</a></li>
                            <li><a href="./contacts.php">Contacts</a></li>
                        </ul>
                    </div>
                </div>
                <div class="col-lg-3">
                    <p><!-- Link back to Colorlib can't be removed. Template is licensed under CC BY 3.0. -->
                      Copyright &copy;<script>document.write(new Date().getFullYear());</script> All rights reserved | This template is made with <i class="fa fa-heart" aria-hidden="true"></i> by <a href="https://colorlib.com" target="_blank">Colorlib</a>
                      <!-- Link back to Colorlib can't be removed. Template is licensed under CC BY 3.0. --></p>

                  </div>
              </div>
          </div>
      </footer>
      <!-- Footer Section End -->

    <!-- Js Plugins -->
    <script src="js/jquery-3.3.1.min.js"></script>
    <script src="js/bootstrap.min.js"></script>
</body>
</html>
