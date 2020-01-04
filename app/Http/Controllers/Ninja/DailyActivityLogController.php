<?php

namespace App\Http\Controllers\Ninja;

use App\Http\Controllers\Controller;
use App\DailyActivityLogEntry;
use App\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use App\Contact;
use Carbon\Carbon;

class DailyActivityLogController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create(Contact $contact, $action)
    {
        $user = Auth::user();
        $params = [
            'user_id'=>$user->id,
            'contact_id'=>$contact->id,
            'action'=>$action
        ];
        $entry = DailyActivityLogEntry::whereDate('created_at', Carbon::today())->where($params)->get()->first();
        if( !$entry ){
            $entry = DailyActivityLogEntry::create($params);
        }
        return $this->edit($contact, $entry);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\DailyActivityLogEntry  $dailyActivityLogEntry
     * @return \Illuminate\Http\Response
     */
    public function show(DailyActivityLogEntry $dailyActivityLogEntry)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\DailyActivityLogEntry  $dailyActivityLogEntry
     * @return \Illuminate\Http\Response
     */
    public function edit(Contact $contact, DailyActivityLogEntry $log)
    {
        $last = DailyActivityLogEntry::where([
            ['contact_id','=',$contact->id],
            ['id','<>',$log->id]
        ])->latest()->get()->first();
        return view('ninja.activity.edit', compact('contact','log','last'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\DailyActivityLogEntry  $dailyActivityLogEntry
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Contact $contact, DailyActivityLogEntry $log)
    {
        $updates = $request->all('family','occupation','recreation','dreams');
        $log->fill($updates);
        $log->save();
        return redirect(route('daily_call'));
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\DailyActivityLogEntry  $dailyActivityLogEntry
     * @return \Illuminate\Http\Response
     */
    public function destroy(DailyActivityLogEntry $dailyActivityLogEntry)
    {
        //
    }
}
