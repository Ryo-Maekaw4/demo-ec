<?php
/**
 * Fuel is a fast, lightweight, community driven PHP 5.4+ framework.
 *
 * @package    Fuel
 * @version    1.9-dev
 * @author     Fuel Development Team
 * @license    MIT License
 * @copyright  2010-2026 Fuel Development Team
 * @link       https://fuelphp.com
 */

/**
 * The Education Controller.
 *
 * A basic controller example.  Has examples of how to set the
 * response body and status.
 *
 * @package  app
 * @extends  Controller
 */
class Controller_Education extends Controller
{
	/**
	 * The basic welcome message
	 *
	 * @access  public
	 * @return  Response
	 */
	public function action_index()
	{
		return Response::forge(View::forge('education/index'));
	}

	public function action_topic1()
	{
		return Response::forge(View::forge('education/topic1'));
	}

	public function action_topic2()
	{
		return Response::forge(View::forge('education/topic2'));
	}

	public function action_topic3()
	{
		return Response::forge(View::forge('education/topic3'));
	}

	public function action_topic4()
	{
		return Response::forge(View::forge('education/topic4'));
	}

	public function action_topic5()
	{
		return Response::forge(View::forge('education/topic5'));
	}

	public function action_topic6()
	{
		return Response::forge(View::forge('education/topic6'));
	}

	public function action_topic7()
	{
		return Response::forge(View::forge('education/topic7'));
	}

	public function action_topic8()
	{
		return Response::forge(View::forge('education/topic8'));
	}

	public function action_topic9()
	{
		return Response::forge(View::forge('education/topic9'));
	}

	public function action_topic10()
	{
		return Response::forge(View::forge('education/topic10'));
	}
}
