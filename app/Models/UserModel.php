<?php
namespace App\Models;
use CodeIgniter\Model;

class UserModel extends Model{
    protected $table      = 'users';
    protected $primaryKey = 'id';
    protected $useAutoIncrement = true;
    protected $allowedFields = ['name', 'email','phone','password','created_at','updated_at','deleted_at'];

    // Dates
    protected $useTimestamps = false;
    protected $dateFormat    = 'datetime';
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';
    protected $deletedField  = 'deleted_at';

    public function get_user_details($id){
        if(!$id){
            return $this->findAll();
        }
        return $this->where('id', $id)->first();
    }

    

}

?>