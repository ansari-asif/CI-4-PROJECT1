<?php
namespace App\Controllers;
use App\Models\UserModel;
use CodeIgniter\API\ResponseTrait;


class User extends BaseController{
    use ResponseTrait;
    private $userModel;

    public function __construct(){
        $this->userModel = new UserModel();
    }

    public function add_user(){
        $validation=\Config\Services::validation();
        $request=\Config\Services::request();
        $validationMessages = [
            'profile_image' => [
                'uploaded' => 'Please choose a valid image file.',
                'max_size' => 'The image file size should not exceed 1MB.',
                'is_image' => 'Please choose a valid image file.',
            ],
        ];

        $validationRules=[
            'name' => 'required|min_length[3]|max_length[50]',
            'email' => 'required|valid_email',
            'phone' => 'required|min_length[10]|integer',
            'password'     => 'required|min_length[6]|max_length[255]',
            'pass_confirm' => 'required|max_length[255]|matches[password]',
        ];

        if($this->request->getFile('profile_image')->getSize()>0){
            $validationRules['profile_image']='uploaded[profile_image]|max_size[profile_image,1024]|is_image[profile_image]';
        }

        $validation->setRules($validationRules,$validationMessages);
        
        if($validation->withRequest($request)->run()){
            $validData=$validation->getValidated();
            // process image upload 
            $profileImage=$this->request->getFile('profile_image');
            $newFileName = '';
            if ($profileImage->getSize() > 0 && $profileImage->isValid() && !$profileImage->hasMoved()) {
                $newFileName = $profileImage->getRandomName();
                // echo $newFileName;die;
                $profileImage->move(ROOTPATH . 'public/uploads/profile_image', $newFileName);
            }
            // print_r($profileImage);die;
            $user=$this->userModel->where('email',$validData['email'])->first();
            if($user){
                return $this->respond([
                    "status" => "false",
                    "message" => "Email already exists."
                ]);
            }else{
                $hashedPassword=password_hash($validData['password'],PASSWORD_DEFAULT);
                $userId=$this->userModel->insert([
                    "name"=>$validData['name'],
                    "email"=>$validData['email'],
                    "phone"=>$validData['phone'],
                    "password"=>$hashedPassword,
                    "created_at"=>date('Y-m-d H:i:s')
                ]);
                $user_details=$this->userModel->get_user_details($userId);
                return $this->respond([
                    "status" => "true",
                    "message" => "User Added successfully",
                    "data" => $user_details
                ]);
            }
            return $this->respond([
                "status" => "true",
                "data" => $validData
            ]);
        }else{
            $errors=$validation->getErrors();
            return $this->respond([
                "status" => "false",
                "message" => $errors
            ]);
        }
    }

    public function edit_user(){

    }

    public function delete_user(){

    }
}

?>