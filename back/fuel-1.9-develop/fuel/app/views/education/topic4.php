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
        <h1>FuelPHPのビュー</h1>
        <p>ここではFuelPHPのビュー（View）について学びます。ビューは、ユーザーに見せるHTMLを生成する部分です。</p>
        
        <h2>ビューとは？</h2>
        <p><strong>ビュー（View）</strong>は、MVCパターンの「V」にあたる部分で、<strong>ユーザーに見せる画面（HTML）を定義するファイル</strong>です。</p>
        <p>ビューファイルは<code>fuel/app/views/</code>ディレクトリに配置され、通常は<code>.php</code>拡張子を持ちます。ビューファイル内では、HTMLとPHPコードを組み合わせて動的なページを作成できます。</p>

        <h2>ビューファイルの配置</h2>
        <p>ビューファイルは、コントローラーごとにディレクトリを分けて配置するのが一般的です：</p>
        
        <div class="card mb-3">
            <div class="card-body">
                <h5 class="card-title">ビューファイルの配置例</h5>
                <pre class="bg-light p-3"><code>fuel/app/views/
├── education/
│   ├── index.php      # 教育ページのトップ
│   ├── topic1.php     # Topic1のページ
│   └── topic2.php     # Topic2のページ
└── welcome/
    ├── index.php      # ウェルカムページ
    └── hello.php      # ハローページ</code></pre>
            </div>
        </div>

        <h2>ビューの読み込み方法</h2>
        <p>コントローラーからビューを読み込むには、<code>View::forge()</code>メソッドを使用します：</p>
        
        <div class="card mb-3">
            <div class="card-body">
                <h5 class="card-title">コントローラーでのビューの読み込み</h5>
                <pre class="bg-light p-3"><code>class Controller_Education extends Controller
{
    public function action_topic1()
    {
        // ビューファイルを読み込む
        // 'education/topic1' は 'fuel/app/views/education/topic1.php' を指す
        return Response::forge(View::forge('education/topic1'));
    }
}</code></pre>
            </div>
        </div>

        <h2>ビューへのデータの渡し方</h2>
        <p>コントローラーからビューにデータを渡すことができます。これにより、動的なコンテンツを表示できます：</p>
        
        <div class="card mb-3">
            <div class="card-body">
                <h5 class="card-title">データをビューに渡す例</h5>
                <pre class="bg-light p-3"><code>// コントローラー
class Controller_Education extends Controller
{
    public function action_topic1()
    {
        $data = array(
            'title' => 'FW（フレームワーク）とは？',
            'content' => 'フレームワークについて学びます。'
        );
        
        // ビューにデータを渡す
        return Response::forge(View::forge('education/topic1', $data));
    }
}

// ビューファイル（education/topic1.php）
&lt;h1&gt;&lt;?php echo $title; ?&gt;&lt;/h1&gt;
&lt;p&gt;&lt;?php echo $content; ?&gt;&lt;/p&gt;</code></pre>
            </div>
        </div>

        <h2>ビューファイルの基本構造</h2>
        <p>ビューファイルは通常のHTMLファイルと同じように記述できますが、PHPコードを埋め込むことができます：</p>
        
        <div class="card mb-3">
            <div class="card-body">
                <h5 class="card-title">ビューファイルの例</h5>
                <pre class="bg-light p-3"><code>&lt;!DOCTYPE html&gt;
&lt;html&gt;
&lt;head&gt;
    &lt;title&gt;&lt;?php echo $title; ?&gt;&lt;/title&gt;
&lt;/head&gt;
&lt;body&gt;
    &lt;h1&gt;&lt;?php echo $title; ?&gt;&lt;/h1&gt;
    &lt;?php if (isset($content)): ?&gt;
        &lt;p&gt;&lt;?php echo $content; ?&gt;&lt;/p&gt;
    &lt;?php endif; ?&gt;
