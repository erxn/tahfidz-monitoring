<?php

namespace App\Model\User;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Spatie\Permission\Traits\HasRoles;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Hash;
use Illuminate\Contracts\Auth\Authenticatable as AuthenticatableContract;
use Illuminate\Contracts\Auth\Access\Authorizable as AuthorizableContract;

class User extends Authenticatable
{
    use HasRoles;

    protected $table = 'tbl_user';
    protected $guard_name = 'web';

    const USER_STATUS_ACTIVE = 10;
    const USER_STATUS_NOT_ACTIVE = 20;

    const ACCOUNT_TYPE_CREATOR = 10;
    const ACCOUNT_TYPE_USER = 20;
    const ACCOUNT_TYPE_PARENT = 30;
    const ACCOUNT_TYPE_TEACHER = 40;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'username','address', 'full_name','account_type','password','status'
    ];


    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $attributes = [
        'account_type' => self::ACCOUNT_TYPE_PARENT,
        'status' => self::USER_STATUS_ACTIVE
    ];

    public static $rules = [
        'username' => 'required | unique',
        'address' => 'string',
        'full_name' => 'required | string',
        'account_type' => 'required | integer',
        'status' => 'required | integer'
    ];

     /**
     * 
     */
     public static function getUser()
     {
        return self::where('status',self::USER_STATUS_ACTIVE)->whereNotIn('account_type', [User::ACCOUNT_TYPE_CREATOR,User::ACCOUNT_TYPE_PARENT])->get();
     }

     /**
     * 
     */
     public static function getTeacher($search=null)
     {
        return self::where('status',self::USER_STATUS_ACTIVE)->where('account_type', User::ACCOUNT_TYPE_TEACHER)->where('full_name', 'like', '%'.$search.'%')->get();
     }

     /**
     * 
     */
     public static function getParent($search=null)
     {
        return self::where('status',self::USER_STATUS_ACTIVE)->where('account_type', User::ACCOUNT_TYPE_PARENT)->where('full_name', 'like', '%'.$search.'%')->get();
     }

    /**
     * The attributes excluded from the model's JSON form.
     *
     * @var array
     */
    protected $hidden = [
        'password'
    ];

    /**
     * @var array
     */
    public static function userByUsername($username)
    {
        $data = static::where('username', $username)->where('status', static::USER_STATUS_ACTIVE)->first();
        return $data;
    } 

    /**
     * @var Bol
     */
    public static function checkIfParent($id)
    {
        $data = static::where(['account_type' => static::ACCOUNT_TYPE_PARENT,'id'=>$id])->first();

        if($data != null)
        {
            return true;
        }
        else
        {
            return false;
        }
    }

    /**
     * 
     */
    public static function getAccountMeaning($acount)
    {
        switch ($acount) {
            case static::ACCOUNT_TYPE_CREATOR:
               return 'Developer';
            case static::ACCOUNT_TYPE_PARENT:
               return 'Orangtua';
            case static::ACCOUNT_TYPE_TEACHER:
               return 'Guru';
            case static::ACCOUNT_TYPE_USER:
               return 'User Umum';
            default:
                return '';
        }
    }


    /**
     * @var Bol
     */
    public static function checkIfTeacher($id)
    {
        $data = static::where(['account_type' => static::ACCOUNT_TYPE_TEACHER,'id'=>$id])->first();

        if($data != null)
        {
            return true;
        }
        else
        {
            return false;
        }
    }

    public function getClass()
    {
        return $this->belongsTo('App\Model\Class\Class');
    }

    public function getSiswa()
    {
        return $this->belongsTo('App\Model\Siswa\Siswa');
    }
}