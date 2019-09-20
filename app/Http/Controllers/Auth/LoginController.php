<?php

namespace App\Http\Controllers\Auth;
use App\Http\Controllers\Controller;
use Socialite;
use App\User; 
use Auth;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

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

        $data['nama'] = $user->name; 
        $data['email'] = $user->email;
        $data['time'] = date("d F, Y H:i:s");
        $data="";
        $iduserbaru=DB::select('select ifnull(max(id),0)+1 as id from users;')[0]->id;
        DB::table('users')->insert(
            [
                [
                    'id' => $iduserbaru,
                    'name' => $user->name,
                    'email' => $user->email,
                    'provider' => strtoupper($provider),
                    'provider_id' => $user->id,
                    'picture' => $user->avatar,
                    'created_at' => date("Y-m-d H:i:s"),
                    'updated_at' => date("Y-m-d H:i:s")
                ],

            ]
        );

        DB::table('jenis_pengeluaran')->insert(
            [
                [
                    'id_jenis_pengeluaran' => 'KTG_'.uniqid(),
                    'jenis_pengeluaran' => 'Makan & Minum',
                    'color' => $this->randomRGB(),
                    'group_category_id' => 1,
                    'id' => $iduserbaru,
                    'created_at' => date("Y-m-d H:i:s"),
                    'updated_at' => date("Y-m-d H:i:s"),
                ],
                [
                    'id_jenis_pengeluaran' => 'KTG_'.uniqid(),
                    'jenis_pengeluaran' => 'Transportasi',
                    'color' => $this->randomRGB(),
                    'group_category_id' => 1,
                    'id' => $iduserbaru,
                    'created_at' => date("Y-m-d H:i:s"),
                    'updated_at' => date("Y-m-d H:i:s"),
                ],
                [
                    'id_jenis_pengeluaran' => 'KTG_'.uniqid(),
                    'jenis_pengeluaran' => 'Personal Care',
                    'color' => $this->randomRGB(),
                    'group_category_id' => 1,
                    'id' => $iduserbaru,
                    'created_at' => date("Y-m-d H:i:s"),
                    'updated_at' => date("Y-m-d H:i:s"),
                ],
                [
                    'id_jenis_pengeluaran' => 'KTG_'.uniqid(),
                    'jenis_pengeluaran' => 'Tak Terduga',
                    'color' => $this->randomRGB(),
                    'group_category_id' => 1,
                    'id' => $iduserbaru,
                    'created_at' => date("Y-m-d H:i:s"),
                    'updated_at' => date("Y-m-d H:i:s"),
                ],
                [
                    'id_jenis_pengeluaran' => 'KTG_'.uniqid(),
                    'jenis_pengeluaran' => 'Donasi',
                    'color' => $this->randomRGB(),
                    'group_category_id' => 1,
                    'id' => $iduserbaru,
                    'created_at' => date("Y-m-d H:i:s"),
                    'updated_at' => date("Y-m-d H:i:s"),
                ],
                [
                    'id_jenis_pengeluaran' => 'KTG_'.uniqid(),
                    'jenis_pengeluaran' => 'Lain-lain',
                    'color' => $this->randomRGB(),
                    'group_category_id' => 1,
                    'id' => $iduserbaru,
                    'created_at' => date("Y-m-d H:i:s"),
                    'updated_at' => date("Y-m-d H:i:s"),
                ],
                [
                    'id_jenis_pengeluaran' => 'KTG_'.uniqid(),
                    'jenis_pengeluaran' => 'BANK BRI',
                    'color' => $this->randomRGB(),
                    'group_category_id' => 2,
                    'id' => $iduserbaru,
                    'created_at' => date("Y-m-d H:i:s"),
                    'updated_at' => date("Y-m-d H:i:s"),
                ],
                [
                    'id_jenis_pengeluaran' => 'KTG_'.uniqid(),
                    'jenis_pengeluaran' => 'Reksadana',
                    'color' => $this->randomRGB(),
                    'group_category_id' => 3,
                    'id' => $iduserbaru,
                    'created_at' => date("Y-m-d H:i:s"),
                    'updated_at' => date("Y-m-d H:i:s"),
                ],
                [
                    'id_jenis_pengeluaran' => 'KTG_'.uniqid(),
                    'jenis_pengeluaran' => 'Emas',
                    'color' => $this->randomRGB(),
                    'group_category_id' => 3,
                    'id' => $iduserbaru,
                    'created_at' => date("Y-m-d H:i:s"),
                    'updated_at' => date("Y-m-d H:i:s"),
                ],
                [
                    'id_jenis_pengeluaran' => 'KTG_'.uniqid(),
                    'jenis_pengeluaran' => 'Bitcoin',
                    'color' => $this->randomRGB(),
                    'group_category_id' => 3,
                    'id' => $iduserbaru,
                    'created_at' => date("Y-m-d H:i:s"),
                    'updated_at' => date("Y-m-d H:i:s"),
                ],
                
            ]
        );




        $authUser = User::where('provider_id', $user->id)->first();
        return $authUser;
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