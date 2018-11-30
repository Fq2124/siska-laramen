<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Vacancies extends Model
{
    protected $table = 'vacancy';

    protected $guarded = ['id'];

    public function agencies()
    {
        return $this->belongsTo(Agencies::class, 'agency_id');
    }

    public function getAccepting()
    {
        return $this->hasMany(Accepting::class,'vacancy_id');
    }

    public function getInvitation()
    {
        return $this->hasMany(Invitation::class, 'vacancy_id');
    }

    public function getQuizInfo()
    {
        return $this->hasOne(QuizInfo::class, 'vacancy_id');
    }

    public function getPlan()
    {
        return $this->belongsTo(Plan::class, 'plan_id');
    }
}
