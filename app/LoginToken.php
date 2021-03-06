<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Mail;

class LoginToken extends Model
{
    //
    protected $fillable = ['user_id', 'token'];



    // by default this returns 'id', we're overriding it for 'token'
    // this is for the authenticate() method in AuthController 
    public function getRouteKeyName()
    {
        return 'token'; // static::where('token', $wildcard);

    }


    public static function generateTokenFor(User $user)
    {
        return static::create([
            'user_id' => $user->id,
            // 'token' => random_bytes(40) . "addedwatheveryouwant"
            // 'token' => bin2hex(random_bytes(10)) // 20 characters, only 0-9a-f
            'token' => substr(str_replace(['+', '/', '='], '', base64_encode(random_bytes(40))), 0, 32)  // 32 characters, without /=+
            
        ]);
    }


    public function send()
    {

        $url = url('/login/confirm', $this->token);

        // Mail::send
        
        Mail::raw(
             // body of email
            "Check this, just copy and paste this into the browser that you want to be logged into. But note, you will need cookies active and will need to re-login once your reset your cookies (which you should do every once in a while anyways, because fuck the bots.
            {$url}",
            function ($message) {
                $message->to($this->user->email)
                    ->subject('Login into site');
            }
        );
    }

    // have to declare how this table matches withother tables
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
