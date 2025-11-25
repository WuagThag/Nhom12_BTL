<?php
session_start();
require_once 'functions/db_connection.php';

$search = isset($_GET['q']) ? trim($_GET['q']) : '';
$results = [];

if ($search !== '') {
    // Tìm kiếm trong bảng anime
    $sql = "SELECT * FROM anime WHERE title LIKE ? OR description LIKE ?";
    $searchTerm = "%" . $search . "%";
    
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ss", $searchTerm, $searchTerm);
    $stmt->execute();
    $result = $stmt->get_result();
    $results = $result->fetch_all(MYSQLI_ASSOC);
}
?>

<!DOCTYPE html>
<html lang="zxx">

<head>
    <meta charset="UTF-8">
    <meta name="description" content="Anime Template">
    <meta name="keywords" content="Anime, unica, creative, html">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Anime | Kết quả tìm kiếm</title>

    <!-- Google Font -->
    <link href="https://fonts.googleapis.com/css2?family=Oswald:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Mulish:wght@300;400;500;600;700;800;900&display=swap" rel="stylesheet">

    <!-- Css Styles -->
    <link rel="stylesheet" href="css/bootstrap.min.css" type="text/css">
    <link rel="stylesheet" href="css/font-awesome.min.css" type="text/css">
    <link rel="stylesheet" href="css/elegant-icons.css" type="text/css">
    <link rel="stylesheet" href="css/plyr.css" type="text/css">
    <link rel="stylesheet" href="css/nice-select.css" type="text/css">
    <link rel="stylesheet" href="css/owl.carousel.min.css" type="text/css">
    <link rel="stylesheet" href="css/slicknav.min.css" type="text/css">
    <link rel="stylesheet" href="css/style.css" type="text/css">
</head>

<body>
    <!-- Page Preloder -->
    <div id="preloder">
        <div class="loader"></div>
    </div>

    <!-- Header Section Begin -->
    <header class="header">
        <div class="container">
            <div class="row">
                <div class="col-lg-2">
                    <div class="header__logo">
                        <a href="./index.php">
                            <img src="img/logo.png" alt="">
                        </a>
                    </div>
                </div>
                <div class="col-lg-8">
                    <div class="header__nav">
                        <nav class="header__menu mobile-menu">
                            <ul>
                                <li><a href="./index.php">Trang chủ</a></li>
                                <li><a href="./categories.php">Thể loại</a></li>
                                <li><a href="./blog.php">Blog</a></li>
                                <li><a href="#">Liên hệ</a></li>
                            </ul>
                        </nav>
                    </div>
                </div>
                <div class="col-lg-2">
                    <div class="header__right">
                        <a href="#" class="search-switch"><span class="icon_search"></span></a>
                        <?php if (isset($_SESSION['username'])): ?>
                            <span style="color: white; margin-left: 10px;">
                                Xin chào, <a href="information_user.php" style="color: #ffd700; text-decoration: underline;"><?php echo htmlspecialchars($_SESSION['username']); ?></a>!
                            </span>
                            <a href="logout.php" style="color: yellow; margin-left: 10px;">Đăng xuất</a>
                        <?php else: ?>
                            <a href="./login.php"><span class="icon_profile"></span></a>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
            <div id="mobile-menu-wrap"></div>
        </div>
    </header>
    <!-- Header End -->

    <!-- Product Section Begin -->
    <section class="product spad">
        <div class="container">
            <div class="row">
                <div class="col-lg-12">
                    <div class="section-title">
                        <h4>Kết quả tìm kiếm cho: "<?php echo htmlspecialchars($search); ?>"</h4>
                    </div>
                </div>
            </div>
            <div class="row">
                <?php if ($search === ''): ?>
                    <div class="col-lg-12">
                        <p class="text-white">Vui lòng nhập từ khóa để tìm kiếm.</p>
                    </div>
                <?php elseif (empty($results)): ?>
                    <div class="col-lg-12">
                        <p class="text-white">Không tìm thấy kết quả nào cho "<?php echo htmlspecialchars($search); ?>"</p>
                    </div>
                <?php else: ?>
                    <?php foreach ($results as $anime): ?>
                        <div class="col-lg-4 col-md-6 col-sm-6">
                            <div class="product__item">
                                <div class="product__item__pic set-bg" data-setbg="<?php echo htmlspecialchars($anime['image']); ?>">
                                    <div class="ep"><?php echo htmlspecialchars($anime['episodes']); ?> tập</div>
                                    <div class="view"><i class="fa fa-eye"></i> <?php echo htmlspecialchars($anime['views']); ?></div>
                                </div>
                                <div class="product__item__text">
                                    <ul>
                                        <li><?php echo htmlspecialchars($anime['status']); ?></li>
                                        <li><?php echo htmlspecialchars($anime['type']); ?></li>
                                    </ul>
                                    <h5><a href="anime-details.php?id=<?php echo $anime['id']; ?>">
                                        <?php echo htmlspecialchars($anime['title']); ?>
                                    </a></h5>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>
        </div>
    </section>
    <!-- Product Section End -->

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
                            <li class="active"><a href="./index.php">Trang chủ</a></li>
                            <li><a href="./categories.php">Thể loại</a></li>
                            <li><a href="./blog.php">Blog</a></li>
                            <li><a href="./contacts.php">Liên hệ</a></li>
                        </ul>
                    </div>
                </div>
                <div class="col-lg-3">
                    <p><!-- Link back to Colorlib can't be removed. Template is licensed under CC BY 3.0. -->
                      Copyright &copy;<script>document.write(new Date().getFullYear());</script> All rights reserved | This template is made with <i class="fa fa-heart" aria-hidden="true"></i> by <a href="https://colorlib.com" target="_blank">Colorlib</a>
                    </p>
                </div>
            </div>
        </div>
    </footer>
    <!-- Footer Section End -->

    <!-- Search model Begin -->
    <div class="search-model">
        <div class="h-100 d-flex align-items-center justify-content-center">
            <div class="search-close-switch"><i class="icon_close"></i></div>
            <form class="search-model-form" action="search.php" method="get">
                <input type="text" id="search-input" name="q" placeholder="Tìm anime bạn muốn xem...">
            </form>
        </div>
    </div>
    <!-- Search model end -->

    <!-- Js Plugins -->
    <script src="js/jquery-3.3.1.min.js"></script>
    <script src="js/bootstrap.min.js"></script>
    <script src="js/player.js"></script>
    <script src="js/jquery.nice-select.min.js"></script>
    <script src="js/mixitup.min.js"></script>
    <script src="js/jquery.slicknav.js"></script>
    <script src="js/owl.carousel.min.js"></script>
    <script src="js/main.js"></script>
</body>

</html>