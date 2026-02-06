<!DOCTYPE html>
<html>
<head>
	<meta charset="utf-8">
	<title>確認画面</title>
	<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
</head>
<body>
	<div class="container mt-5">
		<h1>入力内容の確認</h1>
		<p class="text-muted">以下の内容で送信します。セッションに一時保存されます。</p>
		
		<div class="card">
			<div class="card-body">
				<div class="mb-3">
					<strong>お名前：</strong>
					<?php echo htmlspecialchars($name); ?>
				</div>
				
				<div class="mb-3">
					<strong>メールアドレス：</strong>
					<?php echo htmlspecialchars($email); ?>
				</div>
				
				<div class="mb-3">
					<strong>お問い合わせ内容：</strong>
					<pre><?php echo htmlspecialchars($message); ?></pre>
				</div>
			</div>
		</div>
		
		<form method="post" action="/fuel/sample/contact/session/complete">
			<button type="submit" class="btn btn-primary mt-3">送信する</button>
			<a href="/fuel/sample/contact/session" class="btn btn-secondary mt-3">戻る</a>
		</form>
	</div>
</body>
</html>
