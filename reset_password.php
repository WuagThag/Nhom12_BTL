<?php
session_start();
require_once 'functions/db_connection.php';

// Bảo vệ trang: chỉ cho phép user đã đăng nhập
if (!isset($_SESSION['user_id'])) {
    $_SESSION['error'] = 'Bạn cần đăng nhập để đổi mật khẩu.';
    header('Location: login.php');
    exit();
}

$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['change_password'])) {
    $current = trim($_POST['current_password'] ?? '');
    $new = trim($_POST['new_password'] ?? '');
    $confirm = trim($_POST['confirm_password'] ?? '');

    if ($current === '' || $new === '' || $confirm === '') {
        $error = 'Vui lòng điền đầy đủ các trường.';
    } elseif ($new !== $confirm) {
        $error = 'Mật khẩu mới và xác nhận không khớp.';
    } elseif (strlen($new) < 6) {
        $error = 'Mật khẩu mới phải có ít nhất 6 ký tự.';
    } else {
        $userId = (int) $_SESSION['user_id'];
        $conn = getDbConnection();
        $stmt = $conn->prepare('SELECT password FROM login WHERE id = ? LIMIT 1');
        $stmt->bind_param('i', $userId);
        $stmt->execute();
        $result = $stmt->get_result();
        if ($result && $result->num_rows > 0) {
            $row = $result->fetch_assoc();
            $dbPassword = $row['password'];
            // Hiện tại project lưu mật khẩu dưới dạng plain text -> so sánh trực tiếp
            if ($current !== $dbPassword) {
                $error = 'Mật khẩu hiện tại không đúng.';
            } else {
                // Cập nhật mật khẩu mới (lưu ý: nên hash password trong production)
                $update = $conn->prepare('UPDATE login SET password = ? WHERE id = ?');
                $update->bind_param('si', $new, $userId);
                if ($update->execute()) {
                    $success = 'Đổi mật khẩu thành công.';
                    $_SESSION['success'] = $success;
                    $update->close();
                    $stmt->close();
                    $conn->close();
                    header('Location: information_user.php');
                    exit();
                } else {
                    $error = 'Lỗi khi cập nhật mật khẩu. Vui lòng thử lại.';
                }
                $update->close();
            }
        } else {
            $error = 'Không tìm thấy tài khoản.';
        }
        if ($stmt) $stmt->close();
        if ($conn) $conn->close();
    }
}

?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Đổi mật khẩu | Anime</title>
    <link rel="stylesheet" href="css/bootstrap.min.css" type="text/css">
    <link rel="stylesheet" href="css/style.css" type="text/css">
</head>
<body>
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

    <section class="normal-breadcrumb set-bg" data-setbg="img/normal-breadcrumb.jpg">
        <div class="container text-center">
            <div class="normal__breadcrumb__text">
                <h2>Đổi mật khẩu</h2>
                <p>Thay đổi mật khẩu để bảo vệ tài khoản của bạn.</p>
            </div>
        </div>
    </section>

    <section class="login spad">
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-lg-6">
                    <div class="login__form">
                        <h3>Đổi mật khẩu</h3>

                        <?php if (!empty($error)): ?>
                            <div class="alert alert-danger"><?= htmlspecialchars($error) ?></div>
                        <?php endif; ?>
                        <?php if (!empty($success)): ?>
                            <div class="alert alert-success"><?= htmlspecialchars($success) ?></div>
                        <?php endif; ?>

                        <form action="reset_password.php" method="POST">
                            <div class="input__item">
                                <input type="password" name="current_password" placeholder="Mật khẩu hiện tại" required>
                                <span class="icon_lock"></span>
                            </div>
                            <div class="input__item">
                                <input type="password" name="new_password" placeholder="Mật khẩu mới" required>
                                <span class="icon_lock"></span>
                            </div>
                            <div class="input__item">
                                <input type="password" name="confirm_password" placeholder="Nhập lại mật khẩu mới" required>
                                <span class="icon_lock"></span>
                            </div>
                            <button type="submit" name="change_password" class="site-btn">Lưu mật khẩu mới</button>
                        </form>

                        <a href="information_user.php" class="forget_pass">Quay lại thông tin cá nhân</a>
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

    <script src="js/jquery-3.3.1.min.js"></script>
    <script src="js/bootstrap.min.js"></script>
</body>
</html>
