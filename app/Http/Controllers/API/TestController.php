<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Test;
use App\Models\MedicalDetail;
use Illuminate\Http\Request, Response;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;


class TestController extends Controller
{


    //bp test start
    public function bp(Request $request)
    {
        DB::beginTransaction();
        try {

            // $validator = Validator::make($request->all(), [
            //     'pre_lower_bp' => 'required',
            //     'post_lower_bp' => 'required',
            //     'medical_details_id' => 'required',
            //     'test_type_id' => 'required',
            //     'pre_upper_bp' => 'required',
            //     'post_upper_bp' => 'required',
            // ]);

            // if ($validator->fails()) {
            //     return response()->json([
            //         "status" => "error",
            //         "message" => $validator->errors()
            //     ], 401);
            // }

            $latestTestData = Test::where('features', 'bp')
                ->where('medical_details_id', $request->medical_details_id)
                ->where('test_status', '1')
                ->latest()->first();

            if ($latestTestData) {
                return response()->json([
                    'status' => 'forbidden',
                    'code' => 403,
                    'message' => 'You are not allowed for test again',
                ], 403);
            }

            if (isset($request->pre_lower_bp) && isset($request->pre_upper_bp) && isset($request->pre_upper_bp) && isset($request->post_upper_bp)) {
                $test_status = '1';
            } else {
                $test_status = '0';
            }


            $test = Test::updateOrCreate(
                ['features' => 'bp', 'medical_details_id' => $request->medical_details_id, 'test_type_id' => $request->test_type_id,],

                [

                    'data' => json_encode(['pre_lower_bp' => $request->pre_lower_bp, 'pre_upper_bp' => $request->pre_upper_bp, 'post_lower_bp' => $request->post_lower_bp, 'post_upper_bp' => $request->post_upper_bp]),
                    'test_status' => $test_status
                ]
            );


            $current_timestamp = Carbon::now()->timestamp;

            $certification_no = 'CN' . $current_timestamp . rand(100, 100000);




            $updateMedicalStatusOfConsumer = MedicalDetail::where('id', $request->medical_details_id)->where('consumer_status', '0')->where('certification_number', null)->update([
                'consumer_status' => '1',
                'certification_number' => $certification_no,
            ]);

            $consumerStatus = MedicalDetail::find($request->medical_details_id);

            $updatedConsumerStatus = $consumerStatus->consumer_status;


            DB::commit();



            if ($test) {
                return response()->json([
                    "status" => "success",
                    "message" => "Blood Pressure test added successfully!",
                    "consumer_status" => "1",
                    "verified" => "You gave test and your profile  is varifyed",
                    "consumer_updated_status" => $updatedConsumerStatus,
                    "consumer_test_report" => $test

                ], 200);
            } else {
                return response()->json([
                    "status" => "error",
                    "message" => "Something went wrong!"
                ], 500);
            }

        } catch (\Exception $e) {
            DB::rollBack();
            return Response::json(
                array(
                    'status' => 'error',
                    'code' => 404,
                    'message' => $e->getmessage()
                ),
                404
            );


        }


    }
    //bp test start end

