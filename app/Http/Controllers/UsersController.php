<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Users;
use Auth,Redirect,File,DB, Validator, Session, Hash;

class UsersController extends Controller
{
  //
  public function index(){
      if(!Session::get('login')){
          return redirect('admin.login', $data)->with('alert','Kamu harus login dulu');
      }
      else{
        $data['title'] = "Dashboard";
        return view('admin.dashboard.main',$data);
      }
  }

  public function login(){
      $data['title'] = "Login";
      return view('admin.login',$data);
  }

  public function loginPost(Request $request){

      $name = $request->name;
      $password = $request->password;
      $email = $request->email;

      $data = Users::where('email',$email)->first();
      if($data){ //apakah email tersebut ada atau tidak
          if(Hash::check($password,$data->password)){
              Session::put('name',$data->name);
              Session::put('login',TRUE);
              return redirect('home_user');
          }
          else{
              return redirect('login')->with('alert','Password atau Email, Salah !');
          }
      }
      else{
          return redirect('login')->with('alert','Password atau Email, Salah!');
      }
  }

  public function logout(){
      Session::flush();
      return redirect('login')->with('alert','Kamu sudah logout');
  }

  public function register(Request $request){
      $data['title'] = "Sign Up";
      return view('admin.register',$data);
  }

  public function registerPost(Request $request){
      $this->validate($request, [
          'name' => 'required|min:4',
          'password' => 'required',
          'email' => 'required|min:4|email|unique:users',
          // 'confirmation' => 'required|same:password',
      ]);

      $data =  new Users();
      $data->name = $request->name;
      $data->password = bcrypt($request->password);
      $data->email = $request->email;
      $data->save();
      return redirect('login')->with('alert-success','Kamu berhasil Register');
  }
}
