<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Laravel\Socialite\Facades\Socialite;
use Auth;
class OneloginController extends Controller
{
    //
    public function oneloginRedirect()
    {
        return Socialite::driver('onelogin')->redirect();
    }

    public function callbackOnelogin()
    {
        try {
    
            $user = Socialite::driver('onelogin')->user();
            $isUser = User::where('email', $user->email)->first();
     
            if($isUser){
                Auth::login($isUser);
                return redirect('/dashboard');
            }else{
                $createUser = User::create([
                    'name' => $user->name,
                    'email' => $user->email,
                    'token' => $user->token
                ]);
    
                Auth::login($createUser);
                return redirect('/dashboard');
            }
    
        } catch (Exception $exception) {
            dd($exception->getMessage());
        }
    }

}
