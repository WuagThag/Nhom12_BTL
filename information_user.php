<?php
session_start();
require_once 'functions/db_connection.php';

// Nếu chưa đăng nhập, chuyển hướng về login
if (!isset($_SESSION['user_id'])) {
    $_SESSION['error'] = 'Bạn cần đăng nhập để xem thông tin cá nhân.';
    header('Location: login.php');
    exit();
}

$userId = $_SESSION['user_id'];
$conn = getDbConnection();
$stmt = $conn->prepare('SELECT id, username, display_name, email, phone, birthday, role FROM login WHERE id = ? LIMIT 1');
$stmt->bind_param('i', $userId);
$stmt->execute();
$result = $stmt->get_result();
$user = null;
if ($result && $result->num_rows > 0) {
    $user = $result->fetch_assoc();
}
$stmt->close();
$conn->close();

?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Thông tin cá nhân | Anime</title>
    <link rel="stylesheet" href="css/bootstrap.min.css" type="text/css">
    <link rel="stylesheet" href="css/style.css" type="text/css">
    <style>
        /* Force white text for this page */
        body, .header, .header a, .header .icon_profile, .normal__breadcrumb__text, 
        .normal__breadcrumb__text h2, .normal__breadcrumb__text p, .login__form, .login__form *,
        .footer, .footer * {
            color: #ffffff !important;
        }

        /* Input fields styling */
        .input__item {
            position: relative;
            width: 100%;
            margin-bottom: 20px;
        }

        .input__item input {
            height: 50px;
            width: 100%;
            font-size: 15px;
            padding: 0 20px;
            background-color: #0b0c2a !important;
            color: #ffffff !important;
            border: 1px solid #2f2f2f !important;
            border-radius: 2px;
        }

        .input__item input::placeholder {
            color: #888888 !important;
        }

        .input__item input:focus {
            border-color: #e53637 !important;
            outline: none;
        }

        .input__item span {
            position: absolute;
            right: 20px;
            top: 50%;
            transform: translateY(-50%);
            color: #b7b7b7;
            font-size: 20px;
        }

        .input__item input:focus + span {
            color: #e53637;
        }

        /* Read-only input styling */
        .input__item input[readonly] {
            background-color: #1a1b39 !important;
            cursor: not-allowed;
        }

        /* Table text and borders */
        table.table, table.table th, table.table td {
            color: #ffffff !important;
            border-color: rgba(255,255,255,0.1) !important;
        }

        /* Make table background transparent */
        table.table {
            background: transparent !important;
        }

        /* Buttons styling */
        .site-btn, .forget_pass {
            color: #ffffff !important;
            background: #e53637 !important;
            border: none;
            padding: 12px 30px;
            border-radius: 2px;
            margin-bottom: 20px;
            font-weight: 500;
            text-transform: uppercase;
        }

        .site-btn:hover {
            background: #b52728 !important;
        }

        /* Alert styling */
        .alert {
            background-color: #0b0c2a !important;
            border-color: #2f2f2f !important;
            color: #ffffff !important;
            margin-bottom: 20px;
        }

        .alert-success {
            background-color: #1e4620 !important;
            border-color: #2f5233 !important;
        }

        .alert-danger {
            background-color: #461e1e !important;
            border-color: #522f2f !important;
        }
    </style>
</head>
<body>
    <!-- Header (reuse simple header from index) -->
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
                        <li class="active"><a href="./index.php">Homepage</a></li>
                        <li><a href="./categories.php">Categories</a></li>
                        <li><a href="./blog.php">Our Blog</a></li>
                        <li><a href="./contacts.php">Contacts</a></li>
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
                <h2>Thông tin cá nhân</h2>
                <p>Quản lý thông tin tài khoản của bạn.</p>
            </div>
        </div>
    </section>

    <section class="login spad">
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-lg-8">
                    <div class="login__form">
                        <h3>Thông tin người dùng</h3>

                        <?php if (!$user): ?>
                            <div class="alert alert-danger">User information not found.</div>
                        <?php else: ?>
                            <?php if (isset($_SESSION['update_success'])): ?>
                                <div class="alert alert-success"><?= htmlspecialchars($_SESSION['update_success']); ?></div>
                                <?php unset($_SESSION['update_success']); ?>
                            <?php endif; ?>

                            <?php if (isset($_SESSION['update_error'])): ?>
                                <div class="alert alert-danger"><?= htmlspecialchars($_SESSION['update_error']); ?></div>
                                <?php unset($_SESSION['update_error']); ?>
                            <?php endif; ?>

                            <form action="handle/update_profile.php" method="POST" class="mb-4">
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="input__item">
                                            <input type="text" name="username" value="<?= htmlspecialchars($user['username'] ?? ''); ?>" readonly>
                                            <span class="icon_profile"></span>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="input__item">
                                            <input type="text" name="display_name" value="<?= htmlspecialchars($user['display_name'] ?? ''); ?>" placeholder="Full Name" required>
                                            <span class="icon_tag_alt"></span>
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="input__item">
                                            <input type="email" name="email" value="<?= htmlspecialchars($user['email'] ?? ''); ?>" placeholder="Email Address" required>
                                            <span class="icon_mail"></span>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="input__item">
                                            <input type="tel" name="phone" value="<?= htmlspecialchars($user['phone'] ?? ''); ?>" placeholder="Phone Number">
                                            <span class="icon_phone"></span>
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="input__item">
                                            <input type="date" name="birthday" value="<?= htmlspecialchars($user['birthday'] ?? ''); ?>" placeholder="Birthday">
                                            <span class="icon_calendar"></span>
                                        </div>
                                    </div>
                                </div>

                                <button type="submit" name="update_profile" class="site-btn">Update Profile</button>
                                <a href="reset_password.php" class="site-btn" style="margin-left:10px;">Change Password</a>
                                <a href="logout.php" class="site-btn" style="background:#d9534f; margin-left:10px;">Logout</a>
                            </form>
                        <?php endif; ?>

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
        <!-- Success modal for password change -->
        <?php if (isset($_SESSION['success'])): ?>
                <!-- Modal markup -->
                <div class="modal fade" id="passwordSuccessModal" tabindex="-1" role="dialog" aria-labelledby="passwordSuccessModalLabel" aria-hidden="true">
                    <div class="modal-dialog modal-dialog-centered" role="document">
                        <div class="modal-content" style="background:#222; color:#fff;">
                            <div class="modal-header">
                                <h5 class="modal-title" id="passwordSuccessModalLabel">Thay đổi mật khẩu</h5>
                                <button type="button" class="close" data-dismiss="modal" aria-label="Close" style="color:#fff;">
                                    <span aria-hidden="true">&times;</span>
                                </button>
                            </div>
                            <div class="modal-body">
                                <?= htmlspecialchars($_SESSION['success']); ?>
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-primary" data-dismiss="modal">Đóng</button>
                            </div>
                        </div>
                    </div>
                </div>

                <script>
                        // Show modal on page load
                        $(document).ready(function(){
                                $('#passwordSuccessModal').modal('show');
                        });
                </script>
                <?php unset($_SESSION['success']); endif; ?>
</body>
</html>
