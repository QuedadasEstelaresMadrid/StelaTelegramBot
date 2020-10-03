<?php namespace App\Models;

use CodeIgniter\Model;

class Summary extends Model
{
    protected $table      = 'stela_summary';
    protected $primaryKey = 'id';

    protected $returnType     = 'array';
    protected $useSoftDeletes = false;


    protected $allowedFields = ['msg', 'msg_from', 'msg_id', 'msg_group'];

}