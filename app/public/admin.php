<?php
declare(strict_types=1);
session_start();
require_once __DIR__ . '/../src/db.php';
function e($v): string { return htmlspecialchars((string)$v, ENT_QUOTES, 'UTF-8'); }
$pdo=db(); $error='';
if (isset($_GET['logout'])) { session_destroy(); header('Location: /admin.php'); exit; }
if ($_SERVER['REQUEST_METHOD']==='POST' && isset($_POST['login'])) {
  if (!hash_equals(getenv('ADMIN_PASSWORD') ?: 'ChangeMe_Admin_2026!', (string)($_POST['password'] ?? ''))) $error='Mật khẩu không đúng.';
  else { session_regenerate_id(true); $_SESSION['admin']=true; header('Location: /admin.php'); exit; }
}
if (empty($_SESSION['admin'])): ?>
<!doctype html><html lang="vi"><head><meta charset="utf-8"><meta name="viewport" content="width=device-width, initial-scale=1"><title>Đăng nhập quản trị</title><link rel="stylesheet" href="/style.css"></head><body><main class="login"><h1>Quản trị tin tức</h1><p>Mật khẩu mặc định: <code>ChangeMe_Admin_2026!</code> (hãy đổi trước khi triển khai công khai).</p><?php if($error): ?><p class="error"><?= e($error) ?></p><?php endif; ?><form method="post"><label>Mật khẩu quản trị</label><input type="password" name="password" required><button name="login" value="1">Đăng nhập</button></form><a href="/">Về trang chủ</a></main></body></html>
<?php exit; endif;
if ($_SERVER['REQUEST_METHOD']==='POST' && isset($_POST['save'])) {
  $title=trim((string)($_POST['title']??'')); $summary=trim((string)($_POST['summary']??'')); $content=trim((string)($_POST['content']??'')); $cat=(int)($_POST['category_id']??0);
  if ($title && $summary && $content && $cat) {
    $stmt=$pdo->prepare("INSERT INTO posts(title,summary,content,category_id,status,created_at) VALUES(?,?,?,?, 'published', NOW())");
    $stmt->execute([$title,$summary,$content,$cat]); header('Location: /admin.php?saved=1'); exit;
  } else $error='Vui lòng nhập đầy đủ thông tin.';
}
if (isset($_GET['delete'])) { $stmt=$pdo->prepare("DELETE FROM posts WHERE id=?"); $stmt->execute([(int)$_GET['delete']]); header('Location: /admin.php'); exit; }
$cats=$pdo->query("SELECT id,name FROM categories ORDER BY name")->fetchAll();
$posts=$pdo->query("SELECT p.id,p.title,p.created_at,c.name category FROM posts p JOIN categories c ON c.id=p.category_id ORDER BY p.id DESC")->fetchAll();
?>
<!doctype html><html lang="vi"><head><meta charset="utf-8"><meta name="viewport" content="width=device-width, initial-scale=1"><title>Quản trị</title><link rel="stylesheet" href="/style.css"></head><body><header><div class="wrap"><a class="brand" href="/">TIN MỚI 24H</a><a class="admin-link" href="/admin.php?logout=1">Đăng xuất</a></div></header><main class="wrap admin"><h1>Quản lý bài viết</h1><?php if(isset($_GET['saved'])): ?><p class="success">Đã đăng bài viết.</p><?php endif; ?><?php if($error): ?><p class="error"><?= e($error) ?></p><?php endif; ?>
<form method="post" class="form-card"><h2>Đăng bài mới</h2><label>Tiêu đề</label><input name="title" required maxlength="200"><label>Tóm tắt</label><input name="summary" required maxlength="500"><label>Danh mục</label><select name="category_id" required><?php foreach($cats as $c): ?><option value="<?= (int)$c['id'] ?>"><?= e($c['name']) ?></option><?php endforeach; ?></select><label>Nội dung</label><textarea name="content" rows="6" required></textarea><button name="save" value="1">Đăng bài</button></form>
<h2>Bài viết hiện có</h2><div class="table-wrap"><table><tr><th>ID</th><th>Tiêu đề</th><th>Danh mục</th><th>Thao tác</th></tr><?php foreach($posts as $p): ?><tr><td><?= (int)$p['id'] ?></td><td><?= e($p['title']) ?></td><td><?= e($p['category']) ?></td><td><a onclick="return confirm('Xóa bài viết này?')" href="/admin.php?delete=<?= (int)$p['id'] ?>">Xóa</a></td></tr><?php endforeach; ?></table></div></main></body></html>
