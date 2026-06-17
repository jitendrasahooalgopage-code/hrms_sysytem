<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\EmployeeAsset;
use App\Models\Employee;
use App\Models\AssetRequest;
use Illuminate\Support\Facades\Mail;
use App\Mail\AssetRequestSubmitted;

class AssetRequestController extends Controller
{
    public function create(EmployeeAsset $asset)
{
    return view(
        'employee.assets_requests.create',
        compact('asset')
    );
}

public function store(Request $request)
{
    $request->validate([
        'employee_asset_id' => 'required',
        'request_type'      => 'required',
        'subject'           => 'required',
        'message'           => 'required',
        'photos.*'          => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
    ]);

    $employee = Employee::where(
        'email',
        auth()->user()->email
    )->first();

    $photos = [];

    if ($request->hasFile('photos')) {

        foreach ($request->file('photos') as $file) {

            $path = $file->store(
                'asset_requests',
                'public'
            );

            $photos[] = $path;
        }
    }

    $assetRequest = AssetRequest::create([

        'employee_id'       => $employee->id,

        'employee_asset_id' => $request->employee_asset_id,

        'request_type'      => $request->request_type,

        'subject'           => $request->subject,

        'message'           => $request->message,

        'photos'            => $photos,

        'status'            => 'Pending',

    ]);

    // Send Mail
    Mail::to('jitendrasahooalgopage@gmail.com')
        ->send(
            new AssetRequestSubmitted(
                $assetRequest
            )
        );

    return redirect()
        ->route('employee.assets')
        ->with(
            'success',
            'Request Submitted Successfully'
        );
}

public function myRequests(EmployeeAsset $asset)
{
    $requests = AssetRequest::where(
        'employee_asset_id',
        $asset->id
    )
    ->latest()
    ->get();

    return view(
        'employee.assets_requests.index',
        compact(
            'requests',
            'asset'
        )
    );
}
}
