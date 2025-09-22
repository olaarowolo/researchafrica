<?php
namespace App\Http\Controllers\Traits;


trait MemberTypeTrait
{
    public function author()
    {
        if(auth('member')->user()->member_type_id == 1){
            return true;
        }

        return false;
    }

    public function editor()
    {
        if(auth('member')->user()->member_type_id == 2){
            return true;
        }

        return false;
    }

    public function reviewer()
    {
        if(auth('member')->user()->member_type_id == 3){
            return true;
        }

        return false;
    }

    public function reviewerFinal()
    {
        if(auth('member')->user()->member_type_id == 6){
            return true;
        }

        return false;
    }

    public function account()
    {
        if(auth('member')->user()->member_type_id == 4){
            return true;
        }

        return false;
    }

    public function publisher()
    {
        if(auth('member')->user()->member_type_id == 5){
            return true;
        }

        return false;
    }
}
