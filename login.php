<?php
session_start();
require_once 'functions/db_connection.php';
require_once 'functions/auth.php';


// When the user submits the Login form
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['login'])) {
    handleLogin();
}

function handleLogin() {
    $conn = getDbConnection();
    $username = $_POST['username'] ?? '';
    $password = $_POST['password'] ?? '';
    $next = $_POST['next'] ?? '';

    if (empty($username) || empty($password)) {
        $_SESSION['error'] = 'Please enter both username and password!';
        header('Location: login.php' . (!empty($next) ? '?next=' . urlencode($next) : ''));
        exit();
    }

    $user = authenticateUser($conn, $username, $password);
    if ($user) {
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['username'] = $user['username'];
        $_SESSION['role'] = $user['role'];
        $_SESSION['success'] = 'Login successful!';
        mysqli_close($conn);

        // If `next` is provided and appears to be a relative/internal path, redirect there
        if (!empty($next) && strpos($next, 'http') === false) {
            header('Location: ' . $next);
            exit();
        }

        // Default redirect to homepage
        header('Location: index.php');
        exit();
    }

    $_SESSION['error'] = 'Incorrect username or password!';
    mysqli_close($conn);
    header('Location: login.php' . (!empty($next) ? '?next=' . urlencode($next) : ''));
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="description" content="Anime Template">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login | Anime</title>

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
                <h2>Login</h2>
                <p>Welcome to Anime Blog!</p>
            </div>
        </div>
    </section>

    <!-- Login Section -->
    <section class="login spad">
        <div class="container">
            <div class="row">
                <div class="col-lg-6">
                    <div class="login__form">
                        <h3>Login</h3>

                        <!-- Show error or success messages -->
                        <?php if (isset($_SESSION['error'])): ?>
                            <div class="alert alert-danger"><?= $_SESSION['error']; unset($_SESSION['error']); ?></div>
                        <?php elseif (isset($_SESSION['success'])): ?>
                            <div class="alert alert-success"><?= $_SESSION['success']; unset($_SESSION['success']); ?></div>
                        <?php endif; ?>

                        <!-- Login form -->
                        <form action="login.php" method="POST">
                            <input type="hidden" name="next" value="<?php echo htmlspecialchars($_GET['next'] ?? ''); ?>">
                            <div class="input__item">
                                <input type="text" name="username" placeholder="Username">
                                <span class="icon_profile"></span>
                            </div>
                            <div class="input__item">
                                <input type="password" name="password" placeholder="Password">
                                <span class="icon_lock"></span>
                            </div>
                            <button type="submit" name="login" class="site-btn">Login</button>
                        </form>

                        <a href="forget_password.php" class="forget_pass">Forgot password?</a>
                    </div>
                </div>

                <div class="col-lg-6">
                    <div class="login__register">
                        <h3>Don't have an account?</h3>
                        <a href="register.php" class="primary-btn">Register now</a>
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
