<!DOCTYPE html>
<html>
<head>
	<meta charset="utf-8">
	<title>送信完了</title>
	<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
</head>
<body>
	<div class="container mt-5">
		<div class="alert alert-success">
			<h2>お問い合わせありがとうございました</h2>
			<p><?php echo htmlspecialchars($name); ?>様、お問い合わせを受け付けました。</p>
			<p class="mb-0"><small>※このサンプルではセッションに一時保存されます。実際のアプリケーションではメール送信などの処理を行います。</small></p>
		</div>
		<a href="/fuel/sample/contact/session" class="btn btn-primary">お問い合わせフォームに戻る</a>
		<a href="/fuel/education/topic7" class="btn btn-secondary">説明ページに戻る</a>
	</div>
</body>
</html>
