<?php
/**
 * User: jithinvijayan
 * Date: 15/03/19
 * Time: 9:51 AM
 */

namespace App\Http\Controllers\API;


use Illuminate\Support\Facades\DB;

class CredentialsController extends BaseController
{
    public function getClientDetails()
    {
        $response = DB::table('oauth_clients')
            ->select(array('id', 'secret'))
            ->where('password_client', 1)
            ->where("provider", "=", "users")
            ->get()
            ->first();
        return $this->sendResponse($response, 'Credentials retrieved successfully.');
    }

    public function getParentCredentials()
    {
        $response = DB::table('oauth_clients')
            ->select(array('id', 'secret'))
            ->where('password_client', 1)
            ->where("provider", "=", "parent")
            ->get()
            ->first();
        return $this->sendResponse($response, 'Credentials retrieved successfully.');
    }

}
