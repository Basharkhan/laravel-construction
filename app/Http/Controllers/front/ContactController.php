<?php

namespace App\Http\Controllers\front;

use App\Http\Controllers\Controller;
use App\Mail\ContactEmail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Validator;

class ContactController extends Controller {
    public function sendMail( Request $request ) {
        $validator = Validator::make( $request->all(), [
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'message' => 'required|string|max:500',
        ] );

        if ( $validator->fails() ) {
            return response()->json( [
                'status' => false,
                'message' => $validator->errors(),
            ], 422 );
        }

        $mailData = [
            'name' => $request->name,
            'email' => $request->email,
            'phone' => $request->phone,
            'subject' => $request->subject,
            'message' => $request->message,
        ];

        Mail::to( 'admin@example.com' )->send( new ContactEmail( $mailData ) );

        return response()->json( [
            'status' => true,
            'message' => 'Thanks for contacting us.'
        ] );
    }
}
