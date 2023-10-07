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
            'access_token'=>$token,
            'user'=>$user,
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
            return response()->json($validator->errors()->toJson(),400);
        }
        $credentials=$request->only(['email','password']);

        if($token = Auth::guard('api')->attempt($credentials))
        {
            $user = Auth::guard('api')->user();

            return response()->json([
                'access_token'=>$token,
                'user'=> [
                    "id" => $user->id,
                    "email" => $user->email,
                    "name" => $user->name,
                    "image" => $user->image
                ],                'type' => 0,
            ]);
        }else if($token = Auth::guard('guide-api')->attempt($credentials)){
            $user = Auth::guard('guide-api')->user();
            return response()->json([
                'access_token'=>$token,
                'user'=> [
                    "id" => $user->id,
                    "email" => $user->email,
                    "name" => $user->name,
                    "image" => $user->image
                ],
                'type' => 1,
            ]);
        }
        else if($token = Auth::guard('admin-api')->attempt($credentials)){
            $user = Auth::guard('admin-api')->user();
            return response()->json([
                'access_token'=>$token,
                'user'=> [
                    "id" => $user->id,
                    "email" => $user->email,
                    "name" => $user->name,
                    "image" => $user->image
                ],
                'type' => 2,
            ]);
        }
        return response()->json(["message" => "error while logging in"],400);
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
   public function logout(Request $request)
    {
        $request->user()->logout();
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
            'number'=>'string',
            'image' => 'nullable',


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


        if($request->exists('number')){
            $user->number= $input['number'] ;
        }
        if ($request->image && $request->image->isValid()){

            $file_extension = $request->image->extension();
            $file_name = time() . '.' . $file_extension;
            $request->image->move(public_path('images/activity_images'), $file_name);
            $path = "public/images/activity_images/$file_name";

            $user->image = $path;

        }

        $user->save();

        return response()->json(['user'=>$user,'msg'=>'user update succefully']);
    }
    

    public function changePassword (Request $request){

        $validator = Validator::make($request->all(), [

       'old_password' => 'required',
       'password' => 'required|min:8',
       'confirm_password' => 'required|same:password'

]);
        if ($validator->fails()) {
           return response()->json([
            'message'=> 'Validator fails',
            'error'=>$validator->errors()]);
}

         $user = $request->user();
         if(Hash::check($request->old_password , $user->password)){

            $user->update([
             'password' => Hash::make($request->password)
            ]);
            return response()->json([
                'message' => 'Change password Successsfuly'
                ] ,200);
}

           else {
               return response()->json([
                'message' => 'Old password does not matched'
                ] ,400);
}
}
    public function DeleteMyAccount()
    {
        $user = Auth::guard('api')->user();
            User::where('id' , $user->id)->delete();
            Auth::logout();

        return response()->json([
            'message' => 'Account deleted Successsfuly'
        ]);

    }
}
