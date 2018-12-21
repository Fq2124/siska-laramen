<?php

namespace App\Http\Controllers\Admins;

use App\Accepting;
use App\Admin;
use App\Agencies;
use App\Blog;
use App\ConfirmAgency;
use App\Events\UserPartnershipEmail;
use App\Feedback;
use App\Http\Controllers\Controller;
use App\Partnership;
use App\PsychoTestInfo;
use App\QuizInfo;
use App\QuizResult;
use App\QuizType;
use App\Seekers;
use App\User;
use App\Vacancies;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;

class AdminController extends Controller
{
    protected $sid;
    protected $token;
    protected $key;
    protected $secret;

    public function __construct()
    {
        $this->sid = config('services.twilio.sid');
        $this->token = config('services.twilio.token');
        $this->key = config('services.twilio.key');
        $this->secret = config('services.twilio.secret');
    }

    public function index()
    {
        $newSeeker = Seekers::where('created_at', '>=', today()->subDays('3')->toDateTimeString())->count();
        $newApp = Accepting::where('created_at', '>=', today()->subDays('3')->toDateTimeString())->count();

        $newAgency = Agencies::where('created_at', '>=', today()->subDays('3')->toDateTimeString())->count();
        $newJobPost = ConfirmAgency::where('created_at', '>=', today()->subDays('3')->toDateTimeString())->count();

        $users = User::all();
        $agencies = Agencies::all();
        $seekers = Seekers::all();

        $blogs = Blog::all();

        return view('_admins.home-admin', compact('newSeeker', 'newApp', 'newAgency', 'newJobPost',
            'users', 'agencies', 'seekers', 'blogs'));
    }

    public function showInbox(Request $request)
    {
        $contacts = Feedback::orderByDesc('id')->get();

        if ($request->has("id")) {
            $findMessage = $request->id;
        } else {
            $findMessage = null;
        }

        return view('_admins.inbox', compact('contacts', 'findMessage'));
    }

    public function composeInbox(Request $request)
    {
        $this->validate($request, [
            'inbox_to' => 'required|string|email|max:255',
            'inbox_subject' => 'string|min:3',
            'inbox_message' => 'required'
        ]);
        $data = array(
            'email' => $request->inbox_to,
            'subject' => $request->inbox_subject,
            'bodymessage' => $request->inbox_message
        );
        Mail::send('emails.admins.admin-mail', $data, function ($message) use ($data) {
            $message->from(env('MAIL_USERNAME'));
            $message->to($data['email']);
            $message->subject($data['subject']);
        });

        return back()->with('success', 'Successfully send a message to ' . $data['email'] . '!');
    }

    public function deleteInbox(Request $request)
    {
        $contact = Feedback::find(decrypt($request->id));
        $contact->delete();

        return back()->with('success', 'Feedback from ' . $contact->name . ' <' . $contact->email . '> is successfully deleted!');
    }

    public function updateProfile(Request $request)
    {
        $admin = Admin::find(Auth::guard('admin')->user()->id);
        $this->validate($request, [
            'myAva' => 'image|mimes:jpg,jpeg,gif,png|max:2048',
        ]);
        if ($request->hasFile('myAva')) {
            $name = $request->file('myAva')->getClientOriginalName();
            if ($admin->ava != '' || $admin->ava != 'avatar.png') {
                Storage::delete('public/admins/' . $admin->ava);
            }
            $request->file('myAva')->storeAs('public/admins', $name);

        } else {
            $name = $admin->ava;
        }
        $admin->update([
            'ava' => $name,
            'name' => $request->myName
        ]);

        return back()->with('success', 'Successfully update your profile!');
    }

    public function updateAccount(Request $request)
    {
        $admin = Admin::find(Auth::guard('admin')->user()->id);

        if (!Hash::check($request->myPassword, $admin->password)) {
            return back()->with('error', 'Your current password is incorrect!');

        } else {
            if ($request->myNew_password != $request->myPassword_confirmation) {
                return back()->with('error', 'Your password confirmation doesn\'t match!');

            } else {
                $admin->update([
                    'email' => $request->myEmail,
                    'password' => bcrypt($request->myPassword)
                ]);
                return back()->with('success', 'Successfully update your account!');
            }
        }
    }

    public function showQuizInfo(Request $request)
    {
        $infos = QuizInfo::orderByDesc('id')->get();
        $types = QuizType::all();
        $vacancies = Vacancies::whereHas('getPlan', function ($query) {
            $query->where('isQuiz', true);
        })->where('isPost', true)->get();

        if ($request->has("vac_ids")) {
            $findVac = Vacancies::whereIn('id', explode(',', $request->vac_ids))
                ->whereDoesntHave('getQuizInfo')->get()->pluck('id');
        } else {
            $findVac = null;
        }

        return view('_admins.quiz-setup', compact('infos', 'types', 'vacancies', 'findVac'));
    }

    public function getQuizVacancyInfo($id)
    {
        return Vacancies::whereIn('id', explode(',', $id))->get();
    }

