<?php

namespace App\Http\Controllers\Auth;

use App\PartnerCredential;
use App\User;
use GuzzleHttp\Client;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;

class ActivationController extends Controller
{
    public function activate(Request $request)
    {
        $user = User::byActivationColumns($request->email, $request->verifyToken)->firstOrFail();

        $user->update([
            'status' => true,
            'verifyToken' => null
        ]);

        Auth::loginUsingId($user->id);

        if(Auth::user()->isSeeker()){
            $user->update(['ava' => 'seeker.png']);
            $user->seekers()->create(['user_id' => $user->id]);

            $seeker = User::find($user->id);
            if ($seeker != null) {
                $data = array('name' => $seeker->name, 'email' => $seeker->email, 'password' => $seeker->password);
                $partners = PartnerCredential::where('status', true)->where('isSync', true)
                    ->whereDate('api_expiry', '>=', today())->get();
                if (count($partners) > 0) {
                    foreach ($partners as $partner) {
                        $client = new Client([
                            'base_uri' => $partner->uri,
                            'defaults' => [
                                'exceptions' => false
                            ]
                        ]);
                        $client->post($partner->uri . '/api/SISKA/seekers/create', [
                            'form_params' => [
                                'key' => $partner->api_key,
                                'secret' => $partner->api_secret,
                                'seeker' => $data,
                            ]
                        ]);
                    }
                }
            }

            return back()->with('activated', 'You`re now signed in as Job Seeker.');

        } elseif (Auth::user()->isAgency()) {
            $user->update(['ava' => 'agency.png']);
            $user->agencies()->create(['user_id' => $user->id]);
            return back()->with('activated', 'You`re now signed in as Job Agency.');
        }
    }
}
