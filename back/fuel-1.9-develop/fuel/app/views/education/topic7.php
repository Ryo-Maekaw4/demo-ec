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
        <h1>Topic7-1：実践編 - お問い合わせフォームを作成しよう（セッション版）</h1>
        <p>これまで学んだ知識を活用して、実際にお問い合わせフォームを作成してみましょう。この実践では、<strong>セッションを使ってデータを一時保存</strong>する方法を学びます。データベースを使わないシンプルな実装です。</p>
        
        <div class="alert alert-warning">
            <strong>💡 難しかったら：</strong>このページの内容が難しかった場合は、<a href="/fuel/sample/contact/session" target="_blank">実際に動作するサンプルページ</a>を確認してみてください。サンプルソースコードは<code>fuel/app/classes/controller/sample/contact/session.php</code>と<code>fuel/app/views/sample/contact/session/</code>にあります。
        </div>
        
        <h2>作成するアプリケーション</h2>
        <p>以下の機能を持つお問い合わせフォームを作成します：</p>
        <ul>
            <li>お問い合わせフォームの表示</li>
            <li>フォームデータの送信と受け取り</li>
            <li>入力データのバリデーション（検証）</li>
            <li>確認画面の表示（セッションにデータを保存）</li>
            <li>完了画面の表示</li>
        </ul>
        <div class="alert alert-info">
            <strong>注意：</strong>この実践ではデータベースは使用しません。セッションを使ってデータを一時保存します。データベースを使った実装は<strong>Topic7-2</strong>で学びます。
        </div>

        <h2>ステップ1：ルーティングの設定</h2>
        <p>まず、<code>fuel/app/config/routes.php</code>に以下のルートを追加します：</p>
        
        <div class="card mb-3">
            <div class="card-body">
                <h5 class="card-title">routes.phpに追加</h5>
                <pre class="bg-light p-3"><code>// お問い合わせフォーム関連のルート（Topic7-1: セッション版）
'contact/session' => array('contact/session/index'),           // フォーム表示
'contact/session/confirm' => array('contact/session/confirm'),  // 確認画面
'contact/session/complete' => array('contact/session/complete'), // 完了画面</code></pre>
            </div>
        </div>

        <h2>ステップ2：コントローラーの作成</h2>
        <p>次に、<code>fuel/app/classes/controller/contact/session.php</code>を作成します：</p>
        
        <div class="card mb-3">
            <div class="card-body">
                <h5 class="card-title">contact/session.php（コントローラー）</h5>
                <pre class="bg-light p-3"><code>&lt;?php
class Controller_Contact_Session extends Controller
{
    // フォーム表示
    public function action_index()
    {
        $data = array();
        return Response::forge(View::forge('contact/session/index', $data));
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
            return Response::forge(View::forge('contact/session/index', $data));
        }
        
        // セッションにデータを保存
        Session::set('contact_data', array(
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
        return Response::forge(View::forge('contact/session/confirm', $data));
    }
    
    // 完了画面
    public function action_complete()
    {
        // セッションからデータを取得
        $contact_data = Session::get('contact_data');
        
        if (!$contact_data) {
            // セッションにデータがない場合はフォームに戻る
            Response::redirect('contact/session');
        }
        
        // ここで実際のメール送信処理などを行う
        // （今回は簡略化のため、データを表示するだけ）
        
        // セッションをクリア
        Session::delete('contact_data');
        
        $data = array(
            'name' => $contact_data['name']
        );
        return Response::forge(View::forge('contact/session/complete', $data));
    }
}
?&gt;</code></pre>
            </div>
        </div>

        <h2>ステップ3：ビューファイルの作成</h2>
        <p>次に、ビューファイルを作成します。まず、<code>fuel/app/views/contact/session/index.php</code>（フォーム画面）を作成します：</p>
        
        <div class="card mb-3">
            <div class="card-body">
                <h5 class="card-title">contact/session/index.php（フォーム画面）</h5>
                <pre class="bg-light p-3"><code>&lt;!DOCTYPE html&gt;
&lt;html&gt;
&lt;head&gt;
    &lt;meta charset="utf-8"&gt;
    &lt;title&gt;お問い合わせ&lt;/title&gt;
    &lt;link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css"&gt;
&lt;/head&gt;
&lt;body&gt;
    &lt;div class="container mt-5"&gt;
        &lt;h1&gt;お問い合わせフォーム&lt;/h1&gt;
        
        &lt;?php if (isset($errors) && !empty($errors)): ?&gt;
            &lt;div class="alert alert-danger"&gt;
                &lt;ul class="mb-0"&gt;
                    &lt;?php foreach ($errors as $error): ?&gt;
                        &lt;li&gt;&lt;?php echo htmlspecialchars($error); ?&gt;&lt;/li&gt;
                    &lt;?php endforeach; ?&gt;
                &lt;/ul&gt;
            &lt;/div&gt;
        &lt;?php endif; ?&gt;
        
        &lt;form method="post" action="/fuel/contact/session/confirm"&gt;
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

        <p>次に、<code>fuel/app/views/contact/session/confirm.php</code>（確認画面）を作成します：</p>
        
