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
        <h1>FuelPHPのモデル</h1>
        <p>ここではFuelPHPのモデル（Model）について学びます。モデルは、MVCパターンの「M」にあたる部分で、データベースとのやり取りやビジネスロジックを担当します。</p>
        
        <h2>モデルとは？</h2>
        <p><strong>モデル（Model）</strong>は、データベースとのやり取りや、ビジネスロジック（アプリケーションの核心的な処理）を担当するクラスです。</p>
        <p>モデルクラスは<code>fuel/app/classes/model/</code>ディレクトリに配置され、<code>Model_</code>というプレフィックスで始まるクラス名を使用します。</p>

        <h2>モデルの基本構造</h2>
        <p>FuelPHPでは、モデルは通常<code>Model_Crud</code>や<code>Model_Orm</code>を継承して作成します：</p>
        
        <div class="card mb-3">
            <div class="card-body">
                <h5 class="card-title">モデルの基本構造</h5>
                <pre class="bg-light p-3"><code>&lt;?php
class Model_Topic extends Model_Crud
{
    // テーブル名を指定
    protected static $_table_name = 'topics';
    
    // 主キーを指定（デフォルトは'id'）
    protected static $_primary_key = 'id';
}
?&gt;</code></pre>
            </div>
        </div>

        <h2>モデルの役割</h2>
        <p>モデルは以下のような処理を担当します：</p>
        <ul>
            <li><strong>データベース操作</strong>：データの取得、追加、更新、削除</li>
            <li><strong>データの検証</strong>：入力データのバリデーション</li>
            <li><strong>ビジネスロジック</strong>：アプリケーション固有の処理</li>
            <li><strong>データの加工</strong>：データのフォーマットや計算</li>
        </ul>

        <h2>基本的なデータベース操作</h2>
        <p>FuelPHPのモデルでは、以下のようなメソッドを使用してデータベース操作を行います：</p>
        
        <div class="card mb-3">
            <div class="card-body">
                <h5 class="card-title">データベース操作の例</h5>
                <pre class="bg-light p-3"><code>// すべてのレコードを取得
$topics = Model_Topic::find('all');

// IDで1件取得
$topic = Model_Topic::find_by_pk(1);

// 条件を指定して取得
$topics = Model_Topic::find('all', array(
    'where' => array(
        array('status', '=', 'active')
    )
));

// 新しいレコードを作成
$topic = Model_Topic::forge();
$topic->title = '新しいトピック';
$topic->content = '内容';
$topic->save();

// レコードを更新
$topic = Model_Topic::find_by_pk(1);
$topic->title = '更新されたタイトル';
$topic->save();

// レコードを削除
$topic = Model_Topic::find_by_pk(1);
$topic->delete();</code></pre>
            </div>
        </div>

        <h2>コントローラーでのモデルの使用</h2>
        <p>コントローラーからモデルを使用する例：</p>
        
        <div class="card mb-3">
            <div class="card-body">
                <h5 class="card-title">コントローラーでのモデル使用例</h5>
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
    
    public function action_show($id)
    {
        // IDでトピックを取得
        $topic = Model_Topic::find_by_pk($id);
        
        if (!$topic) {
            // トピックが見つからない場合、404エラー
            throw new HttpNotFoundException;
        }
        
        // ビューにデータを渡す
        $data = array(
            'topic' => $topic
        );
        
        return Response::forge(View::forge('education/show', $data));
    }
}</code></pre>
            </div>
        </div>

        <h2>ORM（Object-Relational Mapping）</h2>
        <p>FuelPHPでは、<strong>ORM（Object-Relational Mapping）</strong>パッケージを使用することで、より高度なデータベース操作が可能になります。</p>
        <p>ORMを使用すると、オブジェクト指向の方法でデータベースを操作できます：</p>
        
        <div class="card mb-3">
            <div class="card-body">
                <h5 class="card-title">ORMの使用例</h5>
                <pre class="bg-light p-3"><code>// ORMモデルの定義
class Model_Topic extends \Orm\Model
{
    protected static $_table_name = 'topics';
    protected static $_primary_key = array('id');
    
    // リレーションの定義
    protected static $_has_many = array(
        'comments' => array(
            'model_to' => 'Model_Comment',
            'key_from' => 'id',
            'key_to' => 'topic_id'
        )
    );
}

// ORMでのデータ操作
$topic = Model_Topic::find(1);
$comments = $topic->comments; // 関連するコメントを取得</code></pre>
            </div>
        </div>

        <h2>データベース設定</h2>
        <p>データベースへの接続設定は、<code>fuel/app/config/db.php</code>ファイルで行います：</p>
        
        <div class="card mb-3">
            <div class="card-body">
                <h5 class="card-title">データベース設定の例</h5>
                <pre class="bg-light p-3"><code>return array(
    'default' => array(
        'type' => 'mysqli',
        'connection' => array(
            'hostname' => 'localhost',
            'database' => 'mydatabase',
            'username' => 'myuser',
            'password' => 'mypassword'
        )
    )
);</code></pre>
            </div>
        </div>

        <h2>モデルのベストプラクティス</h2>
        <ul>
            <li><strong>1つのテーブルにつき1つのモデル</strong>：テーブルごとにモデルクラスを作成する</li>
            <li><strong>ビジネスロジックをモデルに記述</strong>：データに関する処理はモデルに集約する</li>
            <li><strong>再利用可能なメソッドを作成</strong>：よく使う処理はモデル内にメソッドとして定義する</li>
            <li><strong>バリデーション</strong>：データの検証ロジックをモデルに実装する</li>
        </ul>

        <h2>まとめ</h2>
        <p>モデルを理解することで、以下のことが分かるようになります：</p>
        <ul>
            <li>モデルの役割と基本的な構造</li>
            <li>データベース操作の基本（CRUD操作）</li>
            <li>コントローラーからモデルを使用する方法</li>
            <li>ORMの基本的な使い方</li>
            <li>データベース設定の方法</li>
        </ul>
        <p>これで、FuelPHPのMVCパターン（Model-View-Controller）の全体像を理解できました。これらの知識を組み合わせることで、本格的なWebアプリケーションを開発できるようになります。</p>

        <h2>Topicリンク</h2>
        <div class="list-group">
            <a class="list-group-item list-group-item-action" href="/fuel/education/topic1">Topic1：FW（フレームワーク）とは？</a>
            <a class="list-group-item list-group-item-action" href="/fuel/education/topic2">Topic2：FuelPHPの基本構造</a>
            <a class="list-group-item list-group-item-action" href="/fuel/education/topic3">Topic3：FuelPHPのルーティング</a>
            <a class="list-group-item list-group-item-action" href="/fuel/education/topic4">Topic4：FuelPHPのビュー</a>
            <a class="list-group-item list-group-item-action" href="/fuel/education/topic5">Topic5：FuelPHPのコントローラー</a>
            <a class="list-group-item list-group-item-action active" href="/fuel/education/topic6">Topic6：FuelPHPのモデル</a>
            <a class="list-group-item list-group-item-action" href="/fuel/education/topic7">Topic7-1：実践編 - お問い合わせフォームを作成しよう（セッション版）</a>
            <a class="list-group-item list-group-item-action" href="/fuel/education/topic8">Topic7-2：実践編 - お問い合わせフォームを作成しよう（データベース版）</a>
        </div>
	</div>
</body>
</html>
