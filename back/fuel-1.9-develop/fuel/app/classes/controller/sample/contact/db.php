<?php
/**
 * The Contact Controller (Topic7-2: データベース版)
 *
 * @package  app
 * @extends  Controller
 */
class Controller_Sample_Contact_Db extends Controller
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
		return Response::forge(View::forge('sample/contact/db/index', $data));
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
			return Response::forge(View::forge('sample/contact/db/index', $data));
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
		return Response::forge(View::forge('sample/contact/db/confirm', $data));
	}

	/**
	 * 完了画面（データベースに保存）
	 *
	 * @access  public
	 * @return  Response
	 */
	public function action_complete()
	{
		// セッションからデータを取得
		$contact_data = Session::get('contact2_data');

		if (!$contact_data) {
			// セッションにデータがない場合はフォームに戻る
			Response::redirect('sample/contact/db');
		}

		// データベースに保存
		$saved = false;
		$error_message = null;
		
		try {
			$contact = Model_Sample_Contact_Db::forge();
			$contact->name = $contact_data['name'];
			$contact->email = $contact_data['email'];
			$contact->message = $contact_data['message'];
			$contact->save();
			
			// 保存成功
			$saved = true;
		} catch (Exception $e) {
			// 保存失敗
			$saved = false;
			$error_message = 'データの保存に失敗しました。データベースの設定を確認してください。';
		}

		// セッションをクリア
		Session::delete('contact2_data');

		$data = array(
			'name' => $contact_data['name'],
			'saved' => $saved,
			'error_message' => $error_message
		);
		return Response::forge(View::forge('sample/contact/db/complete', $data));
	}
}
