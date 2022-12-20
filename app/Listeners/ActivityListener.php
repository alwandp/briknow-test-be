<?php

namespace App\Listeners;

use App\Achievement;
use App\Activity;
use App\ActivityUser;
use App\Events\TriggerActivityEvent;
use App\Level;
use App\User;
use App\UserAchievement;
use App\UserLevel;
use Carbon\Carbon;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class ActivityListener implements ShouldQueue
{
    public function handle($event)
    {
        // baka di panggil tiap menit pake
        // php artisan queueu:work > storage/logs/jobs.log --stop-when-empty
        // initializr
        $activity_id    = $event->activity_id;
        $datauser       = User::where('personal_number',$event->personal_number)->first();
        // cek activity
        $activity       = Activity::where('id', $activity_id)->first();

        // pengecheckan khusus login Event
        // activity
        $advance_process    =   1;
        if ($activity_id == 6) {
            $cek_sudah_login_hariini      =   ActivityUser::where('personal_number',$datauser->personal_number)->where('activity_id',$activity_id)->whereDate('created_at',Carbon::today())->count();
            if ($cek_sudah_login_hariini > 0) {
                $advance_process    =   0;
            }else{
                $advance_process    =   1;
            }
        }

        if (isset($activity->name) && $advance_process == 1) {
            // kalkulasi xp
            $xp         = $datauser->xp;
            $xpakhir    = (int)$xp + (int)$activity->xp;
            // $xpakhir    = (int)$xp + (int)10;

            // cek apakah naik level ??
            $ceklevelingbefore = Level::where('xp', '<=',$xp)->orderby('xp','desc')->first();
            $ceklevelingafter  = Level::where('xp', '<=',$xpakhir)->orderby('xp','desc')->first();
            if ($ceklevelingbefore->id <> $ceklevelingafter->id) {
                // create user_level
                $new2['personal_number']  = $datauser->personal_number;
                $new2['level_before']     = $ceklevelingbefore->id;
                $new2['level_after']      = $ceklevelingafter->id;
                $new2['congrats_view']    = 0;
                UserLevel::create($new2);
            }

            // update xp user
            $new0['xp']      = $xpakhir;
            $updatexp        = User::where('personal_number',$datauser->personal_number)->update($new0);

            // create user_activity
            $new1['activity_id']      = $activity_id;
            $new1['personal_number']  = $datauser->personal_number;
            $new1['xp_before']        = $xp;
            $new1['xp_after']         = $xpakhir;
            ActivityUser::create($new1);

            // count all activity terkait
            $countactivity      =   ActivityUser::where('personal_number',$datauser->personal_number)->where('activity_id',$activity_id)->count();

            // checking all achivement in above cpunt all activity terkait
            $achievement        =   Achievement::where('activity_id',$activity_id)->where('value','<=',$countactivity)->orderby('value','asc')->get();
            foreach ($achievement as $item) {
                // existing in history user achievement ?
                $cekachievement       = UserAchievement::where('achievements_id',$item->id)->first();
                // jika tidak ada maka create kan lah
                if (!isset($cekachievement->achievements_id)) {
                    $new3['personal_number']  = $datauser->personal_number;
                    $new3['achievements_id']  = $item->id;
                    $new3['congrats_view']    = 0;
                    UserAchievement::create($new3);

                    // Event Activity
                    event(new TriggerActivityEvent(7, $datauser->personal_number));
                }
            }
        }
    }
}
