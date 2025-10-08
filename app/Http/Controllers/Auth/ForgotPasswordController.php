<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Password;

class ForgotPasswordController extends Controller
{
    public function showLinkRequestForm()
{
return view('auth.forgot-password');
}

public function sendResetLinkEmail(Request $request)
{
$request->validate(['vEmail' => 'required|email']);

$status = Password::broker('users')->sendResetLink(
['vEmail' => $request->vEmail]
);

return $status === Password::RESET_LINK_SENT
? back()->with(['status' => __($status)])
: back()->withErrors(['vEmail' => __($status)]);
}
}
