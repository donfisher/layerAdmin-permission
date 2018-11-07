<?php
namespace App\Api\Controllers;

use App\Client;
use Illuminate\Http\Request;
use JWTAuth;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Tymon\JWTAuth\Exceptions\JWTException;
use Tymon\JWTAuth\Exceptions\TokenExpiredException;
use Tymon\JWTAuth\Exceptions\TokenInvalidException;
use App\Http\Resources\UserList as UserListResource;
use App\Http\Resources\RoleList as RoleListResource;
use App\Http\Resources\PermissionList as PermissionListResource;


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

    /**登录
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

    /** register注册
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function register(Request $request)
    {
        $newUser = [
            'user_email' => $request->get('email'),
            'user_name' => $request->get('name'),
            'password' => bcrypt($request->get('password')),
            'status' => $request->get('status',0)?1:0
        ];
        if($id = $request->get('id')){
            //编辑
            if($request->get('password')=='******'){
                unset($newUser['password']);
            }
            $user = Client::find($id);
            if(!$user)  return response()->json(['code'=>1,'msg'=>'用户不存在！']);
            $res = Client::where('id',$user->id)->update($newUser);
            //删除原角色
            $userRole = $user->roles()->first();
            $user->removeRole($userRole->name);
            //分配新角色
            $role = $user->assignRole($request->get('role'));
            if($res && $role){
                return response()->json(['code'=>0,'msg'=>'编辑成功！']);
            }
            return response()->json(['code'=>1,'msg'=>'编辑失败！']);
        }else{
            //创建
            $user = Client::create($newUser);
            if(!$request->get('role')){
                return response()->json(['code'=>1,'msg'=>'请选择权限！']);
            }
            //分配角色
            $role = $user->assignRole($request->get('role'));
            //$token = JWTAuth::fromUser($user);
            if($user && $role){
                return response()->json(['code'=>0,'msg'=>'创建成功！']);
            }
            return response()->json(['code'=>1,'msg'=>'创建失败！']);
        }
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

        $lists = Client::orderBy('id','desc')->skip($page*$pageSize??0)->take($pageSize??10)->get();
        return new UserListResource($lists);
    }

    /**角色列表
     * @param Request $request
     * @return RoleListResource
     */
    public function roleList(Request $request)
    {
        $page = $request->get('page')-1;
        if($page < 0) $page = 0;
        $pageSize = $request->get('limit');

        $roleList = Role::orderBy('id','desc')->skip($page*$pageSize??0)->take($pageSize??10)->get();
        return new RoleListResource($roleList);
    }

    /**权限列表
     * @param Request $request
     * @return PermissionListResource
     */
    public function permissionList(Request $request)
    {
        $page = $request->get('page')-1;
        if($page < 0) $page = 0;
        $pageSize = $request->get('limit');

        $lists = Permission::orderBy('id','desc')->skip($page*$pageSize??0)->take($pageSize??10)->get();
        return new PermissionListResource($lists);
    }

    /**新增和编辑权限
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function permissionAdd(Request $request)
    {
        $input = $request->except('token');
        $data['guard_name'] = 'web';
        $data['name'] = $input['name'];
        $data['status'] = isset($input['on'])&&$input['on'] == 'on' ? 1 : 0;
        if($input['id']){
            Permission::where('id',$input['id'])->update($data);
        }else{
            Permission::create($data);
        }
        return response()->json(['code'=>0,'msg'=>'保存成功！']);
    }

    /**删除权限
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function permissionDelete(Request $request)
    {
        $res = Permission::where('id',$request->get('id',0))->delete();
        if($res){
            return response()->json(['code'=>0,'msg'=>'删除成功！']);
        }else{
            return response()->json(['code'=>1,'msg'=>'删除失败！']);
        }
    }

    /**获取可用权限
     * @return PermissionListResource
     */
    public function allPermission(Request $request)
    {
        $permissions = Permission::where('status',1)->get();
        if($id = $request->get('id')){
            //编辑
            $role = Role::findById($id);
            $permissionIds = $role->permissions()->allRelatedIds()->toArray();
            foreach ($permissions as $v){
                $v->checked = '';
                if(in_array($v->id,$permissionIds)){
                    $v->checked = 'checked';
                }
            }
        }
        request()->offsetSet('name',$role->name??'');//追加name到request
        return new PermissionListResource($permissions);
    }


    /**添加和编辑角色
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function roleAdd(Request $request)
    {
        if($request->get('id')){
            //编辑
            $role = Role::findById($request->get('id'));
            if($role->name != $request->get('name')){
                //更新角色名称
                Role::where('id',$request->get('id'))
                    ->update(['name'=>$request->get('name')]);
            }
            //删除原permission
            $permission = $role->permissions()->allRelatedIds()->toArray();
            $permission = Permission::whereIn('id',$permission)->get();
            $role->revokePermissionTo($permission);
            //创建permission
            $permissionNew = Permission::whereIn('id',array_keys($request->get('permission')))->get();
            $res = $role->givePermissionTo($permissionNew);
            if($res){
                return response()->json(['code'=>0,'msg'=>'角色编辑成功！']);
            }else{
                return response()->json(['code'=>1,'msg'=>'角色编辑失败！']);
            }
        }else{
            if(!$request->get('name')) return response()->json(['code'=>1,'msg'=>'角色名称不能为空！']);
            $permission = Permission::whereIn('id',array_keys($request->get('permission')))->get();
            $role = Role::create(['name' => $request->get('name')]);
            $res = $role->givePermissionTo($permission);
            if($res){
                return response()->json(['code'=>0,'msg'=>'角色添加成功！']);
            }else{
                return response()->json(['code'=>1,'msg'=>'角色添加失败！']);
            }
        }
    }
    /**删除角色
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function roleDelete(Request $request)
    {
        $roleId = $request->get('id',0);
        //删除绑定权限
        $role = Role::findById($roleId);
        $permission = $role->permissions()->allRelatedIds()->toArray();
        $permission = Permission::whereIn('id',$permission)->get();
        $role->revokePermissionTo($permission);
        //删除角色
        $res = Role::where('id',$roleId)->delete();
        if($res){
            return response()->json(['code'=>0,'msg'=>'删除成功！']);
        }else{
            return response()->json(['code'=>1,'msg'=>'删除失败！']);
        }
    }

    /**获取可用角色
     * @param $request
     * @return RoleListResource
     */
    public function allRoles(Request $request)
    {
        $roles = Role::get();
        if($id = $request->get('id')){
           $user = Client::find($id);
            $hasRole = $user->roles()->pluck('id')->toArray();
            foreach ($roles as $v){
                $v->selected = '';
                if(in_array($v->id,$hasRole)){
                    $v->selected = 'selected';
                }
            }
            request()->offsetSet('name',$user->user_name);
            request()->offsetSet('email',$user->user_email);
            request()->offsetSet('status',$user->status);
        }
        return new RoleListResource($roles);
    }

    /**修改管理员状态
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function changeAdminStatus(Request $request)
    {
        $id = $request->get('id');
        $data['status'] = $request->get('status')?1:0;
        if($id){
            Client::where('id',$id)->update($data);
            return response()->json(['code'=>0,'msg'=>'操作成功！']);
        }
        return response()->json(['code'=>1,'msg'=>'操作失败！']);
    }
    /**修改权限状态
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function changePermissionStatus(Request $request)
    {
        $id = $request->get('id');
        $data['status'] = $request->get('status')?1:0;
        if($id){
            Permission::where('id',$id)->update($data);
            return response()->json(['code'=>0,'msg'=>'操作成功！']);
        }
        return response()->json(['code'=>1,'msg'=>'操作失败！']);
    }
}
