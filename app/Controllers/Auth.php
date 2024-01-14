<?php
namespace App\Controllers;
use App\Models\UserModel;
use CodeIgniter\API\ResponseTrait;
use Firebase\JWT\JWT;
use CodeIgniter\HTTP\Response;
use CodeIgniter\Session\Session;
use CodeIgniter\Cookie\Cookie;


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


    public function login_page(){
        if($this->request->getMethod()=='post'){
            $rules = [
                'password' => 'required|max_length[255]|min_length[6]',
                'email'    => 'required|max_length[254]|valid_email',
            ];
            $validation=\Config\Services::validation();
            $request=\Config\Services::request();
            $validation->setRules($rules);            
            $data['data']=$this->request->getPost();
            if($validation->withRequest($request)->run()){
                $validData=$validation->getValidated();
                $user_details=$this->userModel->where('email',$validData['email'])->first();
                if($user_details){
                    $checkPassword=password_verify($validData['password'],$user_details['password']);
                    if($checkPassword){
                        $session=session();
                        $session->set($user_details);
                        $session->set('isLoggedIn',true);
                        return redirect('courses');
                    }else{
                        $data['error']='Wrong Password';
                    }
                    
                }else{
                    $data['error']='Wrong email address';
                }
            }else{
                $errors=$validation->getErrors();
                $data['errors']=$errors;
            }
            return view('incld/header').
            view('Auth/login',$data).
            view('incld/footer');
        }else{
            return view('incld/header').
            view('Auth/login').
            view('incld/footer');
        }
        
    }
}

?>