<?php

class IndexController extends Controller{

/*
* ----------------------------------
*  SINGLE USER
* ----------------------------------
*/
	public function user($fbid){
		$user = User::whereFbid($fbid)->with('engagement')->with('fieldaction')->get();
		return Response::json($user[0]);
	}
	
	public function register(){
		$user = new User;
		$user->fbid = Input::get('fbid');
		$user->twid = Input::get('twid');
		$user->name = Input::get('name');
		$user->email = Input::get('email');
		$user->fieldaction_id = Input::get('fieldaction_id');
		$user->gender = Input::get('gender');
		$user->age = Input::get('age');
		$user->save();

		$user = User::whereFbid(Input::get('fbid'))->with('engagement')->with('fieldaction')->get();
		return Response::json($user);
	}

	public function addPoints($fbid){
		$user = User::where('fbid', '=', $fbid)->get();
		DB::table('users')
            ->whereFbid($fbid)
            ->increment('points', Input::get('points'));

		$user = User::whereFbid($fbid)->with('engagement')->with('fieldaction')->get();
		return Response::json($user);
	}

	public function getSelfie($fbid){

	}

	public function uploadSelfie($fbid){
		$up = Input::hasFile('picture');
		$status = array();
		if($up){
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
			//redimensiones, a todos tamaños de carpetascuidando el upsize

			$img = Image::make($file)->crop(512, 512, 25, 25)->save($path);
			Image::make($file)->resize(64, 64)->save($pathThumb);

			//insertamos la imagen en la bd
			$selfie = new Selfie;
			$selfie->user_id = $fbid;
			$selfie->engagement_id = Input::get('engagement_id');
			$selfie->picture = $filename;
			$selfie->desription = Input::get('description');
			$selfie->save();

			//obtenemos el id
			$id = $selfie->id;
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
		$users = User::with('engagement')->with('fieldaction')->get();

		return Response::json($users);
	}

	public function leaderBoard($fieldaction){
		$users = User::where('fieldaction_id', '=', $fieldaction)->with('fieldaction')->get();

		return Response::json($users);
	}

	public function getAllSelfies(){

	}
}

?>