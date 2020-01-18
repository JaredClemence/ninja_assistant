<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use App\User;
use Carbon\Carbon;

/**
 * @property Carbon date
 * @property int user_id
 * @property string call_list
 * @property string mail_list
 */
class DailyData extends Model
{
    static public function initToday(User $user): DailyData{
        $date = Carbon::now();
        /* @var $date Carbon */
        $date->setTimezone('America/Los_Angeles');
        $dateString = $date->format('Y-m-d');
        $daily =  DailyData::firstOrCreate([
            'user_id'=>$user->id,
            'date'=>$dateString
        ]);
        return $daily;
    }
    
    protected $fillable = [
        'date',
        'user_id',
    ];
    
    protected $attributes = [
        'list_to_call'=>'',
        'list_to_mail'=>''
    ];
    
    public function setCallList(&$list){
        $string = $this->makeIterableModelsIntoString($list);
        $this->list_to_call = $string;
    }
    public function readCallList(){
        $data = $this->list_to_call;
        return $this->explodeData($data);
    }
    
    private function explodeData(&$string){
        return array_filter( explode("|", $string), function($value){ return trim($value)!==""; } );
    }
    
    public function setMailList(&$list){
        $string = $this->makeIterableModelsIntoString($list);
        $this->list_to_mail = $string;
    }
    public function readMailList(){
        $string = $this->list_to_mail;
        return $this->explodeData($string);
    }

    private function makeIterableModelsIntoString($list) {
        $list = clone $list;
        $data = [];
        foreach( $list as $item ){
            $id = $item->id;
            $data[] = $id;
        }
        $string = implode("|",$data);
        return $string;
    }

}
