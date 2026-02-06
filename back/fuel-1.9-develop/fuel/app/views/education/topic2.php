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
        <h1>FuelPHPの基本構造</h1>
        <p>ここではFuelPHPのディレクトリ構造と、それぞれの役割について学びます。</p>
        
        <h2>FuelPHPのディレクトリ構造</h2>
        <p>FuelPHPプロジェクトは、以下のようなディレクトリ構造になっています：</p>
        
        <div class="card mb-3">
            <div class="card-body">
                <h5 class="card-title">主要なディレクトリ</h5>
                <pre class="bg-light p-3"><code>fuel-1.9-develop/
├── fuel/              # FuelPHPのコアとアプリケーション
│   ├── app/           # アプリケーション固有のコード
│   ├── core/          # FuelPHPのコア機能
│   ├── packages/      # 追加パッケージ
│   └── vendor/        # Composerで管理される依存関係
└── public/            # 公開ディレクトリ（Webサーバーのドキュメントルート）
    └── index.php       # エントリーポイント</code></pre>
            </div>
        </div>
        
        <h3>各ディレクトリの詳細説明</h3>
        <ul>
            <li><strong><code>fuel/app/</code></strong>：<strong>あなたが開発するアプリケーションのコードを配置する場所</strong>です。コントローラー、モデル、ビュー、設定ファイルなど、実際の開発で触るファイルのほとんどがここにあります。
                <ul>
                    <li>例：<code>classes/controller/education.php</code>、<code>views/education/topic1.php</code>、<code>config/routes.php</code></li>
                </ul>
            </li>
            <li><strong><code>fuel/core/</code></strong>：<strong>FuelPHPフレームワーク自体のコア機能</strong>が入っています。通常は触る必要はありません。フレームワークの内部実装が含まれているため、直接編集するとフレームワークの更新時に問題が発生する可能性があります。</li>
            <li><strong><code>fuel/packages/</code></strong>：<strong>追加のパッケージ（拡張機能）を配置する場所</strong>です。FuelPHPの標準機能では足りない場合に、ここに追加のパッケージを配置して機能を拡張できます。最初は空のディレクトリです。</li>
            <li><strong><code>fuel/vendor/</code></strong>：<strong>Composer（PHPのパッケージ管理ツール）で管理される外部ライブラリ</strong>が入ります。FuelPHPやその他の依存関係が自動的にインストールされます。通常は直接編集しません。</li>
            <li><strong><code>public/</code></strong>：<strong>Webサーバーのドキュメントルート（公開ディレクトリ）</strong>として設定するディレクトリです。Webブラウザから直接アクセスできるファイル（CSS、JavaScript、画像など）を配置します。このディレクトリがWebサーバーの公開フォルダになるため、セキュリティ上重要な役割を果たします。</li>
            <li><strong><code>public/index.php</code></strong>：<strong>エントリーポイント（入口となるファイル）</strong>です。すべてのWebリクエストは最初にこのファイルを通ります。このファイルがFuelPHPを起動し、適切なコントローラーにリクエストを振り分けます。エントリーポイントとは、プログラムの実行が始まる最初の場所のことです。</li>
        </ul>

        <h2>fuel/app/ ディレクトリ</h2>
        <p><code>fuel/app/</code>は、<strong>あなたが開発するアプリケーションのコードを配置する場所</strong>です。MVCパターンに従って、以下のような構造になっています：</p>
        
        <ul>
            <li><strong>classes/controller/</strong>：コントローラークラスを配置
                <ul>
                    <li>例：<code>education.php</code>、<code>welcome.php</code></li>
                    <li>URLリクエストを受け取り、処理を実行する</li>
                </ul>
            </li>
            <li><strong>classes/model/</strong>：モデルクラスを配置
                <ul>
                    <li>データベースとのやり取りやビジネスロジックを担当</li>
                </ul>
            </li>
            <li><strong>views/</strong>：ビューファイル（HTMLテンプレート）を配置
                <ul>
                    <li>例：<code>education/topic1.php</code>、<code>welcome/index.php</code></li>
                    <li>ユーザーに見せる画面を定義</li>
                </ul>
            </li>
            <li><strong>config/</strong>：設定ファイルを配置
                <ul>
                    <li><code>routes.php</code>：URLルーティングの設定</li>
                    <li><code>config.php</code>：アプリケーションの基本設定</li>
                    <li><code>db.php</code>：データベース接続の設定</li>
                </ul>
            </li>
            <li><strong>cache/</strong>：キャッシュファイルが保存される</li>
            <li><strong>logs/</strong>：ログファイルが保存される</li>
            <li><strong>migrations/</strong>：データベースマイグレーションファイルを配置</li>
        </ul>

        <h2>fuel/core/ ディレクトリ</h2>
        <p><code>fuel/core/</code>には、<strong>FuelPHPフレームワーク自体のコア機能</strong>が含まれています。通常、このディレクトリのファイルを直接編集することはありません。</p>
        <p>このディレクトリには、以下のような機能が含まれています：</p>
        <ul>
            <li>ルーティング機能</li>
            <li>データベース接続機能</li>
            <li>セッション管理機能</li>
            <li>セキュリティ機能</li>
            <li>その他、フレームワークの基本機能</li>
        </ul>

        <h2>fuel/packages/ ディレクトリ</h2>
        <p><code>fuel/packages/</code>には、<strong>追加のパッケージ</strong>が配置されます。FuelPHPには標準で以下のパッケージが含まれています：</p>
        <ul>
            <li><strong>auth</strong>：認証機能</li>
            <li><strong>email</strong>：メール送信機能</li>
            <li><strong>orm</strong>：ORM（Object-Relational Mapping）機能</li>
            <li><strong>oil</strong>：コマンドラインツール</li>
        </ul>
        <p>必要に応じて、独自のパッケージを追加することもできます。</p>

        <h2>public/ ディレクトリ</h2>
        <p><code>public/</code>は、<strong>Webサーバーのドキュメントルート</strong>として設定するディレクトリです。このディレクトリが外部からアクセス可能になります。</p>
        <ul>
            <li><strong>index.php</strong>：すべてのリクエストのエントリーポイント
                <ul>
                    <li>このファイルが最初に実行され、FuelPHPを起動する</li>
                    <li>URLのルーティングもここから始まる</li>
                </ul>
            </li>
            <li><strong>assets/</strong>：CSS、JavaScript、画像などの静的ファイルを配置</li>
        </ul>
        <div class="alert alert-info">
            <strong>セキュリティのポイント：</strong>Webサーバーは<code>public/</code>ディレクトリのみを公開するように設定します。これにより、<code>fuel/app/</code>や<code>fuel/core/</code>などの重要なファイルが直接アクセスされることを防ぎます。
        </div>

        <h2>リクエストの流れ</h2>
        <p>ユーザーがブラウザでURLにアクセスしたとき、FuelPHPでは以下のような流れで処理が行われます：</p>
        <ol>
            <li><strong>public/index.php</strong>が実行される（エントリーポイント）</li>
            <li><strong>config/routes.php</strong>でURLがルーティングされる（どのコントローラーのどのメソッドを実行するか決定）</li>
            <li><strong>classes/controller/</strong>内の該当するコントローラーが実行される</li>
            <li>コントローラーが<strong>classes/model/</strong>からモデルを呼び出す（必要に応じて）</li>
            <li>コントローラーが<strong>views/</strong>内のビューファイルを読み込む</li>
            <li>ビューファイルがHTMLとして出力され、ブラウザに表示される</li>
        </ol>

        <h2>まとめ</h2>
        <p>FuelPHPの基本構造を理解することで、以下のことが分かるようになります：</p>
        <ul>
            <li>新しいコントローラーを作成する場合は<code>fuel/app/classes/controller/</code>に配置</li>
            <li>新しいビューを作成する場合は<code>fuel/app/views/</code>に配置</li>
            <li>URLルーティングを設定する場合は<code>fuel/app/config/routes.php</code>を編集</li>
            <li>設定ファイルは<code>fuel/app/config/</code>に配置</li>
        </ul>
        <p>次回以降のTopicでは、これらのディレクトリを使って実際にコードを書いていきます。</p>

        <h2>Topicリンク</h2>
        <div class="list-group">
            <a class="list-group-item list-group-item-action" href="/fuel/education/topic1">Topic1：FW（フレームワーク）とは？</a>
            <a class="list-group-item list-group-item-action active" href="/fuel/education/topic2">Topic2：FuelPHPの基本構造</a>
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
