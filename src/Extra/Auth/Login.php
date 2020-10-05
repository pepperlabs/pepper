<?php

namespace Pepper\Authenticate;

use Illuminate\Validation\ValidationException;

class Login
{
    /**
    * Get a JWT via given credentials.
    *
    * @return \Illuminate\Http\JsonResponse
    */
    public function __invoke()
    {
        /**
         * @todo set username in config
         */
        $credentials = request(['email', 'password']);

        if (! $token = auth()->attempt($credentials)) {
            throw ValidationException::withMessages(['error' => 'Unauthorized']);
        }

        return $this->respondWithToken($token);
    }
}
