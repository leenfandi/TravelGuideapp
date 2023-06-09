<?php
namespace App\Http\Controllers;
use App\Models\Guide;
use App\Models\Admin;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;

class GuideAuthController extends Controller
{
    protected $db_mysql;
    public function __construct()
    {
        $this ->db_mysql= config('database.connections.mysql.database');

    }


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

     if(!$token=Auth::guard('guide-api')->attempt($credentials))
     {
       return response()->json(['error'=>'Unauthorized'],401);
     }
      Guide::where('email' , $request->email);
     return response()->json([
         'access_token'=>$token,
         'guide'=>auth()->guard('guide-api'),
         'type'=>'guide',
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
        Auth::guard('guide-api')->logout();
        return response()->json(['message' => 'Successfully logged out']);
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
