<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use DB;
use Auth;
use App\Models\User;
use Hash;
use Brian2694\Toastr\Facades\Toastr;

class UserManagementController extends Controller
{
    //
    public function index()
    {
        $user = DB::table('users')->get();
        return view('usermamgement.usercontroller',compact('user'));
    }
    // view detail 
    public function viewDetail($id)
    {  
        if (Auth::user()->role_name=='Admin')
        {
            $data = DB::table('users')->where('id',$id)->get();
            $roleName = DB::table('role_type_users')->get();
            $userStatus = DB::table('user_types')->get();
            return view('usermamgement.view_users',compact('data','roleName','userStatus'));
        }
        else
        {
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

       $image = time().'.'.$request->image->extension();  
       $request->image->move(public_path('images'), $image);

       $user = new User;
       $user->name         = $request->name;
       $user->avatar       = $image;
       $user->email        = $request->email;
       $user->phone_number = $request->phone;
       $user->role_name    = $request->role_name;
       $user->password     = Hash::make($request->password);
       $user->save();
       Toastr::success('Create new account successfully :)','Success');
       return redirect()->route('user/management');
   }
   
   // update
   public function update(Request $request)
   {
       $id           = $request->id;
       $fullName     = $request->fullName;
       $email        = $request->email;
       $phone_number = $request->phone_number;
       $status       = $request->status;
       $role_name    = $request->role_name;

       $old_image = User::find($id);
       $image_name = $request->hidden_image;
       $image = $request->file('image');

       if($old_image->avatar=='photo_defaults.jpg')
       {
           if($image != '')
           {
               $image_name = rand() . '.' . $image->getClientOriginalExtension();
               $image->move(public_path('images'), $image_name);
           }
       }
       else{
           
           if($image != '')
           {
               $image_name = rand() . '.' . $image->getClientOriginalExtension();
               $image->move(public_path('images'), $image_name);
               unlink('images/'.$old_image->avatar);
           }
       }
       
       $update = [

           'id'           => $id,
           'name'         => $fullName,
           'avatar'       => $image_name,
           'email'        => $email,
           'phone_number' => $phone_number,
           'status'       => $status,
           'role_name'    => $role_name,
       ];
       User::where('id',$request->id)->update($update);
       Toastr::success('User updated successfully :)','Success');
       return redirect()->route('user/management');
   }
   // delete
   public function delete($id)
   {
       $delete = User::find($id);
       unlink('images/'.$delete->avatar);
       $delete->delete();
       Toastr::success('User deleted successfully :)','Success');
       return redirect()->route('user/management');
   }

   // view change password
   public function changePasswordView()
   {
       return view('usermanagement.change_password');
   }
   
   // change password in db
   public function changePasswordDB(Request $request)
   {
       $request->validate([
           'current_password' => ['required', new MatchOldPassword],
           'new_password' => ['required'],
           'new_confirm_password' => ['same:new_password'],
       ]);
  
       User::find(auth()->user()->id)->update(['password'=> Hash::make($request->new_password)]);
       Toastr::success('User change successfully :)','Success');
       return redirect()->route('home');
   }
}
