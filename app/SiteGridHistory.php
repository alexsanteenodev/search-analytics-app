<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class SiteGridHistory extends Model
{
    protected $fillable = ['site_id', 'url', 'code', 'index', 'title'];
    
    
    public function sites()
    {
        return $this->belongsTo('App\SitesGrid');
    }


    
}
