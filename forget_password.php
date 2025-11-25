<?php
session_start();
require_once 'functions/db_connection.php';

function sendVerificationEmail($to, $code)
{
    $subject = "Password Reset Verification Code";
    $message = "Hello!\n\nYour verification code is: $code\nPlease enter this code on the password reset page to continue.\n\nThank you!";
    $headers = "From: no-reply@animecenter.com";

    return mail($to, $subject, $message, $headers);
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['forget'])) {
    $conn = getDbConnection();
    $username = trim($_POST['username'] ?? '');
    $email = trim($_POST['email'] ?? '');

    if (empty($username) || empty($email)) {
        $_SESSION['error'] = "Please enter both username and email!";
        header("Location: forget_password.php");
        exit();
    }

    // Check if account exists and email matches
    $stmt = $conn->prepare("SELECT id, email FROM login WHERE username = ? AND email = ?");
    $stmt->bind_param("ss", $username, $email);
    $stmt->execute();
    $stmt->store_result();

    if ($stmt->num_rows > 0) {
        $verification_code = rand(100000, 999999);

        // Save code in session (or DB if preferred)
        $_SESSION['reset_username'] = $username;
        $_SESSION['verification_code'] = $verification_code;
        $_SESSION['reset_email'] = $email;

        if (sendVerificationEmail($email, $verification_code)) {
            $_SESSION['success'] = "✅ Verification code has been sent to $email. Please check your inbox.";
            header("Location: verify_code.php");
            exit();
        } else {
            $_SESSION['error'] = "⚠️ Failed to send email. Please try again later.";
            header("Location: forget_password.php");
            exit();
        }
    } else {
        $_SESSION['error'] = "⚠️ Invalid username or email!";
        header("Location: forget_password.php");
        exit();
    }
}
?>
<!DOCTYPE html>
<html lang="vi">

<head>
    <meta charset="UTF-8">
    <meta name="description" content="Anime Template">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Forgot Password | Anime</title>

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
                            <li><a href="./index.php">Homepage</a></li>
                            <li><a href="./categories.php">Categories</a></li>
                            <li><a href="./blog.php">Blog</a></li>
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

    <!-- Breadcrumb -->
    <section class="normal-breadcrumb set-bg" data-setbg="img/normal-breadcrumb.jpg">
        <div class="container text-center">
            <div class="normal__breadcrumb__text">
                <h2>Forgot Password</h2>
                <p>Enter your username and email to receive a verification code</p>
            </div>
        </div>
    </section>

    <!-- Forget Password Section -->
    <section class="login spad">
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-lg-6">
                    <div class="login__form">
                        <h3>Reset Password</h3>

                        <!-- Display messages -->
                        <?php if (isset($_SESSION['error'])): ?>
                            <div class="alert alert-danger"><?= $_SESSION['error']; unset($_SESSION['error']); ?></div>
                        <?php elseif (isset($_SESSION['success'])): ?>
                            <div class="alert alert-success"><?= $_SESSION['success']; unset($_SESSION['success']); ?></div>
                        <?php endif; ?>

                        <!-- Form -->
                        <form action="forget_password.php" method="POST" class="login-form">
                            <div class="row justify-content-center">
                                <div class="col-md-10">
                                    <div class="input__item">
                                        <input type="text" name="username" placeholder="Enter your username..." required>
                                        <span class="icon_profile"></span>
                                    </div>
                                </div>
                            </div>

                            <div class="row justify-content-center">
                                <div class="col-md-10">
                                    <div class="input__item">
                                        <input type="email" name="email" placeholder="Enter your registered email..." required>
                                        <span class="icon_mail"></span>
                                    </div>
                                </div>
                            </div>

                            <div class="row justify-content-center mt-4">
                                <div class="col-md-10">
                                    <button type="submit" name="forget" class="site-btn btn-block">Send Verification Code</button>
                                    <a href="login.php" class="forget_pass mt-3">← Back to Login</a>
                                </div>
                            </div>
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
    <script src="js/main.js"></script>
</body>

</html>
