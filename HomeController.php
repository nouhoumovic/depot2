<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class HomeController extends Controller
{
    public function index()
    {
        $user = DB::table('users')->get();
        return view('usermamgement.usercontroller', compact('user'));
    }
    // view detail 
    public function viewDetail($id)
    {
        if (Auth::user()->role_name == 'Admin') {
            $data = DB::table('users')->where('id', $id)->get();
            $roleName = DB::table('role_type_users')->get();
            $userStatus = DB::table('user_types')->get();
            return view('usermamgement.view_users', compact('data', 'roleName', 'userStatus'));
        } else {
            return redirect()->route('home');
        }
    }
    //
    public function addNew()
    {
        return view('usermamgement.add_new_user');
    }
    // save new user
    public function addNewUserSave(Request $request)
    {
        $request->validate([
            'name'      => 'required|string|max:255',
            'image'     => 'required|image',
            'email'     => 'required|string|email|max:255|unique:users',
            'phone'     => 'required|min:11|numeric',
            'role_name' => 'required|string|max:255',
            'password'  => 'required|string|min:8|confirmed',
            'password_confirmation' => 'required',
        ]);


}
