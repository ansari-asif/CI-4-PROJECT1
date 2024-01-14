<?php
namespace App\Controllers;
use App\Models\CourseModel;
use CodeIgniter\HTTP\RequestInterface;

class Course extends BaseController{
    protected $courseModel;
    public function __construct(){
        $this->courseModel = new CourseModel();
    }

   
    public function index(){
        $session=session();
        // print_r($_SESSION);die;
        $user_data=$session->get();
        $data['user_data']=$user_data;
        $user_id=$user_data['id'];
        $userCourse=$this->courseModel->get_user_courses($user_id);
        return  view('incld/header').
                view('Course/list',$data).
                view('incld/footer');
    }
}
?>