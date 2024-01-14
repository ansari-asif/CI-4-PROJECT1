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

    public function get_users($id=null){
        try {
           
            $user_list=$this->userModel->get_users($id);
            return $this->respond([
                "status"=>true,
                "message"=>"user list fetched successfully",
                "data"=>$user_list
            ]);
        } catch (\Throwable $th) {
            //throw $th;
            return $this->respond([
                "status"=>false,
                "message"=>"Something went wrong. Code Error...",
                "data"=>[]
            ]);
        }
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
        if($this->request->getFile('profile_image')&&$this->request->getFile('profile_image')->getSize()>0){
            $validationRules['profile_image']='uploaded[profile_image]|max_size[profile_image,1024]|is_image[profile_image]';
        }

        $validation->setRules($validationRules,$validationMessages);
        
        if($validation->withRequest($request)->run()){
            $validData=$validation->getValidated();
            // print_r($validData);die;
            // process image upload 
            $user=$this->userModel->where('email',$validData['email'])->first();
            // echo $newFileName;die;
            if($user){
                return $this->respond([
                    "status" => "false",
                    "message" => "Email already exists."
                ]);
            }else{
                $profileImage=$this->request->getFile('profile_image');
                $newFileName = '';
                if ($profileImage->getSize() > 0 && $profileImage->isValid() && !$profileImage->hasMoved()) {
                    $newFileName = $profileImage->getRandomName();
                    $profileImage->move(ROOTPATH . 'public/uploads/profile_image', $newFileName);
                    $newFileName=base_url().'/uploads/profile_image/'.$newFileName;
                }
                $hashedPassword=password_hash($validData['password'],PASSWORD_DEFAULT);
                $userId=$this->userModel->insert([
                    "name"=>$validData['name'],
                    "email"=>$validData['email'],
                    "phone"=>$validData['phone'],
                    "password"=>$hashedPassword,
                    "created_at"=>date('Y-m-d H:i:s'),
                    'profile_image'=>$newFileName
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

    public function edit_user($id=null){
        if($id){
            $userExist=$this->userModel->get_user_details($id);
            if(!$userExist){
                return $this->respond([
                    "status" => "false",
                    "message" => "User not found."
                ]);
            }
            $validation=\Config\Services::validation();
            $request=\Config\Services::request();
            $validationMessages=[
                'profile_image'=>[
                    'uploaded'=>"please choose valid image file",
                    'is_image'=>"please choose valid image file",
                    'max_size'=>"profile image size should not exceed of 1 MB.",
                ]
            ];

            $validationRules=[
                'name' => 'required|min_length[3]|max_length[50]',
                'email' => 'required|valid_email',
                'phone' => 'required|min_length[10]|integer',
            ];            
            $password=$request->getPost('password');           
            if($password!==null){
                $validationRules['password']='required|min_length[6]|max_length[255]';
                $validationRules['pass_confirm']='required|max_length[255]|matches[password]';
            }

            $profile_image=$this->request->getFile('profile_image');
            if($profile_image && $profile_image->getSize()>0){
                $validationRules['profile_image']="uploaded[profile_image]|is_image[profile_image]|max_size[profile_image,1024]";
            }
            $validation->setRules($validationRules,$validationMessages);
            if($validation->withRequest($request)->run()){
                $validData=$validation->getValidated();
                // print_r($validData);die;
                $existUser=$this->userModel->where('email',$validData['email'])->where('id!=',$id)->first();
                if($existUser){
                    return $this->respond([
                        "status"=>false,
                        "message"=>"Email already exists."
                    ]);
                }else{
                    $newFileName=null;
                    if($profile_image->getSize()>0 && $profile_image->isValid()&& !$profile_image->hasMoved()){
                        $newFileName=$profile_image->getRandomName();
                        $profile_image->move(ROOTPATH.'public/uploads/profile_images/',$newFileName);
                        $newFileName=base_url().'/uploads/profile_images/'.$newFileName;
                    }
                    $hashedPassword=password_hash($password,PASSWORD_DEFAULT);
                    $post_data=[
                        'name'=>$validData['name'],
                        'email'=>$validData['email'],
                        'phone'=>$validData['phone'],
                        'updated_at'=>date('Y-m-d H:i:s')
                    ];
                    if($newFileName){
                        $post_data['profile_image'] = $newFileName;
                    }

                    if($hashedPassword){
                        $post_data['password'] = $hashedPassword;
                    }
                    $this->userModel->update($id,$post_data);
                    $user_details=$this->userModel->get_user_details($id);
                    return $this->respond([
                        "status"=>true,
                        "message"=>"User updated successfully.",
                        "data"=>$user_details
                    ]);
                }
            }else{
                $errors=$validation->getErrors();
                // print_r($errors);die;
                return $this->respond([
                    "status"=>false,
                    "message"=>$errors
                ]);
            }
        }else{
            return $this->respond([
                "status" => "false",
                "message" => "Invalid parameter."
            ]);
        }
    }

    public function delete_user($id=null){
        if($id){
            $user_details=$this->userModel->get_user_details($id);
            
            if($user_details){
                $this->userModel->delete($id);
                return $this->respond([
                    'status' => true,
                    "message" =>"User Deleted Successfully"
                ]);
            }else{
                return $this->respond([
                    'status' => false,
                    "message" =>"User does not exist"
                ]);
            }
        }else{
            return $this->respond([
                "status" => "false",
                "message" => "Invalid parameter."
            ]);
        }
    }
}

?>