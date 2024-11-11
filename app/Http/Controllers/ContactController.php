<?php

namespace App\Http\Controllers;

use App\Mail\ContactMail;
use App\Models\Setting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Twilio\Rest\Client;
use Illuminate\Support\Facades\Redirect;

class ContactController extends Controller
{
    public function submit(Request $request){
        $recaptchaSecret = '79b093ea-9f8e-48e0-b40e-fcb67ae6f2ec';
        $recaptchaResponse = $request->input('h-captcha-response');
        // dd($recaptchaResponse);

        // Verify reCAPTCHA response with hCaptcha API
        $response = file_get_contents("https://hcaptcha.com/siteverify?secret=$recaptchaSecret&response=$recaptchaResponse");
        $responseKeys = json_decode($response, true);
        dd("https://hcaptcha.com/siteverify?secret=$recaptchaSecret&response=$recaptchaResponse");

        if (intval($responseKeys['success']) !== 1) {
            return back()->withErrors(['captcha' => 'Please complete the CAPTCHA correctly.']);
        }

         $request->validate([
            'name' => 'required|min:4',
            'subject_mail' => 'required|min:4',
            'email' => 'required|email',
            'content' => 'required|min:10',
        ]);
        $contact_email = Setting::select('contact_mail')->where('id',2)->first();
        $status = Mail::to($contact_email->contact_mail)->send(new ContactMail($request->name, $request->email, $request->subject_mail, $request->content));
        // dd($status['0']);
        // Mail::to('mr.monirbd@gmail.com')->send(new ContactMail($request->name, $request->email, $request->subject_mail, $request->content));
        // Mail::to("zz@xx.com")->send(new ContactMail('nnn','e@z.com','bla bla','bla bla bla bla'));
        // return to_route('home')->with('message','Message sent sucessfully !');
        return Redirect::route('home')->with('success', 'Your message has been sent successfully. We\'ll get back to you soon.');
    }

    public function wpMessage(){
        $sid = 'your_account_sid';
        $token = 'your_auth_token';
        $twilio = new Client($sid, $token);

        $message = $twilio->messages->create(
            'whatsapp:+8801722931199', // Recipient's WhatsApp number
            [
                'from' => 'whatsapp:+YOUR_TWILIO_NUMBER',
                'body' => 'Hello from Laravel with Twilio!'
            ]
        );

    }
}
