<?php

namespace App\Controllers;

use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\RESTful\ResourceController;
use Firebase\JWT\JWT;
use Config\Services;
use Firebase\JWT\Key;
use App\Models\UserModel;

class User extends ResourceController
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
        unset($user['password']);
        $user['token'] = $token;
        $response = [
                'status'   => 200,
                'error'    => null,
                'messages' => [
                    'success' => 'User Details',
                    ],
                'data' => [
                    'user' => $user
                    ]
                ];
        return $this->respond($response);
    }
}
