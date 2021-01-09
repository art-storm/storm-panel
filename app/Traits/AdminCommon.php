<?php

namespace App\Traits;

use Exception;

trait AdminCommon
{
    /**
     * @return \Illuminate\Http\Response
     */
    public function adminNoAccess()
    {
        return response()->view('admin.noaccess', [], 403);
    }

    public function user()
    {
        return auth()->user();
    }
}
