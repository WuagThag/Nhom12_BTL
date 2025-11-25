<?php
// Simple PHP-only dashboard page that lists anime entries from the database.
// Usage: open http://localhost/anime-main/php_dashboard.php when XAMPP Apache is running

require_once __DIR__ . '/functions/db_connection.php';

$conn = getDbConnection();

$sql = "SELECT id, title, image, episodes, views, status, type, description FROM anime ORDER BY id DESC";
$result = $conn->query($sql);

$animes = [];
if ($result) {
    while ($row = $result->fetch_assoc()) {
        $animes[] = $row;
    }
}

$conn->close();
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>PHP Dashboard - Anime List</title>
    <link rel="stylesheet" href="css/bootstrap.min.css" type="text/css">
    <link rel="stylesheet" href="css/font-awesome.min.css" type="text/css">
    <link rel="stylesheet" href="css/style.css" type="text/css">
    <style>
        .anime-card { margin-bottom: 20px; }
        .anime-img { width: 100%; height: 180px; object-fit: cover; }
        .meta { font-size: 0.9rem; }
        .container-heading { margin: 30px 0; color: #fff; }
        body { background: #0f1724; }
        .card { background: #121826; color: #e6eef8; }
        a.link-light { color: #ffd700; }
    </style>
</head>
<body>
    <div class="container">
        <div class="row">
            <div class="col-12 text-center container-heading">
                <h2>PHP Dashboard — Danh sách Anime</h2>
                <p>Hiển thị dữ liệu trực tiếp bằng PHP + MySQL (sử dụng `functions/db_connection.php`)</p>
                <p><a class="link-light" href="index.php">Quay về giao diện chính</a></p>
            </div>
        </div>

        <div class="row">
            <?php if (empty($animes)): ?>
                <div class="col-12">
                    <div class="alert alert-info">Không có anime nào để hiển thị.</div>
                </div>
            <?php else: ?>
                <?php foreach ($animes as $a): ?>
                    <div class="col-lg-4 col-md-6 anime-card">
                        <div class="card">
                            <?php if (!empty($a['image'])): ?>
                                <img src="<?php echo htmlspecialchars($a['image']); ?>" alt="<?php echo htmlspecialchars($a['title']); ?>" class="anime-img card-img-top">
                            <?php else: ?>
                                <div style="height:180px;background:#0b1220;display:flex;align-items:center;justify-content:center;color:#9aa7bf;">No image</div>
                            <?php endif; ?>
                            <div class="card-body">
                                <h5 class="card-title"><?php echo htmlspecialchars($a['title']); ?></h5>
                                <p class="meta">
                                    <strong>Tập:</strong> <?php echo htmlspecialchars($a['episodes'] ?? 'N/A'); ?>
                                    &nbsp;|&nbsp;
                                    <strong>Views:</strong> <?php echo htmlspecialchars($a['views'] ?? '0'); ?>
                                </p>
                                <p class="mb-1"><span class="badge bg-secondary"><?php echo htmlspecialchars($a['status'] ?? 'Unknown'); ?></span>
                                <span class="badge bg-info text-dark"><?php echo htmlspecialchars($a['type'] ?? ''); ?></span></p>
                                <p class="card-text" style="max-height:3.6em;overflow:hidden;"><?php echo nl2br(htmlspecialchars($a['description'] ?? '')); ?></p>
                                <a href="anime-details.php?id=<?php echo urlencode($a['id']); ?>" class="btn btn-sm btn-primary">Chi tiết</a>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
    </div>

    <script src="js/jquery-3.3.1.min.js"></script>
    <script src="js/bootstrap.min.js"></script>
</body>
</html>
