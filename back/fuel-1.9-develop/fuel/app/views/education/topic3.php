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
        <h1>FuelPHPのルーティング</h1>
        <p>ここではFuelPHPのルーティング機能について学びます。ルーティングは、URLとコントローラーのアクションを結びつける重要な機能です。</p>
        
        <h2>ルーティングとは？</h2>
        <p><strong>ルーティング（Routing）</strong>とは、ユーザーがアクセスしたURLに対して、どのコントローラーのどのメソッド（アクション）を実行するかを決定する機能です。</p>
        <p>例えば、ユーザーが<code>http://localhost/fuel/education/topic1</code>にアクセスしたとき、FuelPHPは「educationコントローラーのtopic1メソッドを実行する」と判断します。この判断を下すのがルーティング機能です。</p>

        <h2>routes.phpファイル</h2>
        <p>FuelPHPでは、ルーティングの設定は<code>fuel/app/config/routes.php</code>ファイルで行います。このファイルを開くと、以下のような設定が記述されています：</p>
        
        <div class="card mb-3">
            <div class="card-body">
                <h5 class="card-title">routes.phpの基本構造</h5>
                <pre class="bg-light p-3"><code>return array(
    // デフォルトルート（トップページ）
    '_root_' => 'welcome/index',
    
    // 404エラーページ
    '_404_' => 'welcome/404',
    
    // カスタムルート
    'education' => array('education/index'),
    'education/topic1' => array('education/topic1'),
    'education/topic2' => array('education/topic2'),
);</code></pre>
            </div>
        </div>

        <h2>ルーティングの書き方</h2>
        <p>ルーティングは、<strong>「URLパターン」=>「コントローラー/アクション」</strong>の形式で記述します。</p>
        
        <h3>基本的な書き方</h3>
        <ul>
            <li><strong>シンプルなルート</strong>：
                <pre class="bg-light p-2"><code>'education' => array('education/index')</code></pre>
                <p>URL：<code>/fuel/education</code> → <code>Controller_Education::action_index()</code>を実行</p>
            </li>
            <li><strong>パスを含むルート</strong>：
                <pre class="bg-light p-2"><code>'education/topic1' => array('education/topic1')</code></pre>
                <p>URL：<code>/fuel/education/topic1</code> → <code>Controller_Education::action_topic1()</code>を実行</p>
            </li>
        </ul>

        <h3>特殊なルート</h3>
        <ul>
            <li><strong>_root_</strong>：デフォルトルート（トップページ）
                <pre class="bg-light p-2"><code>'_root_' => 'welcome/index'</code></pre>
                <p>URL：<code>/fuel/</code>または<code>/fuel/index.php</code> → <code>Controller_Welcome::action_index()</code>を実行</p>
            </li>
            <li><strong>_404_</strong>：404エラーページ
                <pre class="bg-light p-2"><code>'_404_' => 'welcome/404'</code></pre>
                <p>存在しないURLにアクセスした場合に表示されるページ</p>
            </li>
        </ul>

        <h2>パラメータ付きルート</h2>
        <p>URLにパラメータを含めることもできます。例えば、ユーザーIDをURLに含める場合：</p>
        
        <div class="card mb-3">
            <div class="card-body">
                <h5 class="card-title">パラメータ付きルートの例</h5>
                <pre class="bg-light p-3"><code>// routes.php
'user/:id' => array('user/profile', 'id' => '$1'),

// コントローラー
class Controller_User extends Controller
{
    public function action_profile($id)
    {
        // $idにURLのパラメータが入る
        // 例：/fuel/user/123 → $id = 123
    }
}</code></pre>
            </div>
        </div>

        <h2>ルーティングの優先順位</h2>
        <p>FuelPHPは、<code>routes.php</code>の<strong>上から順に</strong>ルートをチェックします。最初にマッチしたルートが使用されます。</p>
        <div class="alert alert-warning">
            <strong>注意：</strong>より具体的なルート（例：<code>education/topic1</code>）を、より一般的なルート（例：<code>education/:id</code>）より<strong>上に</strong>記述する必要があります。
        </div>

        <h2>実際の動作例</h2>
        <p>現在のプロジェクトでは、以下のようなルーティングが設定されています：</p>
        <ol>
            <li>ユーザーが<code>http://localhost/fuel/education/topic1</code>にアクセス</li>
            <li>FuelPHPが<code>routes.php</code>を確認</li>
            <li><code>'education/topic1' => array('education/topic1')</code>にマッチ</li>
            <li><code>Controller_Education</code>クラスの<code>action_topic1()</code>メソッドを実行</li>
            <li>メソッド内で<code>View::forge('education/topic1')</code>が呼ばれ、ビューファイルが読み込まれる</li>
            <li>HTMLが生成され、ブラウザに表示される</li>
        </ol>

        <h2>ルーティングのメリット</h2>
        <ul>
            <li><strong>URLの見た目を整理できる</strong>：<code>/fuel/education/topic1</code>のような分かりやすいURLにできる</li>
            <li><strong>SEOに有利</strong>：検索エンジンが理解しやすいURLになる</li>
            <li><strong>柔軟性</strong>：URLを変更しても、コントローラーやビューのコードを変更する必要がない</li>
            <li><strong>セキュリティ</strong>：実際のファイル構造を隠すことができる</li>
        </ul>

        <h2>まとめ</h2>
        <p>ルーティングを理解することで、以下のことが分かるようになります：</p>
        <ul>
            <li>新しいページを追加するには、<code>routes.php</code>にルートを追加する</li>
            <li>コントローラーに新しいアクション（メソッド）を追加する</li>
            <li>ビューファイルを作成する</li>
            <li>URLとコントローラーの関係を理解する</li>
        </ul>
        <p>次回のTopicでは、ビューについて詳しく学びます。</p>

        <h2>Topicリンク</h2>
        <div class="list-group">
            <a class="list-group-item list-group-item-action" href="/fuel/education/topic1">Topic1：FW（フレームワーク）とは？</a>
            <a class="list-group-item list-group-item-action" href="/fuel/education/topic2">Topic2：FuelPHPの基本構造</a>
            <a class="list-group-item list-group-item-action active" href="/fuel/education/topic3">Topic3：FuelPHPのルーティング</a>
            <a class="list-group-item list-group-item-action" href="/fuel/education/topic4">Topic4：FuelPHPのビュー</a>
            <a class="list-group-item list-group-item-action" href="/fuel/education/topic5">Topic5：FuelPHPのコントローラー</a>
            <a class="list-group-item list-group-item-action" href="/fuel/education/topic6">Topic6：FuelPHPのモデル</a>
            <a class="list-group-item list-group-item-action" href="/fuel/education/topic7">Topic7-1：実践編 - お問い合わせフォームを作成しよう（セッション版）</a>
            <a class="list-group-item list-group-item-action" href="/fuel/education/topic8">Topic7-2：実践編 - お問い合わせフォームを作成しよう（データベース版）</a>
        </div>
	</div>
</body>
</html>
