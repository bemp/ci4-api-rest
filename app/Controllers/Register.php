<?php
namespace App\Controllers;

use CodeIgniter\RESTful\ResourceController;
use App\Models\UserModel;
use CodeIgniter\API\ResponseTrait;

class Register extends ResourceController
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
            'name' => 'required|min_length[3]',
            'email' => 'required|valid_email|is_unique[users.email]',
            'phone_no' => 'required|is_unique[users.phone_no]|integer',
            'email' => 'required|valid_email',
            'phone_no' => 'required|integer',
            'password' => 'required|min_length[6]'
        ];

        if (!$this->validate($rules)) {
            return $this->fail($this->validator->getErrors());
        } 

        $userData = [
            'name' => $this->request->getVar('name'),
            'email'     => $this->request->getVar('email'),
            'phone_no'  => $this->request->getVar('phone_no'),
            'password'  => password_hash($this->request->getVar('password'), PASSWORD_BCRYPT) 
        ];

        $model = new UserModel();
        $registered = $model->insert($userData);
        $userData['id'] = $model->getInsertID();
        unset($userData['password']);

        if ($registered) {
            $response = [
                'status'   => 200,
                'error'    => null,
                'messages' => [
                    'success' => 'User Created'
                    ],
                'data' => $userData
                ];
        }  else {
             $response = [
                'status'   => 400,
                'error'    => 400,
                'messages' => [
                    'error' => 'Invalid Request'
                    ]
                ];
        }

        return $this->respond($response);
    }
}
