<?php
namespace App\Models;
use CodeIgniter\Model;

class UserModel extends Model{
    protected $db;
    public function __construct(){
        parent::__construct();
        $this->db = $db??\Config\Database::connect();
    }
    protected $table      = 'users';
    protected $primaryKey = 'id';
    protected $useAutoIncrement = true;
    protected $allowedFields = ['name', 'email','phone','password','profile_image','created_at','updated_at','deleted_at'];

    // Dates
    protected $useTimestamps = false;
    protected $dateFormat    = 'datetime';
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';
    protected $deletedField  = 'deleted_at';

    public function get_user_details($id=null){
        if(!$id){
            return $this->findAll();
        }
        return $this->where('id', $id)->first();
    }

    public function get_users($id=null){
        $this->select('id,name,email,phone,profile_image,created_at,updated_at');
        if($id>0){
            $this->where('id',$id);
        }
        $result= $this->get()->getResult();
        // $lastQuery = $this->db->getLastQuery();
        // echo $lastQuery;die;
        return $result;
    }
    

}

?>