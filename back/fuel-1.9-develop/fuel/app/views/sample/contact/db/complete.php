<!DOCTYPE html>
<html>
<head>
	<meta charset="utf-8">
	<title>送信完了</title>
	<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
</head>
<body>
	<div class="container mt-5">
		<?php if ($saved): ?>
			<div class="alert alert-success">
				<h2>お問い合わせありがとうございました</h2>
				<p><?php echo htmlspecialchars($name); ?>様、お問い合わせをデータベースに保存しました。</p>
			</div>
		<?php else: ?>
			<div class="alert alert-danger">
				<h2>エラーが発生しました</h2>
				<p><?php echo htmlspecialchars($error_message); ?></p>
				<p class="mb-0"><small>データベースの設定を確認してください。contactsテーブルが作成されているか、db.phpの設定が正しいか確認してください。</small></p>
			</div>
		<?php endif; ?>
		<a href="/fuel/sample/contact/db" class="btn btn-primary">お問い合わせフォームに戻る</a>
		<a href="/fuel/education/topic8" class="btn btn-secondary">説明ページに戻る</a>
	</div>
</body>
</html>
