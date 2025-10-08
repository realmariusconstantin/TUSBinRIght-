<?php
namespace App\Models;

use CodeIgniter\Model;

class UserModel extends Model
{
    protected $table = 'users'; // change if your table name differs
    protected $primaryKey = 'id';
    protected $allowedFields = ['username','email','password','role'];
    protected $returnType = 'array';
}
