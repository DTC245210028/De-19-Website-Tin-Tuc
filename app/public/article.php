<?php
declare(strict_types=1);
require_once __DIR__ . '/../src/db.php';
function e($v): string { return htmlspecialchars((string)$v, ENT_QUOTES, 'UTF-8'); }
$id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);
$stmt = db()->prepare("SELECT p.*, c.name category FROM posts p JOIN categories c ON c.id=p.category_id WHERE p.id=? AND p.status='published'");
$stmt->execute([$id ?: 0]); $post = $stmt->fetch();
if (!$post) { http_response_code(404); $title='Không tìm thấy bài viết'; $content='Bài viết không tồn tại hoặc chưa được xuất bản.'; }
else { $title=$post['title']; $content=$post['content']; }
?>
<!doctype html><html lang="vi"><head><meta charset="utf-8"><meta name="viewport" content="width=device-width, initial-scale=1"><title><?= e($title) ?></title><link rel="stylesheet" href="/style.css"></head><body>
<header><div class="wrap"><a class="brand" href="/">TIN MỚI 24H</a><a class="admin-link" href="/admin.php">Quản trị</a></div></header>
<main class="wrap article"><a href="/">← Về trang chủ</a><?php if ($post): ?><p class="category"><?= e($post['category']) ?></p><?php endif; ?><h1><?= e($title) ?></h1><p><?= nl2br(e($content)) ?></p></main><footer><div class="wrap">Tin Mới 24h</div></footer></body></html>
