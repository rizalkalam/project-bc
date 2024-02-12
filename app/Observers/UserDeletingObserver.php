<?php

namespace App\Observers;

use App\Models\User;
use App\Models\Backup;
use App\Models\Assignment;
use Illuminate\Support\Facades\DB;

class UserDeletingObserver
{
    protected function handleUserDeletion(User $user)
    {
        $employee = User::where('id', $user->id)->first();
        $head_office = User::where('id', $user->id)
            ->where('position', '=', 'Kepala KPPBC TMC Kudus')
            ->first();
        $ppk = Assignment::where('ppk', $user->id)->first();

        if ($head_office && $head_office->id !== null) {
            $assignmentByHeadOfc = Assignment::where('head_officer', $user->id)
                ->first();

            if ($assignmentByHeadOfc !== null) {
                if ($assignmentByHeadOfc->plt == 'kosong') {
                    $employee->delete();

                    DB::table('assignments')->where('head_officer', $user->id)->update([
                        "head_officer_status" => "non-active",
                        "head_officer" => 0,
                        "plt" => "plh",
                        "plh" => "Plh"
                    ]);
                    DB::table('backups')->where('head_officer', $user->id)->update([
                        "head_officer_status" => "non-active",
                        "head_officer" => 0,
                        "plt" => "plh",
                        "plh" => "Plh"
                    ]);

                    Assignment::where('user_id', $user->id)->delete();

                    Backup::where('user_id', $user->id)->update([
                        "employee_status" => "blank",
                        "availability_status" => "not_yet"
                    ]);

                    return;
                } else {
                    $employee->delete();

                    DB::table('assignments')->where('head_officer', $user->id)->update([
                        "head_officer_status" => "non-active",
                        "head_officer" => 0,
                    ]);
                    DB::table('backups')->where('head_officer', $user->id)->update([
                        "head_officer_status" => "non-active",
                        "head_officer" => 0,
                    ]);

                    Assignment::where('user_id', $user->id)->delete();

                    Backup::where('user_id', $user->id)->update([
                        "employee_status" => "blank",
                        "availability_status" => "not_yet"
                    ]);

                    return;
                }
            } else {
                $employee->delete();
                return;
            }
        } elseif ($ppk && $ppk->id !== null) {
            $employee->delete();

            DB::table('assignments')->where('ppk', $user->id)->update([
                "ppk_status" => "non-active",
                "ppk" => 0
            ]);
            DB::table('backups')->where('ppk', $user->id)->update([
                "ppk_status" => "non-active",
                "ppk" => 0,
            ]);

            Backup::where('user_id', $user->id)->update([
                "employee_status" => "blank",
                "availability_status" => "not_yet"
            ]);

            Assignment::where('user_id', $user->id)->delete();

            return;
        } else {
            $employee->delete();

            DB::table('assignments')->where('head_officer', $user->id)->update([
                "head_officer_status" => "non-active",
                "head_officer" => 0,
            ]);
            DB::table('backups')->where('head_officer', $user->id)->update([
                "head_officer_status" => "non-active",
                "head_officer" => 0,
            ]);

            Assignment::where('user_id', $user->id)->delete();

            Backup::where('user_id', $user->id)->update([
                "employee_status" => "blank",
                "availability_status" => "not_yet"
            ]);

            return;
        }
    }


    /**
     * Handle the User "created" event.
     */
    public function created(User $user): void
    {
        //
    }

    /**
     * Handle the User "updated" event.
     */
    public function updated(User $user): void
    {
        //
    }

    /**
     * Handle the User "deleted" event.
     */
    public function deleted(User $user): void
    {
        $this->handleUserDeletion($user);
    }

    /**
     * Handle the User "restored" event.
     */
    public function restored(User $user): void
    {
        //
    }

    /**
     * Handle the User "force deleted" event.
     */
    public function forceDeleted(User $user): void
    {
        //
    }
}
