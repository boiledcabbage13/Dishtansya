<?php

namespace App\Services\Auth;

use Auth;
use App\Models\User;
use Exception;

class Login {
    protected $user;

    public function __construct(User $user) {
        $this->user = $user;
    }

    /**
     * @param $email
     * @param $password
     * 
     * @return User
     */

    public function execute($email, $password) {
        $checkUser = $this->user->where('email', $email)->first();

        if (\Carbon\Carbon::now()->lt($checkUser->locked_until)) {
            throw new Exception("Your account is locked please wait after 5 minutes.", 1);
        }

        $authenticate = Auth::attempt([
            'email' => $email,
            'password' => $password
        ]);

        if($authenticate){ 
            /**
             * Reset lock period after success attempt
             */
            $checkUser->locked_until = null;
            $checkUser->failed_count = 0;

            $checkUser->save();

            $user = Auth::user();
 
            $user->access_token =  $user->createToken('Distansya')->accessToken; 

            return $user;
        }

        if ($checkUser) {
            $numberOfTries = 5;

            $checkUser->failed_count++;
            $checkUser->locked_until = null;

            if ($checkUser->failed_count >= $numberOfTries) {
                $checkUser->locked_until = \Carbon\Carbon::now()->addMinute(5);
                $checkUser->failed_count = 0;
            }

            $checkUser->save();
        }

        throw new Exception('Invalid credentials.', 1);
    }
}