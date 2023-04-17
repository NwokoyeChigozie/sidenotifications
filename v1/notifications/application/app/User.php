<?php

namespace App;

use App\Models\Countries;
use App\Models\BusinessProfile;
use Laravel\Passport\HasApiTokens;
use Illuminate\Auth\Authenticatable;
use Laravel\Lumen\Auth\Authorizable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Contracts\Auth\Authenticatable as AuthenticatableContract;
use Illuminate\Contracts\Auth\Access\Authorizable as AuthorizableContract;

class User extends Model
// class User extends Model implements AuthenticatableContract, AuthorizableContract
{


    // use Authenticatable, Authorizable, HasApiTokens, Notifiable, SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 'email',
    ];

    /**
     * The attributes excluded from the model's JSON form.
     *
     * @var array
     */

    protected $primaryKey = 'account_id';

    /**
     * Get Full Name Atribute of the User Model
     * 
     * @return string
     */
    public function getFullNameAttribute()
    {
        return $this->firstname . ' ' . $this->lastname;
    }

    /**
     * Route notifications for the mail channel.
     *
     * @param  \Illuminate\Notifications\Notification  $notification
     * @return string
     */
    public function routeNotificationForMail($notification)
    {
        return $this->email_address;
    }

    public function notifications()
    {
        return $this->morphMany(DatabaseMemberNotification::class, 'notifiable')->orderBy('created_at', 'desc');
    }

    function business_profile()
    {
        return BusinessProfile::where('account_id', $this->account_id)->first();
    }

    function business()
    {
        return $this->hasOne(BusinessProfile::class, 'account_id', 'account_id');
    }

    function profile()
    {
        return UserProfile::where('account_id', $this->account_id)->first();
    }

    function getCountry()
    {
        $country = '';

        // If this account type is an individual
        if ($this->account_type == 'individual') {
            // Make sure the user has a country in their profile
            $setCountry = $this->profile()->country ?? 'NG';

            if (!empty($setCountry)) {
                // Fetch Country using it's name or its code
                $fetchCountry = Countries::where('name', $setCountry)->orWhere('country_code', $setCountry)->first();

                // If the country exists set the var to the countries code
                if ($fetchCountry) {
                    $country = $fetchCountry->country_code;
                }
                // user has no country. set to empty
            } else {
                $country = 'NG';
            }
        } else if ($this->account_type == 'business') {
            // If this account type is a business
            $setCountry = $this->business_profile()->country ?? 'NG';

            if (!empty($setCountry)) {
                // Fetch Country using it's name or its code
                $fetchCountry = Countries::where('name', $setCountry)->orWhere('country_code', $setCountry)->first();

                // If the country exists set the var to the countries code
                if ($fetchCountry) {
                    $country = $fetchCountry->country_code;
                }
                // user has no country. set to empty
            } else {
                $country = 'NG';
            }
        }

        return $country;
    }
}
