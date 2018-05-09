<?php

namespace App\Http\Resources;

use App\Client;
use App\Permissions;
use Illuminate\Http\Resources\Json\ResourceCollection;
use Spatie\Permission\Contracts\Role;

class PermissionList extends ResourceCollection
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
            'count' => Permissions::count(),
            'name' => $request->get('name','')
        ];
        //return parent::toArray($request);
    }
}
