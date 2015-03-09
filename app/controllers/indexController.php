<?php

class IndexController extends BaseController{

/*
* ----------------------------------
*  MICROSITIO
* ----------------------------------
*/

	public function micrositio(){
		return View::make('micrositio');
	}

/*
* ----------------------------------
*  SINGLE USER
* ----------------------------------
*/
	public function user($fbid){
		$user = User::whereFbid($fbid)->with('fieldaction')->get();
		if($user->isEmpty()){
			$error = array(
				'error' => 1
			);
			return Response::json($error);
		}else{
			return Response::json($user[0]);
		}
	}
	
	public function register(){
		$user = User::whereFbid(Input::get('fbid'))->get();

		if($user->isEmpty()){
			$user = new User;
			$user->fbid = Input::get('fbid');
			$user->twid = Input::get('twid');
			$user->name = Input::get('name');
			$user->email = Input::get('email');
			$user->fieldaction_id = Input::get('fieldaction_id');
			$user->gender = Input::get('gender');
			$user->age = Input::get('age');
			$user->save();

			$user = User::whereFbid(Input::get('fbid'))->with('fieldaction')->get();
			return Response::json($user);
		}else{
			$error = array(
				'error' => 1
			);
			return Response::json($error);
		}
	}

	public function addPoints($fbid){
		$user = User::where('fbid', '=', $fbid)->get();
		DB::table('users')
            ->whereFbid($fbid)
            ->increment('points', Input::get('points'));

		$user = User::whereFbid($fbid)->with('fieldaction')->get();
		return Response::json($user);
	}

	public function getSelfie($idSelfie){
		$selfie = Selfie::where('id', '=', $idSelfie)->with('engagement')->with('user')->get();

		if($selfie->isEmpty()){
			$error = array(
				'error' => 1
			);
			return Response::json($error);
		}else{
			return Response::json($selfie[0]);
		}
	}

	public function getSelfies($fbid){
		$selfies = Selfie::where('user_id', '=', $fbid)->OrderBy('id', 'desc')->with('engagement')->get();

		if($selfies->isEmpty()){
			$error = array(
				'error' => 1
			);
			return Response::json($error);
		}else{
			return Response::json($selfies);
		}
	}

	public function uploadSelfie($fbid){
		$up = Input::hasFile('picture');
		$status = array();
		if($up){

			//coordenada x
			$x = Input::get('x');

			//coordenada y
			$y = Input::get('y');

			//width
			$width = Input::get('width');

			//height
			$height = Input::get('height');

			//guardamos la imágen en una variabñe
			$image = Input::file('picture');

			//obtenemos el md5
			$md5 = md5_file($image);

			//traemos la extensión
			$ext = $image->getClientOriginalExtension();

			//generamos el nombre de la imagen
			$filename = $md5.'.'.$ext;

			//se mueve la imágen a la carpeta pictures
			$image->move('pictures', $filename);

			//obtenemos la url de la imágen
			$fileUrl = URL::asset('pictures/'.$filename);
			$fileUrlThumb = URL::asset('pictures/m/'.$filename);

			//obtenemos root de imágen
			$file = public_path('pictures/'.$filename);

			//asignamos las carpetas a variables
			$path = public_path('pictures/'.$filename);
			$pathThumb = public_path('pictures/thumb/'.$filename);

			//creamos la imágen
			$img = Image::make($file)->crop($width, $height, $x, $y);

			//id de compromiso
			$engagement = Input::get('engagement_id');

			//redimensiones a todos tamaños de carpetas y 
			Image::make($img)->resize(512, 512)->insert('img/'.$engagement.'.png')->save($path);
			Image::make($img)->resize(64, 64)->save($pathThumb);

			//insertamos la imagen en la bd
			$selfie = new Selfie;
			$selfie->user_id = $fbid;
			$selfie->engagement_id = $engagement;
			$selfie->picture = $filename;
			$selfie->description = Input::get('description');
			$selfie->save();

			//obtenemos el id
			$id = $selfie->id;

			$selfie = Selfie::find($id);
			return Response::json($selfie);
		}else{
			//si la imagen no sube guardamos el error
			$status = array(
				'status' => 'error',
				'time'=> array(
				'time' => time()
			),
				'error' => 'error',
				'pic' => 'error'
			);
			return Response::json($status);
		}
	}

/*
* ----------------------------------
*  USERS
* ----------------------------------
*/

	public function users(){
		$users = User::with('fieldaction')->get();

		return Response::json($users);
	}

	public function leaderBoard($fieldaction){
		$users = User::where('fieldaction_id', '=', $fieldaction)->with('fieldaction')->take(30)->OrderBy('points', 'desc')->get();

		return Response::json($users);
	}

	public function getAllSelfies(){
		$selfies = Selfie::OrderBy('id', 'desc')->take(30)->get();

		return Response::json($selfies);
	}

	public function getLeaders(){
		$users = User::OrderBy('points', 'desc')->take(10)->get();

		return Response::json($users);
	}
}

?>