<?php

namespace Androsyz\MetaforgeGql\Repo;

class AuthRepo {
    static $findOauthQry = ' SELECT * FROM access_tokens WHERE access_token = ';

    public static function findAuthByAccessToken($accessToken) {
        global $db;
        return @$db->query(self::$findOauthQry . "'$accessToken'")[0];
    }
}