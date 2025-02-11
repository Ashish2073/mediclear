<?php

namespace App\Http\Controllers;

use App\Models\CorporateID;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class CorporateController extends Controller
{

    public function showCorporateID(){


        $corporate=CorporateID::orderBy('created_at','desc')
        ->get();


        return view('dashboard.corporateId',compact('corporate'));
    }






    public function filterCorporateID(Request $req){
$start=$req->start;
$end=$req->end;
        $corporate=CorporateID::whereDate('created_at', '>=', $start)
        ->whereDate('created_at', '<=', $end)
        ->orderBy('created_at', 'desc')
        ->get();

        return view('dashboard.corporateId',compact('corporate','start','end'));
    }




    public function addCorporateID(Request $req){


        $this->validate($req, [
            'name' => 'required',
            'user_id' => 'required|unique:corporate_i_d_s',
            // 'address' => 'required',
            'mobile_no' => 'required|digits:10',
            'email' => 'required|email|unique:corporate_i_d_s',
            'password'=>'required',
            ]);





            $result=CorporateID::create([
                'name'=>$req->name,
                'user_id'=>$req->user_id,
                'mobile_no'=>$req->mobile_no,
                'email'=>$req->email,
                'password'=>bcrypt($req->password),
                'status'=>'Active'
            ]);


            if($result){
                return back()->with('message','Corporate ID Added Successfully');
            }else{
                return back()->with('message','Failed');


            }
    }




public function updateStatusCorporateID($type ,$id){

    if($type=="Pending"){

        CorporateID::where('id',$id)->update(['status'=>'Pending']);
    }else if($type=="Active"){

        CorporateID::where('id',$id)->update(['status'=>'Active']);
    }else if($type=="Deactive"){

        CorporateID::where('id',$id)->update(['status'=>'Deactive']);
    }

    return redirect()->back()->with('message','Updated Successfully');

}


public function deleteCorporateID(Request $req){

    $result=CorporateID::where('id',$req->id)->delete();

if($result){
    return redirect()->back()->with('message','Delete Successfully');
}else{
    return redirect()->back()->with('message',' Failed');

}

}






public function updateCorporateID(Request $req){

    $validate = Validator::make($req->all(), [
        'name' => 'required',
        // 'user_id' => 'required|unique:corporate_i_d_s',
        // 'address' => 'required',
        'mobile_no' => 'required',
        'email' => 'required|email',
        // 'password'=>'required',
        ]);




        if ($validate->fails()) {

            // return back()->withErrors($validate)->withInput();
            return back()->withErrors($validate);
        }



        $result=CorporateID::where('id',$req->id)->update([
            'name'=>$req->name,
            // 'user_id'=>$req->user_id,
            'mobile_no'=>$req->mobile_no,
            'email'=>$req->email,
            // 'password'=>$req->password,
            // 'status'=>'Pending'
        ]);



        if($result){
            return back()->with('message','Corporate ID Updated Successfully');
        }else{
            return back()->with('message','Failed');


        }
}




    //
}
