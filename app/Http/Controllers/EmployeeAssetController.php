<?php

namespace App\Http\Controllers;

use App\Models\Employee;
use App\Models\EmployeeAsset;
use Illuminate\Http\Request;
use App\Models\AssetRequest;

class EmployeeAssetController extends Controller
{
    public function index()
    {
        $employees = Employee::orderBy('firstname')->get();

        $employeeAssets = EmployeeAsset::with('employee')
            ->latest()
            ->get();

        return view(
            'admin.employee_assets.index',
            compact('employees', 'employeeAssets')
        );
    }

    public function create()
{
    $employees = Employee::orderBy('firstname')->get();

    return view(
        'admin.employee_assets.create',
        compact('employees')
    );
}

public function store(Request $request)
{
    $request->validate([
        'employee_id' => 'required',
        'assets' => 'required|array',
    ]);

    $assetDetails = [];

    foreach ($request->assets as $asset) {

        $assetDetails[] = [
            'asset' => $asset,
            'qty' => $request->qty[$asset] ?? 1,
        ];
    }

    EmployeeAsset::updateOrCreate(
        [
            'employee_id' => $request->employee_id
        ],
        [
            'asset_name' => implode(',', $request->assets),

            'asset_details' => $assetDetails,

            'message' => $request->message,

            'assigned_date' => now(),

            'status' => 'Assigned'
        ]
    );

    return redirect()
        ->route('employee-assets.index')
        ->with('success', 'Asset Assigned Successfully');
}

public function edit($id)
{
    $employeeAsset = EmployeeAsset::findOrFail($id);

    $employees = Employee::orderBy('firstname')->get();

    return view(
        'admin.employee_assets.edit',
        compact('employeeAsset', 'employees')
    );
}

public function update(Request $request, $id)
{
    $request->validate([
        'employee_id' => 'required',
        'assets' => 'required|array',
    ]);

    $assetDetails = [];

    foreach ($request->assets as $asset) {

        $assetDetails[] = [
            'asset' => $asset,
            'qty' => $request->qty[$asset] ?? 1,
        ];
    }

    $employeeAsset = EmployeeAsset::findOrFail($id);

    $employeeAsset->update([
        'employee_id'   => $request->employee_id,
        'asset_name'    => implode(',', $request->assets),
        'asset_details' => $assetDetails,
        'message'       => $request->message,
        'status'        => $request->status,
    ]);

    return redirect()
        ->route('employee-assets.index')
        ->with('success', 'Asset Updated Successfully');
}

public function destroy($id)
{
    $asset = EmployeeAsset::findOrFail($id);

    $asset->delete();

    return redirect()
        ->route('employee-assets.index')
        ->with('success', 'Asset Deleted Successfully');
}

public function myAssets()
{
    $employee = Employee::where(
        'email',
        auth()->user()->email
    )->first();

    $assets = EmployeeAsset::where(
        'employee_id',
        $employee->id
    )->get();

    return view(
        'employee.assets.index',
        compact('assets')
    );
}

public function requests($id)
{
    $asset = EmployeeAsset::with('employee')
        ->findOrFail($id);

    $requests = AssetRequest::where(
        'employee_asset_id',
        $id
    )
    ->latest()
    ->get();

    return view(
        'admin.employee_assets.requests',
        compact(
            'asset',
            'requests'
        )
    );
}

public function assetRequestEdit($id)
{
    $requestData = AssetRequest::with([
        'employee',
        'asset'
    ])->findOrFail($id);

    return view(
        'admin.employee_assets.asset_requests_edit',
        compact('requestData')
    );
}

public function assetRequestUpdate(Request $request, $id)
{


    $assetRequest = AssetRequest::findOrFail($id);

    $assetRequest->update([

        'status' => $request->status,

        'admin_remark' => $request->admin_remark,

        'approved_by' => auth()->id(),

        'approved_at' => now(),

    ]);

    return redirect()
        ->route(
            'employee-assets.requests',
            $assetRequest->employee_asset_id
        )
        ->with(
            'success',
            'Request Updated Successfully'
        );
}




}