    //romberg test start
    public function rombergTest(Request $request)
    {
        DB::beginTransaction();

        try {



            $validator = Validator::make($request->all(), [
                'medical_details_id' => 'required',
                'test_type_id' => 'required',
                'both_legs' => 'required|mimetypes:video/mp4',
                'left_leg' => 'required|mimetypes:video/mp4',
                'right_leg' => 'required|mimetypes:video/mp4',
            ]);


            if ($validator->fails()) {
                return response()->json([
                    "status" => "error",
                    "message" => $validator->errors()
                ], 403);
            }

            $latestTestData = Test::where('features', 'rt')
                ->where('medical_details_id', $request->medical_details_id)
                ->where('test_status', '1')
                ->latest()->first();

            if ($latestTestData) {
                return response()->json([
                    'status' => 'forbidden',
                    'code' => 403,
                    'message' => 'You are not allowed for test again',
                ], 403);
            }


            $videoBothLegs = $request->file('both_legs');

            $current_timestamp = Carbon::now()->timestamp;
            $videoBothLegsName = 'both_legs' . $current_timestamp . '.' . $videoBothLegs->getClientOriginalName();


            $videoBothLegs->move(public_path('/videos'), $videoBothLegsName);


            $videoLeftLegs = $request->file('left_leg');

            $current_timestamp = Carbon::now()->timestamp;
            $videoLeftLegsName = 'left_leg' . $current_timestamp . '.' . $videoLeftLegs->getClientOriginalName();


            $videoLeftLegs->move(public_path('/videos'), $videoLeftLegsName);


            $videoRightLegs = $request->file('right_leg');

            $current_timestamp = Carbon::now()->timestamp;
            $videoRightLegsName = 'right_leg' . $current_timestamp . '.' . $videoRightLegs->getClientOriginalName();


            $videoRightLegs->move(public_path('/videos'), $videoRightLegsName);



            $test = Test::updateOrCreate([
                'medical_details_id' => $request->medical_details_id,
                'test_type_id' => $request->test_type_id,
                'features' => 'rt'
            ], [
                'data' => json_encode(['both_legs' => $videoBothLegsName, 'left_leg' => $videoLeftLegsName, 'right_leg' => $videoRightLegsName]),
                'test_status' => '1'
            ]);






            $current_timestamp = Carbon::now()->timestamp;

            $certification_no = 'CN' . $current_timestamp . rand(100, 100000);




            $updateMedicalStatusOfConsumer = MedicalDetail::where('id', $request->medical_details_id)->where('consumer_status', '0')->where('certification_number', null)->update([
                'consumer_status' => '1',
                'certification_number' => $certification_no,
            ]);


            DB::commit();

            return response()->json([
                "status" => "success",
                "message" => "all videos add sucessfully!",
                "consumer_status" => "1",
                "verified" => "You gave test and your profile  is varifyed"

            ], 200);


        } catch (\Exception $e) {

            DB::rollBack();
            return Response::json(
                array(
                    'status' => 'error',
                    'code' => 404,
                    'message' => $e->getmessage()
                ),
                404
            );

        }








    }
    //romberg test end

    //Check Asscess of test start
    public function checkaccesoftest(Request $request)
    {
        $test = Test::where('medical_details_id', $request->medical_details_id)->where('test_type_id', $request->test_type_id)->where('features', $request->features)->pluck('test_status');
        if (!isset($test[0])) {
            return '0';

        } elseif ($test[0] == '0') {
            return '0';
        } elseif ($test[0] == '1') {
            return '1';
        }
    }
    //Check Asscess of test end


    //eyecheckup test start
    public function eyecheckup(Request $request)
    {
        DB::beginTransaction();
        try {
            $validator = Validator::make($request->all(), [
                'medical_details_id' => 'required',
                'q1' => 'required',
                'q2' => 'required',
                'q3' => 'required',
                'q4' => 'required',
                'q5' => 'required',
                'q6' => 'required',
            ]);


            if ($validator->fails()) {
                return response()->json([
                    "status" => "error",
                    "message" => $validator->errors()
                ], 403);
            }

            $latestTestData = Test::where('features', 'eyecheckup')
                ->where('medical_details_id', $request->medical_details_id)
                ->where('test_status', '1')
                ->latest()->first();

            if ($latestTestData) {
                return response()->json([
                    'status' => 'forbidden',
                    'code' => 403,
                    'message' => 'You are not allowed for test again',
                ], 403);
            }

            $test = Test::updateOrCreate(
                ['features' => 'eyecheckup', 'medical_details_id' => $request->medical_details_id, 'test_type_id' => $request->test_type_id,],

                [

                    'data' => json_encode(['q1' => $request->q1, 'q2' => $request->q2, 'q3' => $request->q3, 'q4' => $request->q4, 'q5' => $request->q5, 'q6' => $request->q6]),
                    'test_status' => '1'
                ]
            );


            $current_timestamp = Carbon::now()->timestamp;

            $certification_no = 'CN' . $current_timestamp . rand(100, 100000);




            $updateMedicalStatusOfConsumer = MedicalDetail::where('id', $request->medical_details_id)->where('consumer_status', '0')->where('certification_number', null)->update([
                'consumer_status' => '1',
                'certification_number' => $certification_no,
            ]);







            DB::commit();



            if ($test) {
                return response()->json([
                    "status" => "success",
                    "message" => "Eye Checkup test added successfully!",
                    "consumer_status" => "1",
                    "verified" => "You gave test and your profile  is verified",
                    "consumer_test_report" => $test

                ], 200);
            } else {
                return response()->json([
                    "status" => "error",
                    "message" => "Something went wrong!"
                ], 500);
            }

        } catch (\Exception $e) {

            DB::rollBack();
            return Response::json(
                array(
                    'status' => 'error',
                    'code' => 404,
                    'message' => $e->getmessage()
                ),
                404
            );

        }


    }
    //eyecheckup test end



