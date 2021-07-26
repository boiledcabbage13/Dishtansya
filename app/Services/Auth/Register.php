<?php

namespace App\Services\Auth;

use App\Models\User;

class Register {
    protected $user;

    public function __construct(User $user) {
        $this->user = $user;
    }

    /**
     * @param $data [email, password]
     * 
     * @return User
     */

    public function execute(Array $data) {
        $user = $this->user->create($data);
        
        dispatch(new \App\Jobs\SendEmail($user));

        return $user;
    }
}