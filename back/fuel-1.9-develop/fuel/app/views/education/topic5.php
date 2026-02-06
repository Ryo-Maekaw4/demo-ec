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
        <h1>FuelPHPのコントローラー</h1>
        <p>ここではFuelPHPのコントローラー（Controller）について学びます。コントローラーは、MVCパターンの「C」にあたる部分で、リクエストを処理し、モデルとビューを繋ぐ役割を担います。</p>
        
        <h2>コントローラーとは？</h2>
        <p><strong>コントローラー（Controller）</strong>は、ユーザーからのリクエストを受け取り、適切な処理を実行して、ビューを返す役割を担います。</p>
        <p>コントローラークラスは<code>fuel/app/classes/controller/</code>ディレクトリに配置され、<code>Controller_</code>というプレフィックスで始まるクラス名を使用します。</p>

        <h2>コントローラーの基本構造</h2>
        <p>コントローラークラスは、<code>Controller</code>クラスを継承します：</p>
        
        <div class="card mb-3">
            <div class="card-body">
                <h5 class="card-title">コントローラーの基本構造</h5>
                <pre class="bg-light p-3"><code>&lt;?php
class Controller_Education extends Controller
{
    public function action_index()
    {
        return Response::forge(View::forge('education/index'));
    }
    
    public function action_topic1()
    {
        return Response::forge(View::forge('education/topic1'));
    }
}
?&gt;</code></pre>
            </div>
        </div>

        <h2>アクションメソッド</h2>
        <p>コントローラー内の<code>action_</code>で始まるメソッドは、<strong>アクションメソッド</strong>と呼ばれ、URLから直接呼び出すことができます。</p>
        <ul>
            <li><code>action_index()</code>：デフォルトのアクション（URLにアクション名が指定されていない場合に呼ばれる）</li>
            <li><code>action_topic1()</code>：<code>/fuel/education/topic1</code>にアクセスしたときに呼ばれる</li>
            <li><code>action_show($id)</code>：パラメータを受け取るアクション</li>
        </ul>

        <h2>コントローラーの命名規則</h2>
        <ul>
            <li><strong>クラス名</strong>：<code>Controller_</code>で始まり、続いてコントローラー名（大文字で始まる）</li>
            <li><strong>ファイル名</strong>：クラス名を小文字にし、アンダースコアをそのまま使用（例：<code>Controller_Education</code> → <code>education.php</code>）</li>
            <li><strong>アクションメソッド</strong>：<code>action_</code>で始まる</li>
        </ul>

        <h2>コントローラーの役割</h2>
        <p>コントローラーは以下のような処理を行います：</p>
        <ol>
            <li><strong>リクエストの受け取り</strong>：URLパラメータやフォームデータを受け取る</li>
            <li><strong>モデルの呼び出し</strong>：必要に応じてモデルからデータを取得</li>
            <li><strong>ビジネスロジックの実行</strong>：データの加工やバリデーションなど</li>
            <li><strong>ビューの読み込み</strong>：処理結果をビューに渡して表示</li>
        </ol>

        <h2>コントローラーでのデータ処理</h2>
        <p>コントローラーでは、リクエストデータを処理し、ビューに渡すことができます：</p>
        
        <div class="card mb-3">
            <div class="card-body">
                <h5 class="card-title">データ処理の例</h5>
                <pre class="bg-light p-3"><code>class Controller_Education extends Controller
{
    public function action_topic1()
    {
        // リクエストデータの取得
        $id = Input::get('id', 1); // GETパラメータから取得（デフォルト値：1）
        
        // データの加工
        $title = 'Topic ' . $id;
        $content = 'これはTopic' . $id . 'の内容です。';
        
        // ビューにデータを渡す
        $data = array(
            'title' => $title,
            'content' => $content
        );
        
        return Response::forge(View::forge('education/topic1', $data));
    }
}</code></pre>
            </div>
        </div>

        <h2>モデルとの連携</h2>
        <p>コントローラーは、モデルを呼び出してデータベースからデータを取得することもできます：</p>
        
        <div class="card mb-3">
            <div class="card-body">
                <h5 class="card-title">モデルとの連携例</h5>
                <pre class="bg-light p-3"><code>class Controller_Education extends Controller
{
    public function action_list()
    {
        // モデルからデータを取得
        $topics = Model_Topic::find('all');
        
        // ビューにデータを渡す
        $data = array(
            'topics' => $topics
        );
        
        return Response::forge(View::forge('education/list', $data));
    }
}</code></pre>
            </div>
        </div>

        <h2>リダイレクト</h2>
        <p>コントローラーから別のページにリダイレクトすることもできます：</p>
        
        <div class="card mb-3">
            <div class="card-body">
                <h5 class="card-title">リダイレクトの例</h5>
                <pre class="bg-light p-3"><code>class Controller_Education extends Controller
{
    public function action_save()
    {
        // データの保存処理
        // ...
        
        // 保存後、一覧ページにリダイレクト
        Response::redirect('education/list');
    }
}</code></pre>
            </div>
        </div>

        <h2>コントローラーのベストプラクティス</h2>
        <ul>
            <li><strong>シンプルに保つ</strong>：コントローラーは薄く保ち、ビジネスロジックはモデルに記述する</li>
            <li><strong>1つのアクションにつき1つの責任</strong>：各アクションメソッドは1つの処理のみを行う</li>
            <li><strong>エラーハンドリング</strong>：適切なエラーハンドリングを実装する</li>
            <li><strong>セキュリティ</strong>：入力データのバリデーションとサニタイズを行う</li>
        </ul>

        <h2>まとめ</h2>
        <p>コントローラーを理解することで、以下のことが分かるようになります：</p>
        <ul>
            <li>コントローラーの役割と基本的な構造</li>
            <li>アクションメソッドの作成方法</li>
            <li>リクエストデータの処理方法</li>
            <li>モデルとビューを繋ぐ方法</li>
            <li>リダイレクトの実装方法</li>
        </ul>
        <p>次回のTopicでは、モデルについて詳しく学びます。</p>

        <h2>Topicリンク</h2>
        <div class="list-group">
            <a class="list-group-item list-group-item-action" href="/fuel/education/topic1">Topic1：FW（フレームワーク）とは？</a>
            <a class="list-group-item list-group-item-action" href="/fuel/education/topic2">Topic2：FuelPHPの基本構造</a>
            <a class="list-group-item list-group-item-action" href="/fuel/education/topic3">Topic3：FuelPHPのルーティング</a>
            <a class="list-group-item list-group-item-action" href="/fuel/education/topic4">Topic4：FuelPHPのビュー</a>
            <a class="list-group-item list-group-item-action active" href="/fuel/education/topic5">Topic5：FuelPHPのコントローラー</a>
            <a class="list-group-item list-group-item-action" href="/fuel/education/topic6">Topic6：FuelPHPのモデル</a>
            <a class="list-group-item list-group-item-action" href="/fuel/education/topic7">Topic7-1：実践編 - お問い合わせフォームを作成しよう（セッション版）</a>
            <a class="list-group-item list-group-item-action" href="/fuel/education/topic8">Topic7-2：実践編 - お問い合わせフォームを作成しよう（データベース版）</a>
        </div>
	</div>
</body>
</html>
