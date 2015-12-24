<?php

class ViewController extends Zend_Controller_Action
{

	public function userAction() {

		$user_model = new Application_Model_User();
		$users = $user_model->fetchAll();
		$this->view->users = $users;


		$review_model = new Application_Model_Review();
		$reviews = $review_model->fetchAll();
		$this->view->reviews = $reviews;

		$song_model = new Application_Model_Song();
		$songs = $song_model->fetchAll();
		$this->view->songs = $songs;

	}

}