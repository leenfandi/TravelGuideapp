<?php

namespace App\Http\Controllers;


use App\Models\Guide;
use App\Models\Admin;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\Controller;
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
            'image' => 'nullable',
            'location'=> 'required',
            'bio' => 'nullable'

        ]);
        if ($validator->fails())
        {
            return response()->json($validator->errors()->toJson(),400);
        }

        if ($request->hasFile('image')) {
            $image = $request->file('image');
            $imageName = time() . '.' . $image->getClientOriginalExtension();
            $imagePath = 'public/images/guides_images/' . $imageName;
            $image->move(public_path('images/guides_images'), $imageName);
            $guidee->image=$imagePath;

        }



        $guide=Guide::create(array_merge(
            $validator->validated(),
            ['password'=>bcrypt($request->password)]
        ));
        $credentials=$request->only(['email','password']);
        $token=Auth::guard('guide-api')->attempt($credentials);

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
            $guides = Guide::select('id', 'name', 'gender', 'age', 'yearsofExperience', 'image', 'location')->get();

            $response = [];

            foreach ($guides as $guide) {
                $image = is_null($guide->image) ? 'null' : asset($guide->image);

                $response[] = [
                    'id' => $guide->id,
                    'name' => $guide->name,
                    'image' => $image,
                    'age' => $guide->age,
                    'yearsofExperience' => $guide->yearsofExperience,
                    'location' => $guide->location
                ];
            }

            return response()->json($response);
        }

    }


        /**
         * Refresh a token.
         *
         * @return \Illuminate\Http\JsonResponse
         */
       /* public function refresh()
        {
            return $this->respondWithToken(auth()->refresh());
        }
        /**
         * Get the token array structure.
         *
         * @param  string $token
         *
         * @return \Illuminate\Http\JsonResponse
         */





