<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class SiteGrid extends Model
{

    protected $fillable = ['project_id', 'url','domain_end', 'host_end', 'ssl_end'];

    public function project()
    {
        return $this->belongsTo('App\Projects');
    }

    public function history()
    {
        return $this->hasMany('App\SiteGridHistory',
            'site_id',
            'id'
        );
    }

    protected static function boot()
    {
        parent::boot();

        static::saving(function($model){
            
        });
    }
}
