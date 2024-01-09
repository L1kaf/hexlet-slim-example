<?php

namespace SlimExample;

class Validator
{
    public function validate(array $user)
    {
        $erorrs = [];
        if (strlen($user['name']) <= 4) {
            $erorrs['name'] = 'Nickname must be grater than 4 characters';
        }
        if (empty($user['email'])) {
            $erorrs['email'] = "Can't be blank";
        }

        return $erorrs;
    }
}