    // flatfoot test start
    public function flatfoot(Request $request)
    {

        DB::beginTransaction();
        try {
            $validator = Validator::make($request->all(), [
                'medical_details_id' => 'required',
                'flatfoot' => 'required',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    "status" => "error",
                    "message" => $validator->errors()
                ], 403);
            }


            $latestTestData = Test::where('features', 'flatfoot')
                ->where('medical_details_id', $request->medical_details_id)
                ->where('test_status', '1')
                ->latest()->first();

            if ($latestTestData) {
                return response()->json([
                    'status' => 'forbidden',
                    'code' => 403,
                    'message' => 'You are not allowed for test again',
                ], 403);
            }



            $imageflatfoot = $request->file('flatfoot');

            $current_timestamp = Carbon::now()->timestamp;
            $imageflatfootName = 'both_legs' . $current_timestamp . '.' . $imageflatfoot->getClientOriginalName();

            $imageflatfoot->move(public_path('/test_images'), $imageflatfootName);



            $test = Test::updateOrCreate(
                ['features' => 'flatfoot', 'medical_details_id' => $request->medical_details_id, 'test_type_id' => $request->test_type_id,],

                [

                    'data' => json_encode(['flatfoot' => $imageflatfootName]),
                    'test_status' => '1'
                ]
            );

            $current_timestamp = Carbon::now()->timestamp;

            $certification_no = 'CN' . $current_timestamp . rand(100, 100000);




            $updateMedicalStatusOfConsumer = MedicalDetail::where('id', $request->medical_details_id)->where('consumer_status', '0')->where('certification_number', null)->update([
                'consumer_status' => '1',
                'certification_number' => $certification_no,
            ]);






            DB::commit();



            if ($test) {
                return response()->json([
                    "status" => "success",
                    "message" => "FlatFoot test added successfully!",
                    "consumer_status" => "1",
                    "verified" => "You gave test and your profile  is verified",
                    "consumer_test_report" => $test

                ], 200);
            } else {
                return response()->json([
                    "status" => "error",
                    "message" => "Something went wrong!"
                ], 500);
            }

        } catch (\Exception $e) {

            DB::rollBack();
            return Response::json(
                array(
                    'status' => 'error',
                    'code' => 404,
                    'message' => $e->getmessage()
                ),
                404
            );

        }




    }

    // flatfoot test end


    //bppv test start
    public function bppv(Request $request)
    {

        DB::beginTransaction();
        try {
            $validator = Validator::make($request->all(), [
                'medical_details_id' => 'required',
                'bppv_video' => 'required',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    "status" => "error",
                    "message" => $validator->errors()
                ], 403);
            }


            $latestTestData = Test::where('features', 'bppv')
                ->where('medical_details_id', $request->medical_details_id)
                ->where('test_status', '1')
                ->latest()->first();

            if ($latestTestData) {
                return response()->json([
                    'status' => 'forbidden',
                    'code' => 403,
                    'message' => 'You are not allowed for test again',
                ], 403);
            }



            $videobppv = $request->file('bppv_video');

            $current_timestamp = Carbon::now()->timestamp;
            $videobppvName = 'bppv' . $current_timestamp . '.' . $videobppv->getClientOriginalName();

            $videobppv->move(public_path('/videos'), $videobppvName);



            $test = Test::updateOrCreate(
                ['features' => 'bppv', 'medical_details_id' => $request->medical_details_id, 'test_type_id' => $request->test_type_id,],

                [

                    'data' => json_encode(['bppv_video' => $videobppvName]),
                    'test_status' => '1'
                ]
            );

            $current_timestamp = Carbon::now()->timestamp;

            $certification_no = 'CN' . $current_timestamp . rand(100, 100000);




            $updateMedicalStatusOfConsumer = MedicalDetail::where('id', $request->medical_details_id)->where('consumer_status', '0')->where('certification_number', null)->update([
                'consumer_status' => '1',
                'certification_number' => $certification_no,
            ]);


            DB::commit();



            if ($test) {
                return response()->json([
                    "status" => "success",
                    "message" => "FlatFoot test added successfully!",
                    "consumer_status" => "1",
                    "verified" => "You gave test and your profile  is verified",
                    "consumer_test_report" => $test

                ], 200);
            } else {
                return response()->json([
                    "status" => "error",
                    "message" => "Something went wrong!"
                ], 500);
            }

        } catch (\Exception $e) {

            DB::rollBack();
            return Response::json(
                array(
                    'status' => 'error',
                    'code' => 404,
                    'message' => $e->getmessage()
                ),
                404
            );

        }




    }
    //bppv test end





    // fukuda test start
    public function fukuda(Request $request)
    {

        DB::beginTransaction();
        try {
            $validator = Validator::make($request->all(), [
                'medical_details_id' => 'required',
                'fukuda_video' => 'required',
                'test_type_id' => 'required',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    "status" => "error",
                    "message" => $validator->errors()
                ], 403);
            }


            $latestTestData = Test::where('features', 'fukuda')
                ->where('medical_details_id', $request->medical_details_id)
                ->where('test_status', '1')
                ->latest()->first();

            if ($latestTestData) {
                return response()->json([
                    'status' => 'forbidden',
                    'code' => 403,
                    'message' => 'You are not allowed for test again',
                ], 403);
            }



            $videofukuda = $request->file('fukuda_video');
            $current_timestamp = Carbon::now()->timestamp;
            $videofukudaName = 'fukuda' . $current_timestamp . '.' . $videofukuda->getClientOriginalName();

            $videofukuda->move(public_path('/videos'), $videofukudaName);



            $test = Test::updateOrCreate(
                ['features' => 'fukuda', 'medical_details_id' => $request->medical_details_id, 'test_type_id' => $request->test_type_id,],

                [

                    'data' => json_encode(['fukuda_video' => $videofukudaName]),
                    'test_status' => '1'
                ]
            );

            $current_timestamp = Carbon::now()->timestamp;

            $certification_no = 'CN' . $current_timestamp . rand(100, 100000);




            $updateMedicalStatusOfConsumer = MedicalDetail::where('id', $request->medical_details_id)->where('consumer_status', '0')->where('certification_number', null)->update([
                'consumer_status' => '1',
                'certification_number' => $certification_no,
            ]);


            DB::commit();



            if ($test) {
                return response()->json([
                    "status" => "success",
                    "message" => "Fukuda-Unterberger test added successfully!",
                    "consumer_status" => "1",
                    "verified" => "You gave test and your profile  is verified",
                    "consumer_test_report" => $test

                ], 200);
            } else {
                return response()->json([
                    "status" => "error",
                    "message" => "Something went wrong!"
                ], 500);
            }

        } catch (\Exception $e) {

            DB::rollBack();
            return Response::json(
                array(
                    'status' => 'error',
                    'code' => 404,
                    'message' => $e->getmessage()
                ),
                404
            );

        }




    }
    // fukuda test end





    // videonystagmography test start
    public function videonystagmography(Request $request)
    {

        DB::beginTransaction();
        try {
            $validator = Validator::make($request->all(), [
                'medical_details_id' => 'required',
                'videonystagmography_video' => 'required',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    "status" => "error",
                    "message" => $validator->errors()
                ], 403);
            }


            $latestTestData = Test::where('features', 'videonystagmography')
                ->where('medical_details_id', $request->medical_details_id)
                ->where('test_status', '1')
                ->latest()->first();

            if ($latestTestData) {
                return response()->json([
                    'status' => 'forbidden',
                    'code' => 403,
                    'message' => 'You are not allowed for test again',
                ], 403);
            }



            $videofukuda = $request->file('videonystagmography_video');
            $current_timestamp = Carbon::now()->timestamp;
            $videofukudaName = 'videonystagmography' . $current_timestamp . '.' . $videofukuda->getClientOriginalName();

            $videofukuda->move(public_path('/video'), $videofukudaName);



            $test = Test::updateOrCreate(
                ['features' => 'videonystagmography', 'medical_details_id' => $request->medical_details_id, 'test_type_id' => $request->test_type_id,],

                [

                    'data' => json_encode(['videonystagmography_video' => $request->$videofukudaName]),
                    'test_status' => '1'
                ]
            );

            $current_timestamp = Carbon::now()->timestamp;

            $certification_no = 'CN' . $current_timestamp . rand(100, 100000);




            $updateMedicalStatusOfConsumer = MedicalDetail::where('id', $request->medical_details_id)->where('consumer_status', '0')->where('certification_number', null)->update([
                'consumer_status' => '1',
                'certification_number' => $certification_no,
            ]);


            DB::commit();



            if ($test) {
                return response()->json([
                    "status" => "success",
                    "message" => "Videonystagmography test added successfully!",
                    "consumer_status" => "1",
                    "verified" => "You gave test and your profile  is verified",
                    "consumer_test_report" => $test

                ], 200);
            } else {
                return response()->json([
                    "status" => "error",
                    "message" => "Something went wrong!"
                ], 500);
            }

        } catch (\Exception $e) {

            DB::rollBack();
            return Response::json(
                array(
                    'status' => 'error',
                    'code' => 404,
                    'message' => $e->getmessage()
                ),
                404
            );

        }




    }
    // videonystagmography test end






}
