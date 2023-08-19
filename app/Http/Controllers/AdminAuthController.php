<?php

namespace App\Http\Controllers;


use App\Models\Guide;
use App\Models\Admin;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\Controller;
use App\Models\Guide_Rates;
use Illuminate\Support\Facades\DB;

class AdminAuthController extends Controller
{
    protected $db_mysql;
    public function __construct()
    {
        $this ->db_mysql= config('database.connections.mysql.database');

    }

    public function addguide (Request $request)
    {
        $guidee = new guide();

        $validator =Validator::make($request->all(),[
            'name'=>'required',
            'email'=>'required|string|email|unique:guides',
            'password'=>'required|min:8',
            'gender' => 'required' ,
            'age' => 'required',
            'yearsofExperience' => 'required',
            'location'=> 'required',
            'bio' => 'nullable'

        ]);
        if ($validator->fails())
        {
            return response()->json($validator->errors()->toJson(),400);
        }
        $imagePath = null;
        if ($request->image != null) {
            
            $image = $request->image;
            $imageName = time() . '.' . $image->getClientOriginalExtension();
            $imagePath = 'public/images/guides_images/' . $imageName;
            $image->move(public_path('images/guides_images'), $imageName);
            $guidee->image=$imagePath;

        }



        $guide=Guide::create(array_merge(
            $validator->validated(),
            ['password'=>bcrypt($request->password)],
            ['image'=>$imagePath]
        ));

        return response()->json([
            'type' => 'guide',
            'message'=>'Guide added successfully',

        ],201);
    }
    /**
     * Login
     */
    public function register(Request $request)
    {


            $validator =Validator::make($request->all(),[
                'name'=>'required',
                'email'=>'required|string|email|unique:admins',
                'password'=>'required|min:8',

            ]);

            if ($validator->fails())
            {
                return response()->json($validator->errors()->toJson(),400);
            }
            $admin=Admin::create(array_merge(
                $validator->validated(),
                ['password'=>bcrypt($request->password)]
            ));
            $credentials=$request->only(['email','password']);
            $token=Auth::guard('admin-api')->attempt($credentials);

            return response()->json([
                'type' => 'Admin',
                'message'=>'Register successfully',
                'access_token'=>$token
            ],201);
        }
        /**
         * Login
         */
        public function login(Request $request)
        {
         $validator =Validator::make($request->all(),[

             'email'=>'required|string|email',
             'password'=>'required|string|min:8',

         ]);
         if ($validator->fails())
         {
             return response()->json($validator->errors()->toJson(),422);
         }
         $credentials=$request->only(['email','password']);

         if(!$token=Auth::guard('admin-api')->attempt($credentials))
         {
           return response()->json(['error'=>'Unauthorized'],401);
         }
          Admin::where('email' , $request->email);
         return response()->json([
             'access_token'=>$token,
             'admin'=>Auth::guard('admin-api')->user(),

           ]);
        }
        /**
         * Get the authenticated User.
         *
         * @return \Illuminate\Http\JsonResponse
         */
       /* public function me()
        {
            return response()->json(auth()->user());
        }
        /**
         * Log the user out (Invalidate the token).
         *
         * @return \Illuminate\Http\JsonResponse
         */
       public function logout()
        {
            Auth::guard('admin-api')->logout();
            return response()->json(['message' => 'Successfully logged out']);
        }

        public function getProfile_of_guides()
        {
            $guides = Guide::select('id', 'name', 'gender', 'age', 'yearsofExperience', 'image', 'location' , 'bio' , 'created_at')->get();


            foreach ($guides as $guide) {

                $guide->rating = round(Guide_Rates::where('guide_id' , $guide->id)->avg('rate'),1);
                  
            }

            return response()->json($guides);
        }
        public function getProfile_of_users()
        {
            $users = User::select('id', 'name', 'email',  'image', 'number')->get();

            $response = [];

            foreach ($users as $user) {
                $image = is_null($user->image) ? 'null' : asset($user->image);

                $response[] = [
                    'id' => $user->id,
                    'name' => $user->name,
                    'email' => $user->email,
                    'image' => $image,
                   'number' => $user->number,
                ];
            }

            return response()->json($response);
        }
        public function delete_any_guide($id){

            $guide = Guide::find($id);
            $result = $guide->delete();
            if($result){
                return response()->json([
                    'message'=>' A guide Deleted Successfully'
                ],201);
             } else{
                return response()->json([
                    'message'=>'Guide Not Founded '
                ],400);
                }





        }
        public function delete_any_user($id){
            $user = User::find($id);
            $result = $user->delete();
            if($result){
                return response()->json([
                    'message'=>' A user Deleted Successfully'
                ],201);
             } else{
                return response()->json([
                    'message'=>'User Not Deleted '
                ],400);
                }
        }

        public function getuser(Request $request)
        {
            $user = User::select('id', 'name', 'email', 'number', 'image')->where('id', $request->user_id)->first();

            if ($user) {

                $userData = [
                    'id' => $user->id,
                    'name' => $user->name,
                    'email' => $user->email,
                    'number' => $user->number,
                    'image' => $user->image,
                ];

                return response()->json([
                    'message' => 'User you needed is',
                    'user' => $userData,
                ]);
            } else {
                return response()->json([
                    'message' => 'User not found',
                ], 404);
            }
        }

        public function getguide(Request $request)
        {
            $guide = Guide::select('id', 'name', 'email', 'image','gender',
            'age', 'yearsofExperience', 'location','bio'
             )->where('id', $request->guide_id)->first();

            if ($guide) {

                $guideData = [
                    'id' => $guide->id,
                    'name' => $guide->name,
                    'email' => $guide->email,
                    'gender' => $guide->number,
                    'image' => $guide->image,
                    'age'=> $guide->age,
                    'yearsofExperience' => $guide -> yearsofExperience,
                   'location' => $guide ->location,
                   'bio' => $guide->bio ,
                    'rating' => round(Guide_Rates::where('guide_id' , $guide->id)->avg('rate'),1)
                ];

                return response()->json([
                    'message' => 'User you needed is',
                    'user' => $guideData,
                ]);
            } else {
                return response()->json([
                    'message' => 'User not found',
                ], 404);
            }
        }


    }






