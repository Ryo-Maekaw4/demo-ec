<?php
/**
 * The Contact Controller (Topic7-1: セッション版)
 *
 * @package  app
 * @extends  Controller
 */
class Controller_Sample_Contact_Session extends Controller
{
	/**
	 * フォーム表示
	 *
	 * @access  public
	 * @return  Response
	 */
	public function action_index()
	{
		$data = array();
		return Response::forge(View::forge('sample/contact/session/index', $data));
	}

	/**
	 * 確認画面
	 *
	 * @access  public
	 * @return  Response
	 */
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
			return Response::forge(View::forge('sample/contact/session/index', $data));
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
		return Response::forge(View::forge('sample/contact/session/confirm', $data));
	}

	/**
	 * 完了画面
	 *
	 * @access  public
	 * @return  Response
	 */
	public function action_complete()
	{
		// セッションからデータを取得
		$contact_data = Session::get('contact_data');

		if (!$contact_data) {
			// セッションにデータがない場合はフォームに戻る
			Response::redirect('sample/contact/session');
		}

		// ここで実際のメール送信処理などを行う
		// （今回は簡略化のため、データを表示するだけ）

		// セッションをクリア
		Session::delete('contact_data');

		$data = array(
			'name' => $contact_data['name']
		);
		return Response::forge(View::forge('sample/contact/session/complete', $data));
	}
}
