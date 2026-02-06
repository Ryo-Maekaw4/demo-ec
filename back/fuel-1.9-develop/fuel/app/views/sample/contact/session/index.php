<!DOCTYPE html>
<html>
<head>
	<meta charset="utf-8">
	<title>お問い合わせ</title>
	<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
</head>
<body>
	<div class="container mt-5">
		<h1>お問い合わせフォーム（セッション版）</h1>
		<p class="text-muted">Topic7-1のサンプルページです。セッションを使ってデータを一時保存します。</p>
		
		<?php if (isset($errors) && !empty($errors)): ?>
			<div class="alert alert-danger">
				<ul class="mb-0">
					<?php foreach ($errors as $error): ?>
						<li><?php echo htmlspecialchars($error); ?></li>
					<?php endforeach; ?>
				</ul>
			</div>
		<?php endif; ?>
		
		<form method="post" action="/fuel/sample/contact/session/confirm">
			<div class="mb-3">
				<label for="name" class="form-label">お名前 <span class="text-danger">*</span></label>
				<input type="text" class="form-control" id="name" name="name" 
					   value="<?php echo isset($name) ? htmlspecialchars($name) : ''; ?>" required>
			</div>
			
			<div class="mb-3">
				<label for="email" class="form-label">メールアドレス <span class="text-danger">*</span></label>
				<input type="email" class="form-control" id="email" name="email" 
					   value="<?php echo isset($email) ? htmlspecialchars($email) : ''; ?>" required>
			</div>
			
			<div class="mb-3">
				<label for="message" class="form-label">お問い合わせ内容 <span class="text-danger">*</span></label>
				<textarea class="form-control" id="message" name="message" rows="5" required><?php echo isset($message) ? htmlspecialchars($message) : ''; ?></textarea>
			</div>
			
			<button type="submit" class="btn btn-primary">確認画面へ</button>
			<a href="/fuel/sample/contact/session" class="btn btn-outline-secondary">フォームをリセット</a>
			<a href="/fuel/education/topic7" class="btn btn-secondary">説明ページに戻る</a>
		</form>
	</div>
</body>
</html>
