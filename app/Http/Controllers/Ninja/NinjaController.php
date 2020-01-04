<?php

namespace App\Http\Controllers\Ninja;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\User;
use App\Http\Controllers\Ninja\Daily;
use App\Contact;

class NinjaController extends Controller {

    private $user;

    /** @var Daily */
    private $daily;

    public function loadUser() {
        if ($this->user == null) {
            $this->user = Auth::user();
        }
    }

    public function showDaily() {
        $this->loadUser();
        $this->makeDaily();
        return view('ninja.daily',['daily'=>$this->daily]);
    }

    public function refreshDaily() {
        $this->loadUser();
        $this->makeDaily();
        $this->daily->fresh();
        return back();
    }

    public function deactivateContact(Contact $contact) {
        $contact->active = false;
        $this->skipContact($contact);
        return back();
    }

    public function skipContact(Contact $contact) {
        $this->loadUser();
        $this->makeDaily();
        $this->daily->replaceCaller($contact);
        $this->daily->replaceMailer($contact);
        $contact->save();
        return back();
    }

    private function makeDaily() {
        if ($this->daily == null) {
            $daily = new Daily($this->user);
            $this->daily = $daily;
        }
    }

}
