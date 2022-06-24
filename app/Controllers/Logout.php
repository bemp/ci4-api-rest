<?php
namespace App\Controllers;

use CodeIgniter\RESTful\ResourceController;
use Firebase\JWT\JWT;
use Config\Services;
use Firebase\JWT\Key;
use App\Models\UserModel;

class Logout extends ResourceController
{
    /**
     * Return an array of resource objects, themselves in array format
     *
     * @return mixed
     */
    public function index()
    {
        $key = getenv('TOKEN_SECRET');
        $header = $this->request->getServer('HTTP_AUTHORIZATION');
        $token = explode(' ', $header)[1];
        $userData = JWT::decode($token, new Key($key, getenv('ALGORITHMS')));
        $modelUser = new UserModel();
        $user = $modelUser->where('id', $userData->uid)->first();
        $cache = \Config\Services::cache();
        //save token to blackisted 
        $cache->save($token, $token, 3600);

        $response = [
                'status'   => 200,
                'error'    => null,
                'messages' => [
                    'success' => 'User Logout',
                    ],
                'data' => null
                ];

        return $this->respond($response);
    }
}
