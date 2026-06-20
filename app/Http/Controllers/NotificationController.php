<?php

namespace App\Http\Controllers;


use Illuminate\Http\Request;
use App\Models\User;
use App\Notifications\SystemNotification;
use Illuminate\Support\Facades\Notification;
use Illuminate\Notifications\DatabaseNotification;



class NotificationController extends Controller
{


/*
|--------------------------------------------------------------------------
| Notification Page
|--------------------------------------------------------------------------
*/


public function index()
{

     $notifications = DatabaseNotification::with('notifiable')
        ->latest()
        ->get();
        // dd($notifications);


    return view(
        'admin.notifications.index',
        compact('notifications')
    );


}
public function create()
{

    $users = User::all();


    return view(
        'admin.notifications.create',
        compact('users')
    );

}



/*
|--------------------------------------------------------------------------
| Send Notification
|--------------------------------------------------------------------------
*/


public function send(Request $request)
{


$request->validate([


    'target'=>'required',

    'title'=>'required',

    'message'=>'required'


]);





/*
|--------------------------------------------------------------------------
| Individual User
|--------------------------------------------------------------------------
*/

if($request->target=="user")
{


    $user = User::findOrFail(
        $request->user_id
    );


    $user->notify(

        new SystemNotification(

            $request->title,

            $request->message,

            $request->type

        )

    );


}



/*
|--------------------------------------------------------------------------
| All Users
|--------------------------------------------------------------------------
*/


if($request->target=="all")
{


    $users = User::all();



    Notification::send(

        $users,

        new SystemNotification(

            $request->title,

            $request->message,

            $request->type

        )

    );


}




/*
|--------------------------------------------------------------------------
| Role Wise Group
|--------------------------------------------------------------------------
*/


if($request->target=="role")
{


    $users = User::where(

        'role_id',

        $request->role_id

    )->get();



    Notification::send(

        $users,

        new SystemNotification(

            $request->title,

            $request->message,

            $request->type

        )

    );


}





return back()->with(

    'success',

    'Notification Sent Successfully'

);



}





/*
|--------------------------------------------------------------------------
| Notification History
|--------------------------------------------------------------------------
*/


public function history()
{


return auth()->user()
->notifications;


}


public function read($id)
{
    $n = auth()->user()
->notifications()
->find($id);
if($n)
{
    $n->markAsRead();
}


return back();

}
public function delete($id)
{
    auth()->user()
->notifications()
->where('id',$id)
->delete();



return back();

}




}
