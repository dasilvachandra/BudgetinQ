<?php

namespace App\Http\Controllers\Auth;
use App\Http\Controllers\Controller;
use Socialite;
use App\User; 
use Auth;
use Illuminate\Http\Request;
use App\JenisPengeluaran;
use App\JenisPendapatan;
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

        $id=DB::select('select ifnull(max(id),0)+1 as id from users;')[0]->id;
        DB::table('users')->insert(
            [
                [
                    'id' => $id,
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

        $kategoriJPG = ['Makan & Minum','Transportasi','Personal Care','Tak Terduga','Donasi','Lain-lain'];
        $this->kategoriJPG($kategoriJPG,1,$id);

        $transaksiTabung = ['Bank BRI','MyBank','Bank BCA'];
        $this->kategoriJPG($transaksiTabung,2,$id);
        $this->kategoriJPD($transaksiTabung,2,$id);

        $transaksiInvestasi = ['Reksadana','Emas','Bitcoin'];
        $this->kategoriJPG($transaksiInvestasi,3,$id);
        $this->kategoriJPD($transaksiInvestasi,3,$id);

        $transaksiUtang = ['Bank','Kredit'];
        $this->kategoriJPG($transaksiUtang,4,$id);
        $this->kategoriJPD($transaksiUtang,4,$id);

        $transaksiPiutang = ['Kakak','Adik','Saudara'];
        $this->kategoriJPG($transaksiPiutang,5,$id);
        $this->kategoriJPD($transaksiPiutang,5,$id);
        

        $kategoriJPD = ['Gaji Pokok','Tunjangan','Bonus'];
        $this->kategoriJPD($kategoriJPD,6,$id);

        $transaksiUsaha = ['Usaha Jualan'];
        $this->kategoriJPG($transaksiUsaha,7,$id);
        $this->kategoriJPD($transaksiUsaha,7,$id);


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

    public function kategoriJPG($data,$gcid,$id){
        $ktgjpg = new JenisPengeluaran;
        foreach ($data as $value) {
            $idktg = 'KTG_'.base_convert(microtime(false), 10, 36); // Buat ID
            $date = date("Y-m-d H:i:s"); // Buat Created Date & Update Date
            $ktgjpg->insertData([$idktg,$value,$gcid,$date,$date,$id,$this->randomRGB()]); // Insert
        }
    }

    public function kategoriJPD($data,$gcid,$id){
        $ktgjpd = new JenisPendapatan;
        foreach ($data as $value) {
            $idktg = 'KTG_'.base_convert(microtime(false), 10, 36); // Buat ID
            $date = date("Y-m-d H:i:s"); // Buat Created Date & Update Date
            $ktgjpd->insertData([$idktg,$value,$gcid,$date,$date,$id,$this->randomRGB()]); // Insert
        }
    }


}