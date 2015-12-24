<?php

class AddController extends Zend_Controller_Action
{

	public function init()
	{
		/* Initialize action controller here */
	}

	public function indexAction()
	{
		// action body

	}

	public function newalbumAction() {

		$request = $this->getRequest();

		if($request->isPost()) {
			$title = $request->getParam('title');
			$price = $request->getParam('price');
			$release_year = $request->getParam('release_year');
			$description = $request->getParam('description');

			$db = Zend_Registry::get('db');
			$sql = "INSERT INTO album (title, price, release_year, description) VALUES ('$title', '$price', '$release_year', '$description');";
			$db->query($sql);

			$_SESSION['success'] = true;
			$_SESSION['message'] = "Album added to DB successfully.";

		}
	}

	public function newsongAction() {


		$request = $this->getRequest();

		if($request->isPost()) {
			$album_id = $request->getParam('album_id');
			$title = $request->getParam('title');
			$price = $request->getParam('price');
			$duration = $request->getParam('duration');
			$description = $request->getParam('description');
			$genres = $request->getParam('genres');
			$artist_ids = $request->getParam('ids');


			$data = array(
				'album_id' => $album_id,
				'title' => $title,
				'price' => $price,
				'duration' => $duration,
				'description' => $description
			);

			$song_model = new Application_Model_Song();
			$id = $song_model->insert($data);

			$genre_array = split("\n", $genres);
			foreach( $genre_array as $genre) {
				$db = Zend_Registry::get('db');
				$sql = "INSERT INTO song_genre (song_id, genre) VALUES ('$id', '$genre');";
				$db->query($sql);
			}


			$artist_array = split("\n", $artist_ids);
			foreach( $artist_array as $artist) {
				$db = Zend_Registry::get('db');
				$sql = "INSERT INTO song_artist (song_id, artist_id, role) VALUES ('$id', '$artist', 'Singer');";
				$db->query($sql);
			}

			$_SESSION['success'] = true;
			$_SESSION['message'] = "Song added to DB successfully.";

		}
	}

	public function newartistAction() {

		$request = $this->getRequest();

		if($request->isPost()) {
			$name = $request->getParam('name');
			$family = $request->getParam('family');
			$description = $request->getParam('description');
			$genres = $request->getParam('genres');

			$data = array(
				'name' => $name,
				'family' => $family,
				'description' => $description
			);

			$artist_model = new Application_Model_Artist();
			$id = $artist_model->insert($data);

			$genre_array = split("\n", $genres);
			foreach( $genre_array as $genre) {
				$db = Zend_Registry::get('db');
				$sql = "INSERT INTO artist_genre (artist_id, genre) VALUES ('$id', '$genre');";
				$db->query($sql);
			}
			$_SESSION['success'] = true;
			$_SESSION['message'] = "Artist and genres add to DB successfully.";

		}


	}

	public function newuserAction() {

		$request = $this->getRequest();

		if($request->isPost()) {
			$name = $request->getParam('name');
			$family = $request->getParam('family');
			$username = $request->getParam('username');
			$password = $request->getParam('password');
			$email = $request->getParam('email');

			$db = Zend_Registry::get('db');
			$sql = "INSERT INTO user (name, family, username, password, email) VALUES ('$name', '$family', '$username', '$password', '$email');";
			$db->query($sql);

			$_SESSION['success'] = true;
			$_SESSION['message'] = "User was added to DB successfully.";
		}

	}

	public function addplaylistAction() {

		$request = $this->getRequest();
		$user_id = $request->getParam('user_id');

		if(empty($user_id))
			throw new Exception("empty user id");
		if($request->isPost()) {

			$name = $request->getParam('name');

			$db = Zend_Registry::get('db');
			$sql = "INSERT INTO playlist (user_id, name) VALUES ($user_id, '$name');";
			$db->query($sql);


			$song_ids = $request->getParam('ids');
			$song_array = split("\n", $song_ids);
			foreach( $song_array as $song_id) {
				$sql = "INSERT INTO playlist_song (song_id, user_id, name) VALUES ('$song_id', '$user_id','$name');";
				$db->query($sql);
			}
			$_SESSION['success'] = true;
			$_SESSION['message'] = "New Playlist was added to DB successfully.";

		}

		$this->view->user_id = $user_id;
	}

