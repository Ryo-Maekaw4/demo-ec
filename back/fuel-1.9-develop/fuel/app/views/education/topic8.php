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
        <h1>Topic7-2：実践編 - お問い合わせフォームを作成しよう（データベース版）</h1>
        <p>Topic7-1ではセッションを使った実装を学びました。今回は、<strong>データベースを使ってお問い合わせを保存</strong>する実装を学びます。モデルを使った実践的な開発を体験できます。</p>
        
        <div class="alert alert-warning">
            <strong>💡 難しかったら：</strong>このページの内容が難しかった場合は、<a href="/fuel/sample/contact/db" target="_blank">実際に動作するサンプルページ</a>を確認してみてください。サンプルソースコードは<code>fuel/app/classes/controller/sample/contact/db.php</code>、<code>fuel/app/classes/model/sample/contact/db.php</code>、<code>fuel/app/views/sample/contact/db/</code>にあります。
        </div>
        
        <div class="alert alert-danger">
            <strong>⚠️ サンプルページを動作させるには：</strong>サンプルページを動作させるには、以下の設定が必要です：
            <ol class="mb-0 mt-2">
                <li><strong>データベースの設定</strong>：<code>fuel/app/config/db.php</code>でデータベース接続情報を設定してください（ホスト名、データベース名、ユーザー名、パスワード）</li>
                <li><strong>テーブルの作成</strong>：以下のSQLを実行して<code>contacts</code>テーブルを作成してください：
                    <pre class="bg-light p-2 mt-2 mb-0"><code>CREATE TABLE contacts (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    email VARCHAR(255) NOT NULL,
    message TEXT NOT NULL,
    created_at DATETIME NOT NULL,
    updated_at DATETIME NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;</code></pre>
                </li>
            </ol>
        </div>
        
        <h2>作成するアプリケーション</h2>
        <p>以下の機能を持つお問い合わせフォームを作成します：</p>
        <ul>
            <li>お問い合わせフォームの表示</li>
            <li>フォームデータの送信と受け取り</li>
            <li>入力データのバリデーション（検証）</li>
            <li>確認画面の表示</li>
            <li><strong>データベースへの保存</strong>（モデルを使用）</li>
            <li>完了画面の表示</li>
        </ul>
        <div class="alert alert-info">
            <strong>前提条件：</strong>Topic7-1を完了していることを前提とします。また、データベースの設定が必要です。
        </div>

        <h2>ステップ1：データベーステーブルの作成</h2>
        <p>まず、お問い合わせを保存するためのテーブルを作成します。以下のSQLを実行してください：</p>
        
        <div class="card mb-3">
            <div class="card-body">
                <h5 class="card-title">contactsテーブルの作成</h5>
                <pre class="bg-light p-3"><code>CREATE TABLE contacts (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    email VARCHAR(255) NOT NULL,
    message TEXT NOT NULL,
    created_at DATETIME NOT NULL,
    updated_at DATETIME NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;</code></pre>
            </div>
        </div>

        <h2>ステップ2：モデルの作成</h2>
        <p>次に、<code>fuel/app/classes/model/contact/db.php</code>を作成します：</p>
        
        <div class="card mb-3">
            <div class="card-body">
                <h5 class="card-title">model/contact/db.php（モデル）</h5>
                <pre class="bg-light p-3"><code>&lt;?php
class Model_Contact_Db extends Model_Crud
{
    // テーブル名を指定
    protected static $_table_name = 'contacts';
    
    // 主キーを指定
    protected static $_primary_key = 'id';
    
    // タイムスタンプを自動更新する
    protected static $_created_at = 'created_at';
    protected static $_updated_at = 'updated_at';
}
?&gt;</code></pre>
            </div>
        </div>

        <h2>ステップ3：ルーティングの設定</h2>
        <p><code>fuel/app/config/routes.php</code>に以下のルートを追加します：</p>
        
        <div class="card mb-3">
            <div class="card-body">
                <h5 class="card-title">routes.phpに追加</h5>
                <pre class="bg-light p-3"><code>// お問い合わせフォーム関連のルート（Topic7-2: データベース版）
'contact/db' => array('contact/db/index'),           // フォーム表示
'contact/db/confirm' => array('contact/db/confirm'),  // 確認画面
'contact/db/complete' => array('contact/db/complete'), // 完了画面</code></pre>
            </div>
        </div>

        <h2>ステップ4：コントローラーの作成</h2>
        <p>次に、<code>fuel/app/classes/controller/contact/db.php</code>を作成します：</p>
        
        <div class="card mb-3">
            <div class="card-body">
                <h5 class="card-title">contact/db.php（コントローラー）</h5>
                <pre class="bg-light p-3"><code>&lt;?php
class Controller_Contact_Db extends Controller
{
    // フォーム表示
    public function action_index()
    {
        $data = array();
        return Response::forge(View::forge('contact/db/index', $data));
    }
    
    // 確認画面
    public function action_confirm()
    {
        // POSTデータを取得
        $name = Input::post('name', '');
        $email = Input::post('email', '');
        $message = Input::post('message', '');
        
        // バリデーション
        $errors = array();
        if (empty($name)) {
            $errors[] = 'お名前は必須です。';
        }
        if (empty($email)) {
            $errors[] = 'メールアドレスは必須です。';
        } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $errors[] = '正しいメールアドレスを入力してください。';
        }
        if (empty($message)) {
            $errors[] = 'お問い合わせ内容は必須です。';
        }
        
        // エラーがある場合はフォームに戻る
        if (!empty($errors)) {
            $data = array(
                'errors' => $errors,
                'name' => $name,
                'email' => $email,
                'message' => $message
            );
            return Response::forge(View::forge('contact/db/index', $data));
        }
        
        // セッションにデータを保存（確認画面で使用）
        Session::set('contact2_data', array(
            'name' => $name,
            'email' => $email,
            'message' => $message
        ));
        
        // 確認画面にデータを渡す
        $data = array(
            'name' => $name,
            'email' => $email,
            'message' => $message
        );
        return Response::forge(View::forge('contact/db/confirm', $data));
    }
    
    // 完了画面（データベースに保存）
    public function action_complete()
    {
        // セッションからデータを取得
        $contact_data = Session::get('contact2_data');
        
        if (!$contact_data) {
            // セッションにデータがない場合はフォームに戻る
            Response::redirect('contact/db');
        }
        
        // データベースに保存
		try {
			$contact = Model_Contact_Db::forge();
            $contact->name = $contact_data['name'];
            $contact->email = $contact_data['email'];
            $contact->message = $contact_data['message'];
            $contact->save();
            
            // 保存成功
            $saved = true;
        } catch (Exception $e) {
            // 保存失敗
            $saved = false;
            $error_message = 'データの保存に失敗しました。';
        }
        
        // セッションをクリア
        Session::delete('contact2_data');
        
        $data = array(
            'name' => $contact_data['name'],
            'saved' => $saved,
            'error_message' => isset($error_message) ? $error_message : null
        );
        return Response::forge(View::forge('contact/db/complete', $data));
    }
}
?&gt;</code></pre>
            </div>
        </div>

        <h2>ステップ5：ビューファイルの作成</h2>
        <p>ビューファイルはTopic7-1とほぼ同じですが、アクション先のURLが異なります。まず、<code>fuel/app/views/contact/db/index.php</code>（フォーム画面）を作成します：</p>
        
        <div class="card mb-3">
            <div class="card-body">
                <h5 class="card-title">contact/db/index.php（フォーム画面）</h5>
                <pre class="bg-light p-3"><code>&lt;!DOCTYPE html&gt;
&lt;html&gt;
&lt;head&gt;
    &lt;meta charset="utf-8"&gt;
    &lt;title&gt;お問い合わせ&lt;/title&gt;
    &lt;link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css"&gt;
&lt;/head&gt;
&lt;body&gt;
    &lt;div class="container mt-5"&gt;
        &lt;h1&gt;お問い合わせフォーム（データベース版）&lt;/h1&gt;
        
        &lt;?php if (isset($errors) && !empty($errors)): ?&gt;
            &lt;div class="alert alert-danger"&gt;
                &lt;ul class="mb-0"&gt;
                    &lt;?php foreach ($errors as $error): ?&gt;
                        &lt;li&gt;&lt;?php echo htmlspecialchars($error); ?&gt;&lt;/li&gt;
                    &lt;?php endforeach; ?&gt;
                &lt;/ul&gt;
            &lt;/div&gt;
        &lt;?php endif; ?&gt;
        
        &lt;form method="post" action="/fuel/contact/db/confirm"&gt;
            &lt;div class="mb-3"&gt;
                &lt;label for="name" class="form-label"&gt;お名前 &lt;span class="text-danger"&gt;*&lt;/span&gt;&lt;/label&gt;
                &lt;input type="text" class="form-control" id="name" name="name" 
                       value="&lt;?php echo isset($name) ? htmlspecialchars($name) : ''; ?&gt;" required&gt;
            &lt;/div&gt;
            
            &lt;div class="mb-3"&gt;
                &lt;label for="email" class="form-label"&gt;メールアドレス &lt;span class="text-danger"&gt;*&lt;/span&gt;&lt;/label&gt;
                &lt;input type="email" class="form-control" id="email" name="email" 
                       value="&lt;?php echo isset($email) ? htmlspecialchars($email) : ''; ?&gt;" required&gt;
            &lt;/div&gt;
            
            &lt;div class="mb-3"&gt;
                &lt;label for="message" class="form-label"&gt;お問い合わせ内容 &lt;span class="text-danger"&gt;*&lt;/span&gt;&lt;/label&gt;
                &lt;textarea class="form-control" id="message" name="message" rows="5" required&gt;
                    &lt;?php echo isset($message) ? htmlspecialchars($message) : ''; ?&gt;
                &lt;/textarea&gt;
            &lt;/div&gt;
            
            &lt;button type="submit" class="btn btn-primary"&gt;確認画面へ&lt;/button&gt;
        &lt;/form&gt;
    &lt;/div&gt;
&lt;/body&gt;
&lt;/html&gt;</code></pre>
            </div>
        </div>

        <p>次に、<code>fuel/app/views/contact/db/confirm.php</code>（確認画面）を作成します：</p>
        
        <div class="card mb-3">
            <div class="card-body">
                <h5 class="card-title">contact/db/confirm.php（確認画面）</h5>
                <pre class="bg-light p-3"><code>&lt;!DOCTYPE html&gt;
&lt;html&gt;
&lt;head&gt;
    &lt;meta charset="utf-8"&gt;
    &lt;title&gt;確認画面&lt;/title&gt;
    &lt;link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css"&gt;
&lt;/head&gt;
&lt;body&gt;
    &lt;div class="container mt-5"&gt;
        &lt;h1&gt;入力内容の確認&lt;/h1&gt;
        &lt;p class="text-muted"&gt;以下の内容でデータベースに保存されます。&lt;/p&gt;
        
        &lt;div class="card"&gt;
            &lt;div class="card-body"&gt;
                &lt;div class="mb-3"&gt;
                    &lt;strong&gt;お名前：&lt;/strong&gt;
                    &lt;?php echo htmlspecialchars($name); ?&gt;
                &lt;/div&gt;
                
                &lt;div class="mb-3"&gt;
                    &lt;strong&gt;メールアドレス：&lt;/strong&gt;
                    &lt;?php echo htmlspecialchars($email); ?&gt;
                &lt;/div&gt;
                
                &lt;div class="mb-3"&gt;
                    &lt;strong&gt;お問い合わせ内容：&lt;/strong&gt;
                    &lt;pre&gt;&lt;?php echo htmlspecialchars($message); ?&gt;&lt;/pre&gt;
                &lt;/div&gt;
            &lt;/div&gt;
        &lt;/div&gt;
        
        &lt;form method="post" action="/fuel/contact/db/complete"&gt;
            &lt;button type="submit" class="btn btn-primary mt-3"&gt;データベースに保存する&lt;/button&gt;
            &lt;a href="/fuel/contact/db" class="btn btn-secondary mt-3"&gt;戻る&lt;/a&gt;
        &lt;/form&gt;
    &lt;/div&gt;
&lt;/body&gt;
&lt;/html&gt;</code></pre>
            </div>
        </div>

        <p>最後に、<code>fuel/app/views/contact/db/complete.php</code>（完了画面）を作成します：</p>
        
        <div class="card mb-3">
            <div class="card-body">
                <h5 class="card-title">contact/db/complete.php（完了画面）</h5>
                <pre class="bg-light p-3"><code>&lt;!DOCTYPE html&gt;
&lt;html&gt;
&lt;head&gt;
    &lt;meta charset="utf-8"&gt;
    &lt;title&gt;送信完了&lt;/title&gt;
    &lt;link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css"&gt;
&lt;/head&gt;
&lt;body&gt;
    &lt;div class="container mt-5"&gt;
        &lt;?php if ($saved): ?&gt;
            &lt;div class="alert alert-success"&gt;
                &lt;h2&gt;お問い合わせありがとうございました&lt;/h2&gt;
                &lt;p&gt;&lt;?php echo htmlspecialchars($name); ?&gt;様、お問い合わせをデータベースに保存しました。&lt;/p&gt;
            &lt;/div&gt;
        &lt;?php else: ?&gt;
            &lt;div class="alert alert-danger"&gt;
                &lt;h2&gt;エラーが発生しました&lt;/h2&gt;
                &lt;p&gt;&lt;?php echo htmlspecialchars($error_message); ?&gt;&lt;/p&gt;
            &lt;/div&gt;
        &lt;?php endif; ?&gt;
        &lt;a href="/fuel/contact/db" class="btn btn-primary"&gt;お問い合わせフォームに戻る&lt;/a&gt;
    &lt;/div&gt;
&lt;/body&gt;
&lt;/html&gt;</code></pre>
            </div>
        </div>

        <h2>ステップ6：データベース設定</h2>
        <p><code>fuel/app/config/db.php</code>でデータベース接続を設定します：</p>
        
        <div class="card mb-3">
            <div class="card-body">
                <h5 class="card-title">db.phpの設定例</h5>
                <pre class="bg-light p-3"><code>return array(
    'default' => array(
        'type' => 'mysqli',
        'connection' => array(
            'hostname' => 'localhost',
            'database' => 'your_database_name',
            'username' => 'your_username',
            'password' => 'your_password'
        )
    )
);</code></pre>
            </div>
        </div>

        <h2>ステップ7：動作確認</h2>
        <p>以下の手順で動作確認を行います：</p>
        <ol>
            <li>データベースに<code>contacts</code>テーブルを作成</li>
            <li><code>fuel/app/config/db.php</code>でデータベース接続を設定</li>
            <li><code>http://localhost/fuel/contact/db</code>にアクセスしてフォームを表示</li>
            <li>フォームにデータを入力して「確認画面へ」をクリック</li>
            <li>確認画面で入力内容を確認</li>
            <li>「データベースに保存する」をクリック</li>
            <li>データベースに保存されているか確認</li>
        </ol>
        <div class="alert alert-info">
            <strong>サンプルページ：</strong><a href="/fuel/sample/contact/db" target="_blank">実際に動作するサンプルページを開く</a>
        </div>

        <h2>Topic7-1との違い</h2>
        <table class="table table-bordered">
            <thead>
                <tr>
                    <th>項目</th>
                    <th>Topic7-1（セッション版）</th>
                    <th>Topic7-2（データベース版）</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td>データの保存先</td>
                    <td>セッション（一時的）</td>
                    <td>データベース（永続的）</td>
                </tr>
                <tr>
                    <td>モデルの使用</td>
                    <td>なし</td>
                    <td>あり（Model_Contact_Db）</td>
                </tr>
                <tr>
                    <td>データの永続化</td>
                    <td>なし（セッション終了で消える）</td>
                    <td>あり（データベースに保存）</td>
                </tr>
                <tr>
                    <td>用途</td>
                    <td>一時的なデータ保存</td>
                    <td>永続的なデータ保存</td>
                </tr>
            </tbody>
        </table>

        <h2>実践のポイント</h2>
        <ul>
            <li><strong>Model_Crud</strong>：簡単なCRUD操作を行うためのモデルクラス</li>
            <li><strong>forge()</strong>：新しいモデルインスタンスを作成</li>
            <li><strong>save()</strong>：データベースに保存</li>
            <li><strong>エラーハンドリング</strong>：try-catch文でエラーを処理</li>
            <li><strong>タイムスタンプ</strong>：created_atとupdated_atを自動更新</li>
        </ul>

        <h2>チャレンジ問題</h2>
        <p>以下の機能を追加してみましょう：</p>
        <ul>
            <li>お問い合わせ一覧ページを作成（保存されたお問い合わせを表示）</li>
            <li>お問い合わせ詳細ページを作成（IDで特定のお問い合わせを表示）</li>
            <li>お問い合わせの削除機能を追加</li>
            <li>ページネーション機能を追加（一覧が多くなった場合）</li>
        </ul>

        <h2>まとめ</h2>
        <p>この実践を通じて、以下のことを学びました：</p>
        <ul>
            <li>モデルの作成と使用方法</li>
            <li>データベースへの保存処理</li>
            <li>Model_Crudを使った基本的なCRUD操作</li>
            <li>エラーハンドリングの実装</li>
            <li>セッションとデータベースの使い分け</li>
        </ul>
        <p>これで、FuelPHPを使った本格的なWebアプリケーション開発の基礎を理解できました！</p>

        <h2>Topicリンク</h2>
        <div class="list-group">
            <a class="list-group-item list-group-item-action" href="/fuel/education/topic1">Topic1：FW（フレームワーク）とは？</a>
            <a class="list-group-item list-group-item-action" href="/fuel/education/topic2">Topic2：FuelPHPの基本構造</a>
            <a class="list-group-item list-group-item-action" href="/fuel/education/topic3">Topic3：FuelPHPのルーティング</a>
            <a class="list-group-item list-group-item-action" href="/fuel/education/topic4">Topic4：FuelPHPのビュー</a>
            <a class="list-group-item list-group-item-action" href="/fuel/education/topic5">Topic5：FuelPHPのコントローラー</a>
            <a class="list-group-item list-group-item-action" href="/fuel/education/topic6">Topic6：FuelPHPのモデル</a>
            <a class="list-group-item list-group-item-action" href="/fuel/education/topic7">Topic7-1：実践編 - お問い合わせフォームを作成しよう（セッション版）</a>
            <a class="list-group-item list-group-item-action active" href="/fuel/education/topic8">Topic7-2：実践編 - お問い合わせフォームを作成しよう（データベース版）</a>
        </div>
	</div>
</body>
</html>
