<?php

namespace App\Http\Controllers\Ninja;

use App\Http\Controllers\Controller;
use App\DailyActivityLogEntry;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use App\Contact;
use App\Http\Controllers\Ninja\Service\NinjaLogEntryService;

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
    public function showCreate(Contact $contact, $action, NinjaLogEntryService $logService)
    {
        $entry = $logService->getTodaysLogEntry($contact, $action);
        if( $entry ){
            return redirect(route('edit_activity_log',['contact'=>$contact,'log'=>$entry]));
        }else{
            $log = $logService->make();
            $last = $logService->getMostRecentLogEntryBeforeCurrent( $contact, $log );
            return view('ninja.activity.create', compact('contact','log','last','action'));
        }
    }
    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create(Request $request, Contact $contact, $action, NinjaLogEntryService $logService)
    {
        $entry = $logService->getOrCreateTodaysLogEntry($contact, $action);
        $entry->fill($request->only(['family','occupation','recreation','dreams']));
        $entry->save();
        return redirect(route('daily_call'));
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
    public function edit(Contact $contact, DailyActivityLogEntry $log, NinjaLogEntryService $service)
    {
        $last = $service->getMostRecentLogEntryBeforeCurrent( $contact, $log );
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
