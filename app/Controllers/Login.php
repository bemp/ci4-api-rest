<?php
namespace App\Controllers;

use CodeIgniter\RESTful\ResourceController;
use CodeIgniter\API\ResponseTrait;
use App\Models\UserModel;
use Firebase\JWT\JWT;

class Login extends ResourceController
{
    use ResponseTrait;
    /**
     * Return an array of resource objects, themselves in array format
     *
     * @return mixed
     */
    public function index()
    {
        helper(['form']);
        $rules = [
            'email' => 'required|valid_email',
            'password' => 'required|min_length[6]'
        ];

        if (!$this->validate($rules)) {
            return $this->fail($this->validator->getErrors());
        }
        $model = new UserModel();
        $user = $model->where('email', $this->request->getVar('email'))->first();

        if (!$user) {
            return $this->failNotFound('Email Not Found');
        } 

        $authenticate = password_verify($this->request->getVar('password'), $user['password']);

        if (!$authenticate) {
            return $this->fail('Wrong Password');
        }
 
        $payload = array(
            'iat' => 1356999524,
            'nbf' => 1357000000,
            'uid' => $user['id'],
            'email' => $user['email']
        );

        $token = JWT::encode($payload, getenv('TOKEN_SECRET'), getenv('ALGORITHMS'));

        $cache = \Config\Services::cache();
        $cache->delete($token);
    
        $response = [
                'status'   => 200,
                'error'    => null,
                'messages' => [
                    'success' => 'User Logged',
                    ],
                'data' => [
                    'token' => $token
                    ]
                ];

        return $this->respond($response);
    }
}
