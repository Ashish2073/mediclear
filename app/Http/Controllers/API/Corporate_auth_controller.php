<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\CorporateID;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Facades\Auth;

class Corporate_auth_controller extends Controller
{


    public function UserStatus($user_id){

        $customer= CorporateID::findOrFail($user_id);
        $status=$customer ->status ;
        if ($status == 'Deactive' ) {
            return false;
            }else{
            return true ;
                }
    }





    public function login(Request $request)
    {
        $validator = Validator::make($request->all(),[
            'user_id' => 'required',
            'password' => 'required',
        ]);



        if($validator->fails()){
            return response()->json(['error'=>$validator->errors()],422);
        }


// // if(!$status){
// //     return response()->json([
// //         "message" => "Your account is  Deactive!"
// //         ], 401);
// // }


//         $admin = CorporateID::where('user_id', $request->user_id)->first();

        $credentials = $request->only('user_id', 'password');
        $adminauthentication = Auth::guard('corporate-api')->attempt($credentials);

        $admin = Auth::guard('corporate-api')->user();




        // if (isset($admin)) {
        //     $status = $admin->status;
        // } else {
        //     $status = "null";
        // }



        if (!isset($admin) || !Hash::check($request->password, $admin->password)) {
            return response()->json(['Unauthorized'=>['error'=>'The provided credentials are incorrect.']],401);
        }


        $status=$admin->status ;
        if ($status == 'Deactive' ) {
            return response()->json([
                        "message" => "Your account is  Deactive!"
                        ], 401);;
            }

        return response()->json([
            'data' => $admin,
             'type' => 'corporate',
            'token' => $admin->createToken('mobile', ['role:corporate'])->plainTextToken
        ]);
    }

    //









}
