<?php

namespace App\Http\Controllers;

use App\Contact;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Clemence\PhoneNumber;
use App\User;

class ContactController extends Controller {

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index() {
        $all = $this->user()->contacts->sortBy('name');
        return view('contacts.index', compact('all'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create() {
        $user = $this->user();
        $contact = Contact::make(['name'=>'', 'note'=>'','address'=>'','email'=>'']);
        $user->contacts()->save($contact);
        return $this->edit($contact);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request) {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Contact  $contact
     * @return \Illuminate\Http\Response
     */
    public function show(Contact $contact) {
        return view('contacts.show', compact('contact'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Contact  $contact
     * @return \Illuminate\Http\Response
     */
    public function edit(Contact $contact) {
        return view('contacts.edit', compact('contact'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Contact  $contact
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Contact $contact) {
        $contactDetails = array_map(function($string){ return trim($string); }, $request->all(['name', 'note', 'email', 'address']));
        $contact->fill($contactDetails);
        $contact->save();
        $phonesCount = $contact->phones->count();
        for ($i = 1; $i < $phonesCount + 2; $i++) {
            $data = $request->all(["phone_id_$i", "phone_name_$i", "phone_number_$i"]);
            $id = $data["phone_id_$i"];
            $name = trim($data["phone_name_$i"]);
            $number = trim($data["phone_number_$i"]);
            if (is_numeric($id)) {
                $this->updateContactPhone($contact, $id, $name, $number);
            } else if ( $id == "new" && ($name!='' || $number!='')) {
                $this->createNewPhone($contact, $name, $number);
            }
        }
        return redirect('/');
    }
    
    private function updateContactPhone(Contact $contact, $id, $name, $number){
        $phone = PhoneNumber::find($id);
        if( $phone){
            $phone->fill(compact('name','number'));
            $phone->save();
        }
    }
    private function createNewPhone(Contact $contact, $name, $number){
        $phone = PhoneNumber::make(compact('name','number'));
        $contact->phones()->save($phone);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Contact  $contact
     * @return \Illuminate\Http\Response
     */
    public function destroy(Contact $contact) {
        $data = $contact->all();
        $contact->delete();
        return back();
    }

    public function user(): User {
        return Auth::user();
    }

}
