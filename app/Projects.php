<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Projects extends Model
{

    protected $fillable = ['name', 'logo', 'link', 'kpi', 'domain_owner_name','domain_owner_email','host_owner_name','host_owner_email'];


    public function sites()
    {
        return $this->hasMany('App\SiteGrid',
            'project_id',
            'id'
            );
    }

  
}
