<?php

namespace App\Models;

use CodeIgniter\Model;

class CourseModel extends Model
{
    protected $db;
    public function __construct()
    {
        $this->db = db_connect();

    }
    
    protected $table            = 'course';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = [];

    // Dates
    protected $useTimestamps = false;
    protected $dateFormat    = 'datetime';
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';
    protected $deletedField  = 'deleted_at';

    // Validation
    protected $validationRules      = [];
    protected $validationMessages   = [];
    protected $skipValidation       = false;
    protected $cleanValidationRules = true;

    // Callbacks
    protected $allowCallbacks = true;
    protected $beforeInsert   = [];
    protected $afterInsert    = [];
    protected $beforeUpdate   = [];
    protected $afterUpdate    = [];
    protected $beforeFind     = [];
    protected $afterFind      = [];
    protected $beforeDelete   = [];
    protected $afterDelete    = [];


    public function get_user_courses($user_id=null){
        if($user_id){
            $uc=$this->db->table('user_course'); 
            $uc->select('course.*,users.name as name,users.id as user_id,count(course.id) as total_course');           
            $uc->join('course', 'course.id = user_course.course_id');
            $uc->join('users', 'users.id = user_course.user_id');
            $uc->where('user_course.user_id', $user_id);
            $uc->groupBy('course.id');
            $uc->orderBy('course.id','desc');
            $result = $uc->get()->getResult();
            // echo $this->db->getLastQuery();
            // echo "<pre>";
            // print_r($result);die;
            return $result;
        }else{
            return [];
        }
    }
}
