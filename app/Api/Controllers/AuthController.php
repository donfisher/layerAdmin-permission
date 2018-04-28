<?php
namespace App\Api\Controllers;

use App\Client;
use Illuminate\Http\Request;
use JWTAuth;
use Tymon\JWTAuth\Exceptions\JWTException;
use Tymon\JWTAuth\Exceptions\TokenExpiredException;
use Tymon\JWTAuth\Exceptions\TokenInvalidException;
use App\Http\Resources\UserList as UserListResource;


class AuthController extends BaseController
{
    /**
     * The authentication guard that should be used.
     *
     * @var string
     */
    public function __construct()
    {
        parent::__construct();

    }

    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function authenticate(Request $request)
    {
        $payload = [
            'user_email' => $request->get('email'),
            'password' => $request->get('password')
        ];
        try {

            if (!$token = JWTAuth::attempt($payload)) {
                return response()->json(['code'=>1,'msg'=>'用户名或密码错误！','data'=>''],200);
            }
        } catch (JWTException $e) {
            return response()->json(['code'=>1,'msg'=>'不能创建token！','data'=>''],500);
        }
        return response()->json(['code'=>0,'msg'=>'登陆成功！','data'=>['access_token'=>$token]],200);
    }

    /**
     * @param Request $request
     */
    public function register(Request $request)
    {
        $newUser = [
            'user_email' => $request->get('email'),
            'user_name' => $request->get('name'),
            'password' => bcrypt($request->get('password'))
        ];
        $user = Client::create($newUser);
        $token = JWTAuth::fromUser($user);
        return $token;
    }

    /****
     * 获取用户的信息
     * @return \Illuminate\Http\JsonResponse
     */
    public function AuthenticatedUser()
    {
        try {
            if (!$user = JWTAuth::parseToken()->authenticate()) {
                return response()->json(['user_not_found'], 404);
            }
        } catch (TokenExpiredException $e) {
            return response()->json(['token_expired'], $e->getStatusCode());
        } catch (TokenInvalidException $e) {
            return response()->json(['token_invalid'], $e->getStatusCode());
        } catch (JWTException $e) {
            return response()->json(['token_absent'], $e->getStatusCode());
        }
        // the token is valid and we have found the user via the sub claim
        return response()->json(compact('user'));
    }

    /**
     * @param Request $request
     * @return UserListResource
     */
    public function userList(Request $request)
    {
        $page = $request->get('page')-1;
        if($page < 0) $page = 0;
        $pageSize = $request->get('limit');
        $lists = Client::skip($page??0)->take($pageSize??10)->get();
        dd($lists);
        return new UserListResource($lists);
    }
}
