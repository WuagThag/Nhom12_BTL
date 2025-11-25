<?php
session_start();

// Nếu chưa đăng nhập, chuyển hướng tới trang đăng nhập kèm thông tin trang hiện tại
if (!isset($_SESSION['username'])) {
  $current = $_SERVER['REQUEST_URI'] ?? '/anime-main/booking.php';
  // Chuyển hướng tới login.php kèm tham số next để quay lại sau khi đăng nhập
  header('Location: login.php?next=' . urlencode($current));
  exit();
}
?>
<!DOCTYPE html>
<html lang="vi">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Booking | Anime Template</title>

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

  <style>
    /* Seat map styles */
    .screen {
      width: 100%;
      height: 28px;
      background: #e6e6e6;
      border-radius: 6px;
      text-align: center;
      line-height: 28px;
      font-size: 14px;
      margin-bottom: 16px;
      color: #111;
      font-weight: 600;
    }
    .seat-grid {
      display: grid;
      grid-template-columns: repeat(10, 1fr);
      gap: 8px;
      justify-items: center;
      margin-bottom: 16px;
    }
    .seat {
      width: 34px;
      height: 34px;
      border-radius: 6px;
      background: #1d1f2a;
      color: #fff;
      display: flex;
      align-items: center;
      justify-content: center;
      cursor: pointer;
      font-size: 12px;
      user-select: none;
      border: 1px solid rgba(255,255,255,.08);
      transition: transform .08s ease;
    }
    .seat:hover { transform: translateY(-2px); }
    .seat.selected { background: #e53637; }
    .seat.booked   { background: #6c757d; cursor: not-allowed; }
    .row-label { grid-column: 1 / -1; margin: 6px 0 2px; color:#ADB5BD; font-size: 12px;}
    .legend span { display:inline-flex; align-items:center; margin-right:12px; font-size: 14px;}
    .legend i { width:16px; height:16px; display:inline-block; border-radius:4px; margin-right:6px; }
    .legend .l-free  i{ background:#1d1f2a; border:1px solid rgba(255,255,255,.1); }
    .legend .l-sel   i{ background:#e53637; }
    .legend .l-book  i{ background:#6c757d; }

    .time-chip {
      display:inline-block; padding:8px 14px; border:1px solid rgba(255,255,255,.15);
      border-radius:999px; margin:4px 6px 4px 0; cursor:pointer; font-size:14px;
    }
    .time-chip.active { background:#e53637; border-color:#e53637; }
    .sum-line { display:flex; justify-content:space-between; margin:6px 0; }
    .shadow-card { background:#0b0c2a; border-radius:16px; padding:18px; box-shadow:0 6px 20px rgba(0,0,0,.25); }
    .disabled { opacity:.6; pointer-events:none; }
  </style>
</head>
<body>
  <!-- Page Preloder -->
  <div id="preloder"><div class="loader"></div></div>

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
          <div class="header__nav">
            <nav class="header__menu mobile-menu">
              <ul>
                <li><a href="./index.php">Homepage</a></li>
                <li><a href="./categories.php">Categories</a></li>
                <li><a href="./blog.php">Our Blog</a></li>
                <li><a href="./contacts.php">Contacts</a></li>
                <li class="active"><a href="./booking.php">Booking</a></li>
              </ul>
            </nav>
          </div>
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
    </div>
  </header>

  <!-- Normal Breadcrumb -->
  <section class="normal-breadcrumb set-bg" data-setbg="img/normal-breadcrumb.jpg">
    <div class="container">
      <div class="row">
        <div class="col-lg-12 text-center">
          <div class="normal__breadcrumb__text">
            <h2>Đặt vé</h2>
            <p>Chọn suất chiếu & ghế ngồi yêu thích</p>
          </div>
        </div>
      </div>
    </div>
  </section>

  <!-- Booking Section -->
  <section class="anime-details spad">
    <div class="container">
      <div class="row">
        <!-- Seat map -->
        <div class="col-lg-8 mb-4">
          <div class="shadow-card">
            <div class="screen">Màn hình</div>
            <div id="seatGrid" class="seat-grid"></div>
            <div class="legend mt-2">
              <span class="l-free"><i></i>Trống</span>
              <span class="l-sel"><i></i>Đang chọn</span>
              <span class="l-book"><i></i>Đã đặt</span>
            </div>
          </div>
        </div>

        <!-- Sidebar form -->
        <div class="col-lg-4">
          <div class="shadow-card">
            <h5 class="mb-3">Thông tin đặt vé</h5>
            <form id="bookingForm" method="post" action="">
              <div class="mb-3">
                <label class="form-label">Phim</label>
                <select name="movie" class="form-control">
                  <option value="Fate / Stay Night: Unlimited Blade Works">Fate / Stay Night: Unlimited Blade Works</option>
                  <option value="Shingeki no Kyojin">Shingeki no Kyojin</option>
                  <option value="One Piece">One Piece</option>
                </select>
              </div>
              <div class="mb-3">
                <label class="form-label">Ngày chiếu</label>
                <input type="date" class="form-control" name="date" required>
              </div>
              <div class="mb-3">
                <label class="form-label d-block">Giờ chiếu</label>
                <div id="timeList">
                  <!-- Example times -->
                  <span class="time-chip" data-time="13:40">13:40</span>
                  <span class="time-chip" data-time="16:00">16:00</span>
                  <span class="time-chip" data-time="17:15">17:15</span>
                  <span class="time-chip" data-time="18:20">18:20</span>
                  <span class="time-chip" data-time="19:30">19:30</span>
                  <span class="time-chip" data-time="20:40">20:40</span>
                  <span class="time-chip" data-time="21:50">21:50</span>
                </div>
                <input type="hidden" name="time" id="timeInput" required>
              </div>

              <div class="mb-2"><strong>Ghế đã chọn:</strong> <span id="seatSummary">Chưa chọn</span></div>
              <div class="sum-line"><span>Đơn giá</span> <span id="priceEach">75.000 đ</span></div>
              <div class="sum-line"><span>Số ghế</span> <span id="seatCount">0</span></div>
              <div class="sum-line"><span>Tổng tiền</span> <span id="totalPrice">0 đ</span></div>

              <input type="hidden" name="seats" id="seatsInput">
              <input type="hidden" name="total" id="totalInput">
              <button type="submit" class="site-btn w-100 mt-2 disabled" id="submitBtn">Đặt vé</button>
            </form>
          </div>

          <?php if ($_SERVER['REQUEST_METHOD'] === 'POST' && !empty($_POST['seats'])): ?>
            <div class="shadow-card mt-3">
              <h6>✅ Đặt vé thành công!</h6>
              <p><strong>Phim:</strong> <?php echo htmlspecialchars($_POST['movie']); ?></p>
              <p><strong>Suất:</strong> <?php echo htmlspecialchars($_POST['date']); ?> • <?php echo htmlspecialchars($_POST['time']); ?></p>
              <p><strong>Ghế:</strong> <?php echo htmlspecialchars($_POST['seats']); ?></p>
              <p><strong>Tổng tiền:</strong> <?php echo htmlspecialchars($_POST['total']); ?> đ</p>
            </div>
          <?php endif; ?>
        </div>
      </div>
    </div>
  </section>

  <!-- Footer -->
  <footer class="footer">
    <div class="page-up"><a href="#" id="scrollToTopButton"><span class="arrow_carrot-up"></span></a></div>
  </footer>

  <!-- Js Plugins -->
  <script src="js/jquery-3.3.1.min.js"></script>
  <script src="js/bootstrap.min.js"></script>
  <script src="js/player.js"></script>
  <script src="js/jquery.nice-select.min.js"></script>
  <script src="js/mixitup.min.js"></script>
  <script src="js/jquery.slicknav.js"></script>
  <script src="js/owl.carousel.min.js"></script>
  <script src="js/main.js"></script>

  <script>
    // ---- Config ----
    const rows = 6, cols = 10;
    const seatPrice = 75000;
    const bookedSample = ["A3","A4","B7","C1","D5","E10"]; // ví dụ ghế đã đặt

    // ---- Render seat grid ----
    const seatGrid = document.getElementById('seatGrid');
    const letters = "ABCDEFGHIJKLMNOPQRSTUVWXYZ".split('');
    const selected = new Set();

    for (let r = 0; r < rows; r++) {
      for (let c = 1; c <= cols; c++) {
        const code = letters[r] + c;
        const div = document.createElement('div');
        div.className = "seat" + (bookedSample.includes(code) ? " booked" : "");
        div.dataset.code = code;
        div.textContent = c;
        if (!bookedSample.includes(code)) {
          div.addEventListener('click', () => {
            if (div.classList.contains('selected')) {
              div.classList.remove('selected'); selected.delete(code);
            } else {
              div.classList.add('selected'); selected.add(code);
            }
            updateSummary();
          });
        }
        seatGrid.appendChild(div);
      }
    }

    // ---- Time select ----
    const timeList = document.getElementById('timeList');
    const timeInput = document.getElementById('timeInput');
    timeList.querySelectorAll('.time-chip').forEach(chip => {
      chip.addEventListener('click', () => {
        timeList.querySelectorAll('.time-chip').forEach(c => c.classList.remove('active'));
        chip.classList.add('active');
        timeInput.value = chip.dataset.time;
        updateSummary();
      });
    });

    // ---- Summary ----
    const seatSummary = document.getElementById('seatSummary');
    const seatCount = document.getElementById('seatCount');
    const totalPrice = document.getElementById('totalPrice');
    const seatsInput = document.getElementById('seatsInput');
    const totalInput = document.getElementById('totalInput');
    const submitBtn = document.getElementById('submitBtn');

    function formatVND(n){ return n.toLocaleString('vi-VN'); }

    function updateSummary() {
      const arr = Array.from(selected).sort();
      seatSummary.textContent = arr.length ? arr.join(', ') : 'Chưa chọn';
      seatCount.textContent = arr.length;
      const total = arr.length * seatPrice;
      totalPrice.textContent = (total ? formatVND(total) : 0) + ' đ';
      seatsInput.value = arr.join(',');
      totalInput.value = total ? formatVND(total) : 0;

      const canSubmit = arr.length > 0 && timeInput.value && document.querySelector('input[name="date"]').value;
      submitBtn.classList.toggle('disabled', !canSubmit);
    }
  </script>
</body>
</html>
