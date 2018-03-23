<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Role extends Model
{
    protected $table='admin_roles';
    protected $fillable=['name','description']; 
    public function permissions()
    {
        return $this->belongsToMany(Permission::class,'admin_permission_role','role_id','permission_id');
    }
    public function users()
    {
        return $this->belongsToMany(Adminusers::class,'admin_role_user','role_id','user_id');
    }
    

}
