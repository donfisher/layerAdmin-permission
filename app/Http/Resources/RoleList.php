<?php

namespace App\Http\Resources;

use App\Client;
use App\Roles;
use Illuminate\Http\Resources\Json\ResourceCollection;
use Spatie\Permission\Contracts\Role;

class RoleList extends ResourceCollection
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {

        return [
            'code' => 0,
            'msg' => '数据拉取成功',
            'data' => $this->collection,
            'count' => Roles::count(),
            'name' => $request->get('name',''),
            'email' => $request->get('email',''),
            'status' => $request->get('status',0),
        ];
        //return parent::toArray($request);
    }
}
