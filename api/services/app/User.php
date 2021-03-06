<?php

namespace App;

use Illuminate\Notifications\Notifiable;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Hash;

class User extends Model
{
    use Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'userId', 'emailId', 'password','userName','maxLevelReached','maxScore'
    ];
    protected $table = 'users';
    protected $primaryKey='userId';
    public $timestamps = false;

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */

    public static function logincheck($usercred)
    {
        $userval=User::where('userName', '=',$usercred['userName'])->get()->toArray();
        if(count($userval)==0)
            return false;
        else
        {
            if(!Hash::check($usercred['password'],$userval[0]['password']))
                return false;
            return true;

        }

    }
    public static function signup($usercred)
    {
        $user = new User;
        $userval=User::where('userName', '=',$usercred['userName'])->get()->toArray();
        if(count($userval)>0)
            return false;
        else
        {
            $user->emailId=$usercred['emailId'];
            $user->password=Hash::make($usercred['password']);
            $user->userName=$usercred['userName'];
            $user->maxLevelReached=$usercred['maxLevelReached'];
            $user->maxScore=$usercred['maxScore'];
            $user->save();
            return true;
        }

    }

    public static function initcall($usercred)
    {
        $userval=User::where('userName', '=',$usercred)->get()->toArray();
        if(count($userval)==0)
                        return false;
        return true;
    }

    public static function changePassword($usercred,$data)
    {
        $userval=User::where('userName', '=',$usercred)->get()->toArray();
        if(!Hash::check($data['oldpassword'],$userval[0]['password']))
                return false;
       $affectedRows= User::where('userName', '=',$usercred)->update(array('password' => Hash::make($data['newpassword'])));
        if(count($affectedRows)==0)
                        return false;
        return true;
    }
    public static function getLeaderboard()
    {
        $userval=User::orderBy('maxScore','DESC')->select('userName','userId','maxLevelReached','maxScore')->get()->toArray();
        if(count($userval)==0)
                        return null;
        return $userval;
    }

    public static function updateUserMaxLevelandScore($usercred, $currentScore, $currentLevel){

        $userval=User::where('userName', '=',$usercred)->get()->toArray();

        if($currentLevel > $userval[0]['maxLevelReached']){

            $affectedRows= User::where('userName', '=',$usercred)->update(array('maxLevelReached' => $currentLevel));
        }

        if($currentScore > $userval[0]['maxScore']){

            $affectedRows= User::where('userName', '=',$usercred)->update(array('maxScore' => $currentScore));
        }
        //return true;
  }
    

    
}
