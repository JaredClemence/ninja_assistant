<?php

namespace App\Http\Controllers\Ninja;

use App\Http\Controllers\Controller;
use App\DailyActivityLogEntry;
use App\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use App\Contact;
use Carbon\Carbon;
use App\Http\Controllers\Ninja\Service\NinjaLogEntryService;
use Illuminate\Pagination\Paginator;

class DailyActivityLogController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $user = Auth::user();
        $params = [
            'user_id'=>$user->id,
        ];
        $entries = DailyActivityLogEntry::where($params)->with(['user','contact'])->orderBy('created_at', 'desc')->simplePaginate(15);
        return view('ninja.activity.index', compact('entries'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create(Contact $contact, $action, NinjaLogEntryService $service)
    {
        $entry = $service->getOrCreateTodaysLogEntry($contact, $action);
        return redirect(route('edit_activity_log',['contact'=>$contact,'log'=>$entry]));
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
        $saved = $log->save();
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
