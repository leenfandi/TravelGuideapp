<?php

namespace App\Http\Controllers;


use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;

class RegisterController extends Controller
{
    protected $db_mysql;
    public function __construct()
    {
        $this ->db_mysql= config('database.connections.mysql.database');
     $this->middleware('auth:api',['except'=>['login','register']]);
    }

    public function register (Request $request)
    {
        $userr = new user();

        $validator =Validator::make($request->all(),[
            'name'=>'required',
            'email'=>'required|string|email|unique:users',
            'password'=>'required|min:8',
            'number' => 'required|numeric' ,
            'image' => 'nullable',

        ]);

        if ($validator->fails())
        {
            return response()->json($validator->errors()->toJson(),400);
        }
        if ($request->image){

            $file_extension = $request->image->extension();
            $file_name = time() . '.' . $file_extension;
            $request->image->move(public_path('images/users_images'), $file_name);
            $path = "public/images/users_images/$file_name";
            $userr->image = $path;

        }
        $user=User::create(array_merge(
            $validator->validated(),
            ['password'=>bcrypt($request->password)]
        ));
        $credentials=$request->only(['email','password']);
        $token=Auth::guard('api')->attempt($credentials);

        return response()->json([
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

     if(!$token=Auth::guard('api')->attempt($credentials))
     {
       return response()->json(['error'=>'Unauthorized'],401);
     }
      User::where('email' , $request->email);
     return response()->json([
         'access_token'=>$token,
         'user'=>Auth::guard('api')->user(),

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
        Auth::guard('api')->logout();
        return response()->json(['message' => 'Successfully logged out']);
    }


    public function updateProfile(Request $request)
    {

        $input = $request->all();
        $id = Auth::guard('api')->id();
       $user = User::find($id);
        $validator = validator($input, [
            'name'=>'string',
            'email'=>'string|email|unique:users',
            'password'=>'min:8',
            'number'=>'string',
            'image' => 'string',


        ]);
        if ($validator->fails()) {
            return response()->json(['error'=>$validator->errors()]);
        }

        if($request->exists('name')){
        $user->name= $input['name'] ;
        }
        if($request->exists('email')){
        $user->email= $input['email'] ;
        }
        if($request->exists('password')){
        $user->password=  Hash::make($input['password'])  ;
        }

        if($request->exists('number')){
            $user->number= $input['number'] ;
        }
        if ($request->image && $request->image->isValid()){

            $file_extension = $request->image->extension();
            $file_name = time() . '.' . $file_extension;
            $request->image->move(public_path('images/drivers'), $file_name);
            $path = "public/images/drivers/$file_name";

            $user->image = $path;

        }

        $user->save();

        return response()->json(['user'=>$user,'msg'=>'user update succefully']);
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




}