        <div class="card mb-3">
            <div class="card-body">
                <h5 class="card-title">contact/session/confirm.php（確認画面）</h5>
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
        
        &lt;form method="post" action="/fuel/contact/session/complete"&gt;
            &lt;button type="submit" class="btn btn-primary mt-3"&gt;送信する&lt;/button&gt;
            &lt;a href="/fuel/contact/session" class="btn btn-secondary mt-3"&gt;戻る&lt;/a&gt;
        &lt;/form&gt;
    &lt;/div&gt;
&lt;/body&gt;
&lt;/html&gt;</code></pre>
            </div>
        </div>

        <p>最後に、<code>fuel/app/views/contact/session/complete.php</code>（完了画面）を作成します：</p>
        
        <div class="card mb-3">
            <div class="card-body">
                <h5 class="card-title">contact/session/complete.php（完了画面）</h5>
                <pre class="bg-light p-3"><code>&lt;!DOCTYPE html&gt;
&lt;html&gt;
&lt;head&gt;
    &lt;meta charset="utf-8"&gt;
    &lt;title&gt;送信完了&lt;/title&gt;
    &lt;link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css"&gt;
&lt;/head&gt;
&lt;body&gt;
    &lt;div class="container mt-5"&gt;
        &lt;div class="alert alert-success"&gt;
            &lt;h2&gt;お問い合わせありがとうございました&lt;/h2&gt;
            &lt;p&gt;&lt;?php echo htmlspecialchars($name); ?&gt;様、お問い合わせを受け付けました。&lt;/p&gt;
        &lt;/div&gt;
        &lt;a href="/fuel/contact/session" class="btn btn-primary"&gt;お問い合わせフォームに戻る&lt;/a&gt;
    &lt;/div&gt;
&lt;/body&gt;
&lt;/html&gt;</code></pre>
            </div>
        </div>

        <h2>ステップ4：動作確認</h2>
        <p>以下の手順で動作確認を行います：</p>
        <ol>
            <li><code>http://localhost/fuel/contact/session</code>にアクセスしてフォームを表示</li>
            <li>フォームにデータを入力して「確認画面へ」をクリック</li>
            <li>確認画面で入力内容を確認</li>
            <li>「送信する」をクリックして完了画面を表示</li>
        </ol>
        <div class="alert alert-info">
            <strong>サンプルページ：</strong><a href="/fuel/sample/contact/session" target="_blank">実際に動作するサンプルページを開く</a>
        </div>

        <h2>実践のポイント</h2>
        <ul>
            <li><strong>Input::post()</strong>：POSTデータを取得する</li>
            <li><strong>Session</strong>：セッションを使ってデータを一時保存する</li>
            <li><strong>バリデーション</strong>：入力データの検証を行う</li>
            <li><strong>htmlspecialchars()</strong>：XSS対策のためのエスケープ処理</li>
            <li><strong>Response::redirect()</strong>：リダイレクト処理</li>
        </ul>

        <h2>チャレンジ問題</h2>
        <p>以下の機能を追加してみましょう：</p>
        <ul>
            <li>お問い合わせ種別（選択肢：技術的な質問、バグ報告、その他）を追加</li>
            <li>電話番号の入力欄を追加（任意項目）</li>
            <li>確認画面で「戻る」ボタンを押したときに、入力内容が保持されるようにする</li>
        </ul>

        <h2>まとめ</h2>
        <p>この実践を通じて、以下のことを学びました：</p>
        <ul>
            <li>コントローラー、ビュー、ルーティングを組み合わせた実装</li>
            <li>フォームデータの受け取りと処理</li>
            <li>バリデーションの実装</li>
            <li>セッションの使用</li>
            <li>セキュリティ対策（XSS対策）</li>
        </ul>
        <p>これで、FuelPHPを使った基本的なWebアプリケーション開発の流れを理解できました！</p>

        <h2>Topicリンク</h2>
        <div class="list-group">
            <a class="list-group-item list-group-item-action" href="/fuel/education/topic1">Topic1：FW（フレームワーク）とは？</a>
            <a class="list-group-item list-group-item-action" href="/fuel/education/topic2">Topic2：FuelPHPの基本構造</a>
            <a class="list-group-item list-group-item-action" href="/fuel/education/topic3">Topic3：FuelPHPのルーティング</a>
            <a class="list-group-item list-group-item-action" href="/fuel/education/topic4">Topic4：FuelPHPのビュー</a>
            <a class="list-group-item list-group-item-action" href="/fuel/education/topic5">Topic5：FuelPHPのコントローラー</a>
            <a class="list-group-item list-group-item-action" href="/fuel/education/topic6">Topic6：FuelPHPのモデル</a>
            <a class="list-group-item list-group-item-action active" href="/fuel/education/topic7">Topic7-1：実践編 - お問い合わせフォームを作成しよう（セッション版）</a>
            <a class="list-group-item list-group-item-action" href="/fuel/education/topic8">Topic7-2：実践編 - お問い合わせフォームを作成しよう（データベース版）</a>
        </div>
	</div>
</body>
</html>