	public function addreviewAction() {

		$request = $this->getRequest();
		$user_id = $request->getParam('user_id');

		if(empty($user_id))
			throw new Exception("empty user id");
		if($request->isPost()) {

			$song_id = $request->getParam('song_id');
			$text = $request->getParam('text');
			$star = $request->getParam('star');

			$db = Zend_Registry::get('db');
			$sql = "INSERT INTO review (user_id, song_id, review_text, star) VALUES ($user_id, $song_id, '$text', $star);";
			$db->query($sql);

			$_SESSION['success'] = true;
			$_SESSION['message'] = "New Review was added to DB successfully.";

		}

		$this->view->user_id = $user_id;
	}

	public function orderAction() {

		$request = $this->getRequest();
		$user_id = $request->getParam('user_id');

		if(empty($user_id))
			throw new Exception("empty user id");
		if($request->isPost()) {

			$payment_id = uniqid();

			$data = array(
				'payment_number' => $payment_id,
				'user_id' => $user_id
			);
			$trans_model = new Application_Model_Transaction();
			$trans_model->insert($data);



			$song_ids = $request->getParam('ids');
			$song_array = split("\n", $song_ids);
			$db = Zend_Registry::get("db");

			foreach( $song_array as $song_id) {
				$sql = "INSERT INTO `order` (payment_number, user_id, song_id) VALUES ('$payment_id', '$user_id','$song_id');";
				$db->query($sql);
			}
			$_SESSION['success'] = true;
			$_SESSION['message'] = "New Playlist was added to DB successfully.";

		}

		$this->view->user_id = $user_id;


	}

	public function queryAction() {

		$request = $this->getRequest();
		$query = $request->getParam('query');

		if (!$link = mysql_connect('localhost', 'root', 'p')) {
			echo 'Could not connect to mysql';
			exit;
		}

		if (!mysql_select_db('itunes', $link)) {
			echo 'Could not select database';
			exit;
		}

		$result = mysql_query($query);

		if (!$result) {
			$message  = 'Invalid query: ' . mysql_error() . "\n";
			$message .= 'Whole query: ' . $query;
			die($message);
		}

		while ($row = mysql_fetch_assoc($result)) {
			$this->vdump($row);
			echo "<hr>";
		}
	}

	private function vdump()
	{

		$args = func_get_args();

		$backtrace = debug_backtrace();
		$code = file($backtrace[0]['file']);

		echo "<pre style='background: #eee; border: 1px solid #aaa; clear: both; overflow: auto; padding: 10px; text-align: left; margin-bottom: 5px'>";

		echo "<b>" . htmlspecialchars(trim($code[$backtrace[0]['line'] - 1])) . "</b>\n";

		echo "\n";

		ob_start();

		foreach ($args as $arg)
			var_dump($arg);

		$str = ob_get_contents();

		ob_end_clean();

		$str = preg_replace('/=>(\s+)/', ' => ', $str);
		$str = preg_replace('/ => NULL/', ' &rarr; <b style="color: #000">NULL</b>', $str);
		$str = preg_replace('/}\n(\s+)\[/', "}\n\n" . '$1[', $str);
		$str = preg_replace('/ (float|int)\((\-?[\d\.]+)\)/', " <span style='color: #888'>$1</span> <b style='color: brown'>$2</b>", $str);

		$str = preg_replace('/array\((\d+)\) {\s+}\n/', "<span style='color: #888'>array&bull;$1</span> <b style='color: brown'>[]</b>", $str);
		$str = preg_replace('/ string\((\d+)\) \"(.*)\"/', " <span style='color: #888'>str&bull;$1</span> <b style='color: brown'>'$2'</b>", $str);
		$str = preg_replace('/\[\"(.+)\"\] => /', "<span style='color: purple'>'$1'</span> &rarr; ", $str);
		$str = preg_replace('/object\((\S+)\)#(\d+) \((\d+)\) {/', "<span style='color: #888'>obj&bull;$2</span> <b style='color: #0C9136'>$1[$3]</b> {", $str);
		$str = str_replace("bool(false)", "<span style='color:#888'>bool&bull;</span><span style='color: red'>false</span>", $str);
		$str = str_replace("bool(true)", "<span style='color:#888'>bool&bull;</span><span style='color: green'>true</span>", $str);

		echo $str;

		echo "</pre>";

		echo "<div class='block tiny_text' style='margin-left: 10px'>";

		echo "Sizes: ";
		foreach ($args as $k => $arg) {

			if ($k > 0) echo ",";
			echo count($arg);

		}

		echo "</div>";
	}

}

