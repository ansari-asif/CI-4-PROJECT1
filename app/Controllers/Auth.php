<?php
namespace App\Controllers;
use App\Models\UserModel;
use CodeIgniter\API\ResponseTrait;
use Firebase\JWT\JWT;

class Auth extends BaseController{
    use ResponseTrait;
    private $userModel;

    public function __construct()
    {
        $this->userModel=new UserModel();
    }

    public function registration(){
        
        $validation=\Config\Services::validation();
        $request=\Config\Services::request();
        $validation->setRules([
            'name' => 'required|min_length[3]|max_length[50]',
            'email' => 'required|valid_email',
            'phone' => 'required|min_length[10]|integer',
            'password'     => 'required|min_length[6]|max_length[255]',
            'pass_confirm' => 'required|max_length[255]|matches[password]',
        ]);        
       
        if ($validation->withRequest($request)->run()) {
            $validData = $validation->getValidated();
            $email=$validData['email'];
            $password=$validData['password'];
            // echo $email;die;
            // print_r($user);die;
            $hashedPassword=password_hash($password,PASSWORD_DEFAULT);
            $user = $this->userModel->where('email', $email)->first();
            if($user){
                return $this->respond([
                    "status"=>false,
                    "message"=>"Email already exists.",                   
                ]);
            }else{
                $userId = $this->userModel->insert([
                    'name' => $this->request->getPost('name'),
                    'email' => $this->request->getPost('email'),
                    'phone' => $this->request->getPost('phone'),
                    'password' => $hashedPassword,
                    "created_at" => date("Y-m-d H:i:s"),
                ]);
                $user_details=$this->userModel->get_user_details($userId);
                return $this->respond([
                    "status"=>true,
                    "message"=>"Registration successfully.",
                    "data"=>$user_details,                    
                ]);
            }
        }else{
            $errors =$validation->getErrors();
            return $this->respond([
                "status"=>false,
                "message"=>$errors
            ]);
        }
    }

    public function login(){
        $validation=\Config\Services::validation();
        $request=\Config\Services::request();
        $validation->setRules([
            "email"=>"required|valid_email",
            "password"=>"required",
        ]);

        if($validation->withRequest($request)->run()){
            $validData=$validation->getValidated();
            $email=$validData['email'];
            $password=$validData['password'];
            $user=$this->userModel->where('email',$email)->first();
            if($user){
                if(password_verify($password,$user['password'])){
                    $key='jwt';
                    $payload = [
                        'user_id' => $user['id'],
                        'email' => $user['email'],
                    ];
                    $token=JWT::encode($payload,$key,'HS256');
                    return $this->respond([
                        "status"=>true,
                        "message"=>"Logged in successfully",
                        "data"=>$user,
                        "token"=>$token
                    ]);
                }else{
                    return $this->respond([
                        "status"=>false,
                        "message"=>"Wrong password",
                    ]);
                }
            }else{
                return $this->respond([
                    "status"=>false,
                    "message"=>"Email not found",
                ]);
            }
            return $this->respond([
                "status"=>true,
                "message"=>$validData
            ]);
        }else{
            $errors=$validation->getErrors();
            return $this->respond([
                "status"=>false,
                "message"=>$errors
            ]);
        }
    }
}

?>