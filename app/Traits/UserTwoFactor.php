<?php

namespace App\Traits;

trait UserTwoFactor
{
    /**
     * Generete 2FA code
     */
    public function generateTwoFactorCode()
    {
        $this->timestamps = false;
        $this->two_factor_code = rand(100000, 999999);
        $this->two_factor_expires_at = now()->addMinutes(10);
        $this->unsetEventDispatcher();
        $this->save();
    }

    /**
     * Reset 2FA code
     */
    public function resetTwoFactorCode()
    {
        $this->timestamps = false;
        $this->two_factor_code = null;
        $this->two_factor_expires_at = null;
        $this->unsetEventDispatcher();
        $this->save();
    }
}
