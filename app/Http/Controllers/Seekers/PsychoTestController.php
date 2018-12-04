<?php

namespace App\Http\Controllers\Seekers;

use App\Provinces;
use App\PsychoTestInfo;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Twilio\Rest\Client;
use Twilio\Jwt\AccessToken;
use Twilio\Jwt\Grants\VideoGrant;
use App\Http\Controllers\Controller;

class PsychoTestController extends Controller
{
    protected $sid;
    protected $token;
    protected $key;
    protected $secret;

    public function __construct()
    {
        $this->middleware('psychoTest')->only('joinPsychoTestRoom');

        $this->sid = config('services.twilio.sid');
        $this->token = config('services.twilio.token');
        $this->key = config('services.twilio.key');
        $this->secret = config('services.twilio.secret');
    }

    public function joinPsychoTestRoom(Request $request)
    {
        $provinces = Provinces::all();

        $psychoTest = PsychoTestInfo::find(decrypt($request->psychoTest_id));

        $client = new Client($this->sid, $this->token);
        $exists = $client->video->rooms->read(['uniqueName' => $request->accessCode]);
        if (empty($exists)) {
            $client->video->rooms->create([
                'uniqueName' => $request->accessCode,
                'type' => 'group',
                'recordParticipantsOnConnect' => false
            ]);
        }

        $vacancy = $psychoTest->getVacancy;
        $userAgency = $vacancy->agencies->user;

        $strC = strtoupper("(candidate)");
        $strI = strtoupper("(interviewer)");
        $identity = Auth::guard('admin')->check() ? Auth::guard('admin')->user()->name . " " . $strI :
            Auth::user()->name . " " . $strC;

        $token = new AccessToken($this->sid, $this->key, $this->secret, 3600, $identity);

        $videoGrant = new VideoGrant();
        $videoGrant->setRoom($request->accessCode);

        $token->addGrant($videoGrant);

        return view('_seekers.psychoTest', ['accessToken' => $token->toJWT(), 'roomCode' => $request->accessCode,
            'vacancy' => $vacancy, 'userAgency' => $userAgency, 'provinces' => $provinces]);
    }
}