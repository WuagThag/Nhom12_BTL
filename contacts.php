<?php
session_start();
require_once 'functions/db_connection.php';

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['send_contact'])) {
    $name = trim($_POST['name'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $message = trim($_POST['message'] ?? '');

    // Basic validation
    if ($name === '' || $email === '' || $message === '') {
        $_SESSION['contact_error'] = 'Vui lòng nhập đầy đủ tên, email và nội dung phản hồi.';
        header('Location: contacts.php');
        exit();
    }
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $_SESSION['contact_error'] = 'Email không hợp lệ.';
        header('Location: contacts.php');
        exit();
    }
    if (mb_strlen($message) > 5000) {
        $_SESSION['contact_error'] = 'Nội dung quá dài (tối đa 5000 ký tự).';
        header('Location: contacts.php');
        exit();
    }

    $conn = getDbConnection();
    $stmt = $conn->prepare("INSERT INTO contact (name, email, message, created_at) VALUES (?, ?, ?, NOW())");
    if (!$stmt) {
        $_SESSION['contact_error'] = 'Lỗi kết nối CSDL: ' . $conn->error;
        header('Location: contacts.php');
        exit();
    }
    $stmt->bind_param("sss", $name, $email, $message);
    if ($stmt->execute()) {
        $_SESSION['contact_success'] = 'Cảm ơn! Phản hồi của bạn đã được gửi đến admin.';
    } else {
        $_SESSION['contact_error'] = 'Lỗi khi gửi phản hồi: ' . $stmt->error;
    }
    $stmt->close();
    $conn->close();
    header('Location: contacts.php');
    exit();
}
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Contact Us | Anime</title>
    <link rel="stylesheet" href="css/bootstrap.min.css" type="text/css">
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
                            <li><a href="./index.php">Homepage</a></li>
                            <li><a href="./categories.php">Categories</a></li>
                            <li><a href="./blog.php">Our Blog</a></li>
                            <li class="active"><a href="./contacts.php">Contact</a></li>
                        </ul>
                    </nav>
                </div>
                <div class="col-lg-2">
                    <div class="header__right">
                        <?php if (isset($_SESSION['username'])): ?>
                            <span style="color: white; margin-right: 10px;">
                                Welcome, <a href="information_user.php" style="color: #e53637; text-decoration: none;"><?php echo htmlspecialchars($_SESSION['username']); ?></a>
                            </span>
                            <a href="logout.php" style="color: #888; margin-left: 10px;" title="Logout">
                                <span class="fa fa-sign-out"></span>
                            </a>
                        <?php else: ?>
                            <a href="login.php"><span class="fa fa-user"></span></a>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
            <div id="mobile-menu-wrap"></div>
        </div>
    </header>

    <section class="normal-breadcrumb set-bg" data-setbg="img/normal-breadcrumb.jpg">
        <div class="container text-center">
            <div class="normal__breadcrumb__text">
                <h2>Contact</h2>
                <p>Gửi cho chúng tôi thông tin liên hệ của bạn</p>
            </div>
        </div>
    </section>

    <section class="login spad">
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-lg-6">
                    <div class="login__form">
                        <h3>Contact Us</h3>

                        <?php if (isset($_SESSION['contact_error'])): ?>
                            <div class="alert alert-danger"><?php echo $_SESSION['contact_error']; unset($_SESSION['contact_error']); ?></div>
                        <?php endif; ?>
                        <?php if (isset($_SESSION['contact_success'])): ?>
                            <div class="alert alert-success"><?php echo $_SESSION['contact_success']; unset($_SESSION['contact_success']); ?></div>
                        <?php endif; ?>

                        <form action="contacts.php" method="POST">
                            <div class="input__item">
                                <input type="text" name="name" placeholder="Name" required>
                                <span class="icon_profile"></span>
                            </div>
                            <div class="input__item">
                                <input type="email" name="email" placeholder="Email" required>
                                <span class="icon_mail"></span>
                            </div>
                            <div class="">
                                <textarea name="message" placeholder="Ý kiến, phản hồi hoặc câu hỏi gửi đến admin..." required rows="5" style="width:100%;padding:10px;"></textarea>
                                <span class="icon_chat"></span>
                            </div>
                            <button type="submit" name="send_contact" class="site-btn">Gửi</button>
                        </form>

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
                        <li><a href="./booking.php">Booking</a></li>
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
