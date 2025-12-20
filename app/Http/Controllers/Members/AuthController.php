<?php

namespace App\Http\Controllers\Members;

use App\Models\Member;
use App\Models\Country;
use App\Models\MemberRole;
use App\Models\MemberType;
use App\Mail\ResetPassword;
use App\Models\EmailVerify;
use Illuminate\Http\Request;
use App\Mail\EmailVerification;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use Illuminate\Http\RedirectResponse;
use App\Http\Requests\Member\StoreRegisterRequest;
use App\Models\ResetPassword as ModelResetPassword;

class AuthController extends Controller
{
    public function registerPage()
    {

        if(auth('member')->user()){
            return back();
        };

        $title = Member::TITLE_SELECT;
        $countries = Country::get(['name', 'id']);
        $member_roles = MemberRole::pluck('title', 'id');
        $member_types = MemberType::whereIn('id', [1, 4])->pluck('name', 'id');


        return view('member.auth.register', compact('title', 'countries', 'member_roles', 'member_types'));

    }


    public function register(StoreRegisterRequest $request)
    {

        // dd($request->all());

        $input = $request->validated();
        $member = Member::create($input);

        $token = str_pad(random_int(0, 999999), 6, '0', STR_PAD_LEFT);

        EmailVerify::create([
            'token' => $token,
            'member_id' => $member->id,
        ]);


        Mail::to($member->email_address)->send(new EmailVerification($member, $token));

        return redirect('/email-verify')->with('success', 'Please check your email for the verification code');

    }



    public function loginPage()
    {
        if(auth('member')->user()){
            return back();
        };

        return view('member.auth.login');

    }



    public function authenticate(Request $request): RedirectResponse
    {
        $credentials = $request->validate([
            'email_address' => ['required', 'email'],
            'password' => ['required'],
        ]);

        if (Auth::guard('member')->attempt($credentials)) {
            $request->session()->regenerate();
            $member = auth('member');


            if(false){

                $token = str_pad(random_int(0, 999999), 6, '0', STR_PAD_LEFT);

                EmailVerify::create([
                    'token' => $token,
                    'member_id' => $member->id(),
                ]);

                Mail::to($member->user()->email_address)->send(new EmailVerification($member->user(), $token));

                $member->logout();

                return redirect('/email-verify')->with('error', 'Verify code sent to your mail, please check to verify');
            }

            // dd('NotVerify');


            return redirect('/profile');
        }

        return back()->withErrors([
            'email' => 'The provided credentials do not match our records.',
        ])->onlyInput('email');
    }





    public function verify(Request $request)
    {

        $token = $request->validate([
            'token' => [
                'required',
            ]
        ]);

        $verify = EmailVerify::with('member')->where('token', $token)->first();

        if($verify){
            $verify->member->update([
                'email_verified' => 1,
                'email_verified_at' => now(),
                'registration_via' => 'email',
            ]);

            $verify->delete();

            return to_route('member.login')->with('success', 'Email Verification Completed');
        }else{
            return back()->withErrors([
                'message' => [
                    'Incorrect Verification code'
                ],
            ]);
        }
    }


    public function forgetPassword()
    {
        return view('member.auth.password.forget-password');
    }


    public function logout(Request $request)
    {
        Auth::guard('member')->logout();
        return to_route('home');
    }

    public function emailPassword(Request $request)
    {
        $validated = $request->validate([
            'email_address' => [
                'required',
                'email',
            ]
        ]);

        $member = Member::where('email_address', $request->email_address)->first();

        if(!$member){
            return back()->with('error', 'Email does not exist. Please use the email you use to register');
        }


        $token = str_pad(random_int(0, 999999), 6, '0', STR_PAD_LEFT);
        $hash = sha1($token);

        ModelResetPassword::create([
            'token' => $token,
            'hash' => $hash,
            'member_id' => $member->id,
        ]);


        Mail::to($member->email_address)->send(new ResetPassword($member,$token,$hash));


        return to_route('home')->with('success', 'Reset Link Sent to your E-mail');


    }

    public function resetPassword($hash)
    {
        return view('member.auth.password.reset-password', compact('hash'));
    }

    public function resetPasswordSubmit(Request $request, $hash){
        $validated = $request->validate([
            'password' => [
                'required',
                'confirmed',
                'min:8'
            ]
        ]);


        $resetPassword = ModelResetPassword::with('member')->where('hash', $hash)->first();

        if($resetPassword){
            $resetPassword->member->update($validated);
            $resetPassword->delete();
            return to_route('home')->with('success', 'Your password has been updated successfully');
        }

        return back()->with('error', 'Something Went Wrong. Please try again later');
    }

}