&lt;/body&gt;
&lt;/html&gt;</code></pre>
            </div>
        </div>

        <h2>ビュー内での変数の使用</h2>
        <p>コントローラーから渡された変数は、ビューファイル内で直接使用できます：</p>
        <ul>
            <li><strong>配列として渡されたデータ</strong>：<code>$data['key']</code>としてアクセス</li>
            <li><strong>オブジェクトとして渡されたデータ</strong>：<code>$object->property</code>としてアクセス</li>
        </ul>

        <h2>ビューの継承（レイアウト）</h2>
        <p>複数のビューファイルで共通のレイアウトを使用する場合、ビューを継承することができます。これにより、ヘッダーやフッターなどの共通部分を一度だけ定義できます。</p>
        
        <div class="card mb-3">
            <div class="card-body">
                <h5 class="card-title">ビューの継承の例</h5>
                <pre class="bg-light p-3"><code>// レイアウトファイル（views/layout.php）
&lt;!DOCTYPE html&gt;
&lt;html&gt;
&lt;head&gt;
    &lt;title&gt;&lt;?php echo isset($title) ? $title : 'デフォルトタイトル'; ?&gt;&lt;/title&gt;
&lt;/head&gt;
&lt;body&gt;
    &lt;header&gt;ヘッダー&lt;/header&gt;
    &lt;?php echo $content; ?&gt;
    &lt;footer&gt;フッター&lt;/footer&gt;
&lt;/body&gt;
&lt;/html&gt;

// 個別のビューファイル
&lt;div&gt;
    &lt;h1&gt;コンテンツ&lt;/h1&gt;
    &lt;p&gt;ここにコンテンツが入ります&lt;/p&gt;
&lt;/div&gt;</code></pre>
            </div>
        </div>

        <h2>ビューのベストプラクティス</h2>
        <ul>
            <li><strong>ロジックを最小限に</strong>：ビューファイルには表示に関するコードのみを記述し、ビジネスロジックはコントローラーやモデルに記述する</li>
            <li><strong>セキュリティ</strong>：ユーザー入力データを表示する際は、<code>htmlspecialchars()</code>などでエスケープ処理を行う</li>
            <li><strong>再利用性</strong>：共通部分は別ファイルに分離し、複数のビューで使い回す</li>
            <li><strong>可読性</strong>：HTMLとPHPコードを適切に分離し、読みやすいコードを心がける</li>
        </ul>

        <h2>まとめ</h2>
        <p>ビューを理解することで、以下のことが分かるようになります：</p>
        <ul>
            <li>ビューファイルの配置場所と命名規則</li>
            <li>コントローラーからビューにデータを渡す方法</li>
            <li>ビューファイル内でPHPコードを使用する方法</li>
            <li>動的なHTMLページの作成方法</li>
        </ul>
        <p>次回のTopicでは、コントローラーについて詳しく学びます。</p>

        <h2>Topicリンク</h2>
        <div class="list-group">
            <a class="list-group-item list-group-item-action" href="/fuel/education/topic1">Topic1：FW（フレームワーク）とは？</a>
            <a class="list-group-item list-group-item-action" href="/fuel/education/topic2">Topic2：FuelPHPの基本構造</a>
            <a class="list-group-item list-group-item-action" href="/fuel/education/topic3">Topic3：FuelPHPのルーティング</a>
            <a class="list-group-item list-group-item-action active" href="/fuel/education/topic4">Topic4：FuelPHPのビュー</a>
            <a class="list-group-item list-group-item-action" href="/fuel/education/topic5">Topic5：FuelPHPのコントローラー</a>
            <a class="list-group-item list-group-item-action" href="/fuel/education/topic6">Topic6：FuelPHPのモデル</a>
            <a class="list-group-item list-group-item-action" href="/fuel/education/topic7">Topic7-1：実践編 - お問い合わせフォームを作成しよう（セッション版）</a>
            <a class="list-group-item list-group-item-action" href="/fuel/education/topic8">Topic7-2：実践編 - お問い合わせフォームを作成しよう（データベース版）</a>
        </div>
	</div>
</body>
</html>
