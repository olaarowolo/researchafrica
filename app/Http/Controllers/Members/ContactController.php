<?php

namespace App\Http\Controllers\Members;

use App\Http\Controllers\Controller;
use App\Mail\ContactUsMail;
use App\Models\Setting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;

class ContactController extends Controller
{
    public function contact(Request $req)
    {
        $data = $req->validate([
            'name' => [
                'required',
                'string'
            ],
            'email' => [
                'required',
                'email'
            ],
            'phone' => [
                'nullable',
            ],
            'subject' => [
                'required',
                'string',
            ],
            'message' => [
                'required',
                'string'
            ],
        ]);
        $message = $this->strip_tags_content($data['message']);
        $admin_email = Setting::latest()->first();

        Mail::to($admin_email->website_email)->send(new ContactUsMail($data));

        return back()->with('success', 'Message Sent Successfully');
    }

    function strip_tags_content($text) {

        return preg_replace('@<(\w+)\b.*?>.*?</\1>@si', '', $text);

    }
}