    public function createQuizInfo(Request $request)
    {
        $it = new \MultipleIterator();
        $it->attachIterator(new \ArrayIterator($request->vacancy_ids));
        $it->attachIterator(new \ArrayIterator($request->unique_code));
        $it->attachIterator(new \ArrayIterator($request->total_question));
        $it->attachIterator(new \ArrayIterator($request->time_limit));
        $it->attachIterator(new \ArrayIterator($request->question_ids));
        foreach ($it as $value) {
            QuizInfo::create([
                'vacancy_id' => $value[0],
                'unique_code' => $value[1],
                'total_question' => $value[2],
                'time_limit' => $value[3],
                'question_ids' => $value[4]
            ]);
        }
        $total = count($request->vacancy_ids);
        $str = $total > 1 ? 'quiz are' : 'quiz is';

        return redirect()->route('quiz.info')
            ->with('success', '' . $total . ' ' . $str . ' successfully created!')
            ->withInput($request->all())->with('vac_ids', implode(',', $request->vacancy_ids));
    }

    public function updateQuizInfo(Request $request)
    {
        $info = QuizInfo::find($request->id);
        $info->update([
            'vacancy_id' => $request->vacancy_id,
            'total_question' => $request->total_question,
            'question_ids' => $request->question_ids,
            'unique_code' => $request->unique_code,
            'time_limit' => $request->time_limit
        ]);

        return redirect()->route('quiz.info')
            ->with('success', 'Quiz #' . $info->unique_code . ' is successfully updated!');
    }

    public function deleteQuizInfo(Request $request)
    {
        $info = QuizInfo::find(decrypt($request->id));
        $info->delete();

        return redirect()->route('quiz.info')->with('success', 'Quiz #' . $info->unique_code . ' is successfully deleted!');
    }

    public function showPsychoTestInfo(Request $request)
    {
        $infos = PsychoTestInfo::orderByDesc('id')->get();

        $vacancies = Vacancies::whereHas('getPlan', function ($query) {
            $query->where('isPsychoTest', true);
        })->whereHas('getQuizInfo', function ($quiz) {
            $quiz->whereHas('getQuizResult');
        })->wherenotnull('psychoTestDate_start')->wherenotnull('psychoTestDate_end')
            ->whereDate('quizDate_end', '<=', today())->get();

        if ($request->has("vac_ids")) {
            $findVac = Vacancies::whereIn('id', explode(',', $request->vac_ids))
                ->whereDoesntHave('getPsychoTestInfo')->get()->pluck('id');
        } else {
            $findVac = null;
        }

        return view('_admins.psychoTest-setup', compact('infos', 'vacancies', 'findVac'));
    }

    public function getPsychoTestVacancyInfo($id)
    {
        $vacancies = Vacancies::whereIn('id', explode(',', $id))->get()->toArray();
        $i = 0;
        $arr = [];
        foreach ($vacancies as $vacancy) {
            $info = QuizInfo::where('vacancy_id', $vacancy['id'])->first();
            $candidate = QuizResult::where('quiz_id', $info->id)->where('isPassed', true)->orderByDesc('score')
                ->take($vacancy['psychoTest_applicant'])->get();

            $participant = array("participant" => $candidate);

            $arr[$i] = array_replace($vacancies[$i], $participant);
            $i = $i + 1;
        }
        return $arr;
    }

    public function createPsychoTestInfo(Request $request)
    {
        $it = new \MultipleIterator();
        $it->attachIterator(new \ArrayIterator($request->vacancy_ids));
        $it->attachIterator(new \ArrayIterator($request->room_codes));
        foreach ($it as $value) {
            PsychoTestInfo::create([
                'vacancy_id' => $value[0],
                'room_codes' => $value[1],
            ]);
        }
        $total = count($request->vacancy_ids);
        $str = $total > 1 ? 'psycho test are' : 'psycho test is';

        return redirect()->route('psychoTest.info')->with('success', '' . $total . ' ' . $str . ' successfully created!');
    }

    public function updatePsychoTestInfo(Request $request)
    {
        $info = PsychoTestInfo::find($request->id);
        $info->update([
            'vacancy_id' => $request->vacancy_id,
            'room_codes' => $request->room_codes,
        ]);

        return redirect()->route('psychoTest.info')
            ->with('success', 'Psycho Test for ' . $info->getVacancy->judul . ' is successfully updated!');
    }

    public function deletePsychoTestInfo(Request $request)
    {
        $info = PsychoTestInfo::find(decrypt($request->id));
        $info->delete();

        return redirect()->route('psychoTest.info')
            ->with('success', 'Psycho Test for ' . $info->getVacancy->judul . ' is successfully deleted!');
    }

    public function showPartnership()
    {
        $partnerships = Partnership::all();

        return view('_admins.partnership-list', compact('partnerships'));
    }

    public function approvePartnership(Request $request)
    {
        $partnership = Partnership::find($request->id);

        if ($partnership->status == false) {
            $partnership->update([
                'api_key' => $partnership->id . str_random(40),
                'api_secret' => $partnership->id . str_random(40),
                'api_expiry' => today()->addMonth(),
                'status' => true
            ]);
            event(new UserPartnershipEmail($partnership));

            return back()->with('success', 'Credentials API Key & API Secret for ' . $partnership->name . ' is ' .
                'successfully sent to ' . $partnership->email . '!');

        } else {
            return back()->with('error', 'API Key for ' . $partnership->name . ' is already exist!');
        }
    }
}
