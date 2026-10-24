<?php
declare(strict_types=1);
require_once __DIR__ . '/../src/db.php';
$pdo = db();
$categoryId = filter_input(INPUT_GET, 'category', FILTER_VALIDATE_INT);
$sql = "SELECT p.id,p.title,p.summary,p.created_at,c.name AS category
        FROM posts p JOIN categories c ON c.id=p.category_id
        WHERE p.status='published'";
$params = [];
if ($categoryId) { $sql .= " AND c.id=?"; $params[] = $categoryId; }
$sql .= " ORDER BY p.created_at DESC";
$stmt = $pdo->prepare($sql); $stmt->execute($params); $posts = $stmt->fetchAll();
$categories = $pdo->query("SELECT id,name FROM categories ORDER BY name")->fetchAll();
function e($v): string { return htmlspecialchars((string)$v, ENT_QUOTES, 'UTF-8'); }
?>
<!doctype html><html lang="vi"><head><meta charset="utf-8"><meta name="viewport" content="width=device-width, initial-scale=1">
<title>Tin Mới 24h</title><link rel="stylesheet" href="/style.css"></head><body>
<header><div class="wrap"><a class="brand" href="/">TIN MỚI 24H</a><span>Cổng thông tin tổng hợp</span><a class="admin-link" href="/admin.php">Quản trị</a></div></header>
<nav><div class="wrap"><a href="/">Tất cả</a><?php foreach($categories as $c): ?><a href="/?category=<?= (int)$c['id'] ?>"><?= e($c['name']) ?></a><?php endforeach; ?></div></nav>
<main class="wrap"><section class="hero"><p class="eyebrow">TIN TỨC MỖI NGÀY</p><h1>Cập nhật tin tức mới nhất</h1><p>Tổng hợp thông tin, kiến thức và sự kiện đáng chú ý.</p></section>
<h2>Bài viết mới</h2><div class="grid">
<?php foreach($posts as $p): ?><article class="card"><div class="category"><?= e($p['category']) ?></div><h3><a href="/article.php?id=<?= (int)$p['id'] ?>"><?= e($p['title']) ?></a></h3><p><?= e($p['summary']) ?></p><small><?= e(date('d/m/Y H:i', strtotime($p['created_at']))) ?></small></article><?php endforeach; ?>
<?php if (!$posts): ?><p>Chưa có bài viết trong danh mục này.</p><?php endif; ?>
</div></main><footer><div class="wrap">© <?= date('Y') ?> Tin Mới 24h — Đề tài thực hành triển khai hệ thống</div></footer></body></html>
