<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\InformationForm;
use DB;
use Brian2694\Toastr\Facades\Toastr;

class FormController extends Controller
{
    // index
    public function index()
    {
        return view('form.information_form');
    }
    // save data
    public function saveRecord( Request $request)
    {
        $request->validate([
            'fullname'  => 'required|string|max:255',
            'sex'       => 'required',
            'image'     => 'required|image',
            'email'     => 'required|string|email|max:255',
            'phone'     => 'required|min:11|numeric',
            'country'   => 'required|string|max:255',
        ]);

        $image = time().'.'.$request->image->extension();  
        $request->image->move(public_path('images'), $image);

        $form = new InformationForm;
        $form->fullname  = $request->fullname;
        $form->sex       = $request->sex;
        $form->image     = $image;
        $form->email     = $request->email;
        $form->phone     = $request->phone;
        $form->country   = $request->country;
        $form->save();
        Toastr::success('Insert data successfully :)','Success');
        return redirect()->route('form/information/new');
    }
    // show data table
    public function show()
    {
        $data = DB::table('information_forms')->get();
        return view('form.view_detail',compact('data'));
    }

    // view edit
    public function viewEdit($id)
    {
        $data = DB::table('information_forms')->where('id',$id)->get();
        return view('form.view_edit',compact('data'));
    }
    // update 
    public function viewUpdate( Request $request)
    {

        $id        = $request->id;
        $fullname  = $request->fullname;
        $sex       = $request->sex;
        $email     = $request->email;
        $phone     = $request->phone;
        $country   = $request->country;
 
        $old_image = InformationForm::find($id);
        $image_name = $request->hidden_image;
        $image = $request->file('image');
 
        if($image != '')
        {
            $image_name = rand() . '.' . $image->getClientOriginalExtension();
            $image->move(public_path('images'), $image_name);
            unlink('images/'.$old_image->image);
        }
    
        $update = [
 
            'id'       => $id,
            'fullname' => $fullname,
            'image'    => $image_name,
            'sex'      => $sex,
            'phone'    => $phone,
            'country'  => $country,
        ];
        InformationForm::where('id',$request->id)->update($update);
        Toastr::success('Data updated successfully :)','Success');
        return redirect()->route('form/information/show');
    }
    // delete
    public function delete($id)
    {
        $delete = InformationForm::find($id);
        unlink('images/'.$delete->image);
        $delete->delete();
        Toastr::success('Data deleted successfully :)','Success');
        return redirect()->route('form/information/show');
    }
}
