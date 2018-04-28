<?php
namespace App\Api\Controllers;

use App\Client;
use App\Leads;
use App\Http\Resources\Leads as LeadsResource;
use Illuminate\Http\Request;
use JWTAuth;
use Symfony\Component\HttpFoundation\JsonResponse;
use Tymon\JWTAuth\Exceptions\JWTException;
use Tymon\JWTAuth\Exceptions\TokenExpiredException;
use Tymon\JWTAuth\Exceptions\TokenInvalidException;


class LeadsController extends BaseController
{
    /**录入leads
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function create(Request $request)
    {
        $date['relationship'] = $request->get('relationship');//D or M
        $date['phone'] = $request->get('phone');
        $date['name'] = $request->get('name');
        $date['parent_name'] = $request->get('parent_name');
        $date['birthday'] = strtotime($request->get('birthday'));
        $date['mark'] = strtotime($request->get('mark'));

        if(!Leads::create($date)){
            return response()->json(['error' => 'leads录入失败！'], 500);
        }
        return response()->json(['success' => 'leads录入成功！'], 200);
    }

    /**leads列表
     * @param Request $request
     * @return LeadsResource
     */
    public function lists(Request $request)
    {
        $page = $request->get('page')-1;
        if($page < 0) $page = 0;
        $pageSize = $request->get('pageSize');
        $lists = Leads::skip($page??0)->take($pageSize??10)->get();
        return new LeadsResource($lists);
    }

}