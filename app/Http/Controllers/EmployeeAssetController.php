<?php

namespace App\Http\Controllers;

use App\Models\Employee;
use App\Models\EmployeeAsset;
use Illuminate\Http\Request;
use App\Models\AssetRequest;
use App\Models\Inventory;


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

    $inventories = Inventory::where(
        'status',
        'Available'
    )->get();

    return view(
        'admin.employee_assets.create',
        compact(
            'employees',
            'inventories'
        )
    );
}

public function store(Request $request)
{
    $request->validate([
        'employee_id' => 'required',
        'inventories' => 'required|array', // Received as inventory primary IDs
    ]);

    $assetDetails = [];
    $assetNames = [];

    foreach ($request->inventories as $inventoryId) {
        $inventory = Inventory::where('id', $inventoryId)
            ->where('status', 'Available')
            ->first();

        if ($inventory) {
            $assetNames[] = $inventory->asset_type;
            
            $assetDetails[] = [
                'inventory_id' => $inventory->id,
                'asset'        => $inventory->asset_type,
                'qty'          => (int) ($request->qty[$inventoryId] ?? 1),
                'items'        => $request->asset_details[$inventoryId] ?? []
            ];

            // Mark individual inventory unit as assigned
            $inventory->update(['status' => 'Assigned']);
        }
    }

    if (empty($assetDetails)) {
        return redirect()->back()->withErrors(['inventories' => 'No available inventory assets selected.']);
    }

    EmployeeAsset::updateOrCreate(
        [
            'employee_id' => $request->employee_id
        ],
        [
            'asset_name'    => implode(',', array_unique($assetNames)),
            'asset_details' => $assetDetails,
            'message'       => $request->message,
            'assigned_date' => now(),
            'status'        => 'Assigned'
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
    
    // Fetch inventories so they match create.blade.php dynamically
    $inventories = Inventory::all(); 

    return view('admin.employee_assets.edit', compact('employeeAsset', 'employees', 'inventories'));
}

public function update(Request $request, $id)
{
    $request->validate([
        'employee_id' => 'required',
        'inventories' => 'required|array', // Read by inventory primary record keys
        'status'      => 'required|string',
    ]);

    $assetDetails = [];
    $assetNames = [];

    foreach ($request->inventories as $inventoryId) {
        $inventory = Inventory::find($inventoryId);

        if ($inventory) {
            $assetNames[] = $inventory->asset_type;
            
            $rawItems = $request->asset_details[$inventoryId] ?? [];
            $sanitizedItems = [];

            // Clean up individual structural keys
            foreach ($rawItems as $item) {
                if (isset($item['plan_days'])) {
                    $item['plan_days'] = (int) $item['plan_days'];
                }
                $sanitizedItems[] = $item;
            }

            $assetDetails[] = [
                'inventory_id' => $inventory->id,
                'asset'        => $inventory->asset_type,
                'qty'          => (int) ($request->qty[$inventoryId] ?? 1),
                'items'        => $sanitizedItems
            ];
        }
    }

    $employeeAsset = EmployeeAsset::findOrFail($id);

    $employeeAsset->update([
        'employee_id'   => $request->employee_id,
        'asset_name'    => implode(',', array_unique($assetNames)),
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