<?php

namespace App\Guards;

use Laravel\Sanctum\Guard;

class MongoDBGuard extends Guard
{
    /**
     * Authenticate a request based on the incoming token.
     */
    public function authenticate()
    {
        $token = $this->getTokenFromRequest();
        
        if (empty($token)) {
            return;
        }
        
        try {
            $model = $this->retrieveTokenModel()->findToken($token);
            
            if (!$model) {
                return;
            }

            $this->setUser($model->tokenable);
        } catch (\Throwable $e) {
            // Silently fail, don't crash
            return;
        }
    }
}
