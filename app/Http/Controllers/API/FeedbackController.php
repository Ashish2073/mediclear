<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Complaint;
use App\Models\Feedback;
use Illuminate\Http\Request;

class FeedbackController extends Controller
{
    //

    public function feedbackDetail(Request $request){
        $user_id=$request->user()->user_id;

        // dd($user_id);
        if($request->has('type')){
          if($request->type==2){
            $feedback=Complaint::create([
                'user_id'=>$user_id,
                'subject'=>$request->subject,
                'message'=>$request->message,
        ]);

if($feedback){
    return response()->json(['message'=>'Your Complaint has been submitted successfully','status'=>true],200);
}else{
    return response()->json(['message'=>'failed','status'=>false],200);
}
    }



    if($request->type==1){
        $feedback=Feedback::create([
            'user_id'=>$user_id,
            'subject'=>$request->subject,
            'message'=>$request->message,
    ]);


if($feedback){
return response()->json(['message'=>'Your feedback has been submitted successfully','status'=>true],200);
}else{
return response()->json(['message'=>'failed','status'=>false],200);
}
}


}


    }


}
