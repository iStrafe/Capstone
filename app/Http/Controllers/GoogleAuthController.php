<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Laravel\Socialite\Facades\Socialite;

class GoogleAuthController extends Controller
{
     public function redirect(){
        return Socialite::driver('google')->redirect();
    }

    public function callbackGoogle(){
        
        
        $google_user = Socialite::driver('google')->user();

        $findUser = User::where('google_id', $google_user->id)->first();

        if($findUser){
            Auth::login($findUser);
        }
        else{
        
            $user = User::updateOrCreate([
                'name' => $google_user->getName(),
                'email' => $google_user->getEmail(),
                'google_id' => $google_user->getId()
            ]);

            Auth::login($user);
            return redirect()->route('profile.edit');
            
        }
        
        return redirect()->route('dashboard');

    }
}
