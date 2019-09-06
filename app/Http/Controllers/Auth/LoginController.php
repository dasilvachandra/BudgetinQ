<?php

namespace App\Http\Controllers\Auth;
use App\Http\Controllers\Controller;
use Socialite;
use App\User; 
use Auth;
use Illuminate\Http\Request;

class LoginController extends Controller
{
    /**
     * Redirect the user to the GitHub authentication page.
     *
     * @return \Illuminate\Http\Response
     */

    protected $redirectTo = '/dashboard/';

    public function redirectToProvider()
    {
        return Socialite::driver('google')->redirect();
    }

    /**
     * Obtain the user information from GitHub.
     *
     * @return \Illuminate\Http\Response
     */
    public function handleProviderCallback($provider)
    {
        $user = Socialite::driver('google')->user();
        $authUser = $this->findOrCreateUser($user,$provider);
        Auth::login($authUser,true);
        return redirect($this->redirectTo);
    }
    public function findOrCreateUser($user, $provider)
    {
        $data['nama'] = $user->name; 
        $data['email'] = $user->email;
        $data['time'] = date("d F, Y H:i:s");
        // $data['verification_code']  = str_random(20);
        $authUser = User::where('provider_id', $user->id)->first();
        if ($authUser){
            if ($authUser->picture==null) {
                DB::select('UPDATE users set picture=? where id=? ', [$user->avatar_original,$authUser->id]);
            }
            $data['nama'] = $user->name; 
            $data['email'] = $user->email;
            $data['time'] = date("d F, Y H:i:s");
            return $authUser;
        }

        // $data['nama'] = $user->name; 
        // $data['email'] = $user->email;
        // $data['time'] = date("d F, Y H:i:s");
        // $data="";
        // $jumlahusers = DB::select('select max(id)+1 as id from users;');
        // $iduserbaru=$jumlahusers[0]->id;
        // $data=[$iduserbaru,$user->name,$user->email,strtoupper($provider),$user->id,$user->avatar_original];
        // DB::select('INSERT INTO users (id, name, email, provider,provider_id,picture) VALUES (?, ?, ?, ?,?,?)', $data);
        // $authUser = User::where('provider_id', $user->id)->first();
        // return $authUser;
    }

    public function logout(Request $request) {
        Auth::logout();
        return redirect('/');
    }

    public function showLoginForm()
    {
        return redirect('/');
    }

}