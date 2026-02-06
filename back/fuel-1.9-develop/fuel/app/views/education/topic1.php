<!DOCTYPE html>
<html>
<head>
	<meta charset="utf-8">
	<title>FuelPHPを通したフレームワーク学習</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.rtl.min.css" integrity="sha384-PJsj/BTMqILvmcej7ulplguok8ag4xFTPryRq8xevL7eBYSmpXKcbNVuy+P0RMgq" crossorigin="anonymous">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js" integrity="sha384-geWF76RCwLtnZ8qwWowPQNguL3RmwHVBC9FhGdlKrxdiJJigb/j/68SIy3Te4Bkz" crossorigin="anonymous"></script>
</head>
<body>
    <header>
    <nav class="navbar navbar-expand-lg bg-body-tertiary">
        <div class="container-fluid">
            <a class="navbar-brand" href="#">FuelPHPを通したフレームワーク学習</a>
        </div>
    </nav>
    </header>
	<div class="container">
        <a href="/fuel/education/index">戻る</a>
        <h1>FW（フレームワーク）とは？</h1>
        <p>ここではフレームワークがそもそも何者なのかを学びます。</p>
        
        <h2>フレームワークの定義</h2>
        <p>フレームワーク（以後「FW」と呼びます）とは、ソフトウェア開発において、再利用可能なコードの集合体です。</p>
        <p>FWを使用することで、開発者はソフトウェア開発の効率化や品質の向上を図ることができます。</p>
        
        <h2>なぜフレームワークを使うのか？</h2>
        <p>フレームワークを使わない場合、開発者は毎回同じようなコードを一から書く必要があります。例えば：</p>
        <ul>
            <li>データベースへの接続処理</li>
            <li>URLのルーティング（どのURLがどの処理を実行するか）</li>
            <li>セキュリティ対策（SQLインジェクション対策、XSS対策など）</li>
            <li>フォームのバリデーション（入力値の検証）</li>
            <li>セッション管理</li>
        </ul>
        <p>フレームワークを使うことで、これらの機能が標準で提供されるため、開発者は<strong>ビジネスロジック（実際のアプリケーションの機能）に集中</strong>できるようになります。</p>
        
        <h2>フレームワークを使わない場合との違い</h2>
        <p>素のPHPで開発する場合、データベース接続やセキュリティ対策などを毎回自分で実装する必要があります。一方、フレームワークを使えば、これらの機能を短いコードで利用できます。</p>
        <p>例えば、データベースからデータを取得する処理も、フレームワークを使えば数行のコードで実現できますが、素のPHPでは接続処理、クエリ実行、エラーハンドリングなど、多くのコードを書く必要があります。</p>
        
        <h2>MVCパターンについて</h2>
        <p>多くのフレームワーク（FuelPHPも含む）は、<strong>MVC（Model-View-Controller）パターン</strong>という設計パターンを採用しています。</p>
        <ul>
            <li><strong>Model（モデル）</strong>：データベースとのやり取りやビジネスロジックを担当</li>
            <li><strong>View（ビュー）</strong>：ユーザーに見せる画面（HTML）を担当</li>
            <li><strong>Controller（コントローラー）</strong>：ModelとViewを繋ぐ役割を担当</li>
        </ul>
        <p>このパターンにより、コードが整理され、保守しやすくなります。詳しくは今後のトピックで学びます。</p>
        
        <h2>PHPの主要なフレームワーク</h2>
        <p>FWは様々な言語で利用されており、PHPでは以下のようなフレームワークがあります：</p>
        <ul>
            <li><strong>Laravel</strong>：現在最も人気の高いPHPフレームワーク</li>
            <li><strong>FuelPHP</strong>：この学習で使用するフレームワーク（軽量で高速）</li>
            <li><strong>CodeIgniter</strong>：シンプルで学習しやすい</li>
            <li><strong>Symfony</strong>：大規模なアプリケーション開発に適している</li>
            <li><strong>CakePHP</strong>：規約に従うことで開発を効率化</li>
        </ul>
        
        <h2>学習の進め方</h2>
        <p>このページはフレームワーク学習の入り口です。次回以降のTopicでは、FuelPHPを使った実際の開発を通じて、フレームワークの使い方を学んでいきます。</p>

        <h2>Topicリンク</h2>
        <div class="list-group">
            <a class="list-group-item list-group-item-action active" href="/fuel/education/topic1">Topic1：FW（フレームワーク）とは？</a>
            <a class="list-group-item list-group-item-action" href="/fuel/education/topic2">Topic2：FuelPHPの基本構造</a>
            <a class="list-group-item list-group-item-action" href="/fuel/education/topic3">Topic3：FuelPHPのルーティング</a>
            <a class="list-group-item list-group-item-action" href="/fuel/education/topic4">Topic4：FuelPHPのビュー</a>
            <a class="list-group-item list-group-item-action" href="/fuel/education/topic5">Topic5：FuelPHPのコントローラー</a>
            <a class="list-group-item list-group-item-action" href="/fuel/education/topic6">Topic6：FuelPHPのモデル</a>
            <a class="list-group-item list-group-item-action" href="/fuel/education/topic7">Topic7-1：実践編 - お問い合わせフォームを作成しよう（セッション版）</a>
            <a class="list-group-item list-group-item-action" href="/fuel/education/topic8">Topic7-2：実践編 - お問い合わせフォームを作成しよう（データベース版）</a>
        </div>
	</div>
</body>
</html>
