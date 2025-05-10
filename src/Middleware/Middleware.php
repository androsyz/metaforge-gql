<?php
namespace Androsyz\MetaforgeGql\Middleware;

use Androsyz\MetaforgeGql\Repo\AuthRepo;

class Middleware {

    public function serve($request){
        $accessToken = $request->header('access_token');
        $data = [];
        $data = AuthRepo::findAuthByAccessToken($accessToken);
        if (@$data['auth']) {
            $data['user_id'] = $data['user_id'];
        }
        
        $request->__set('data', $data);
        return $request;
    }
}