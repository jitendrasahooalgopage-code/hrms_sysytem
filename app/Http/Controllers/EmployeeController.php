<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreEmployeeRequest;
use App\Http\Requests\UpdateEmployeeRequest;
use App\Models\Department;
use App\Models\Designation;
use App\Models\Employee;
use App\Models\EmployeeHierarchy;
use App\Models\Salary;
use App\Models\Schedule;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Redirect;

class EmployeeController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
        $employees = Employee::all();

        return view('admin.employee.index', compact('employees'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
        $departments = Department::all();
        $designations = Designation::all();
        $schedules = Schedule::all();

        return view('admin.employee.create', compact('departments', 'designations', 'schedules'));
    }

    /**
     * Store a newly created resource in storage.
     */
    // public function store(StoreEmployeeRequest $request)
    // {
    //     dd($request->all());
    //     $employee = Employee::create($request->all());
    //     if($employee) {
    //         $salary = new Salary($request->all());
    //         $employee->salary()->save($salary);
    //     }
    //     return back()->with('success', 'Employee created successfully.');

    // }

    public function store(StoreEmployeeRequest $request)
    {
        // dd($request->all());
        $data = $request->except([
            '_token',
            'basic',
            'house_rent',
            'medical',
            'transport',
            'phone_bill',
            'internet_bill',
            'special',
            'provident_fund',
            'income_tax',
            'health_insurance',
            'life_insurance',
        ]);

        // Handle avatar upload
        // if ($request->hasFile('avatar')) {
        //     $file = $request->file('avatar');
        //     $filename = time() . '.' . $file->getClientOriginalExtension();
        //     $file->move(public_path('uploads/employees'), $filename);

        //     $data['avatar'] = $filename;
        // }

        if ($request->hasFile('avatar')) {
            $data['avatar'] = $request->file('avatar')
                ->store('employees/docs', 'public');
        }

        if ($request->hasFile('aadhaar_card')) {
            $data['aadhaar_card'] = $request->file('aadhaar_card')
                ->store('employees/docs', 'public');
        }

        if ($request->hasFile('pan_card')) {
            $data['pan_card'] = $request->file('pan_card')
                ->store('employees/docs', 'public');
        }
        if ($request->hasFile('matric_certificate')) {
            $data['matric_certificate'] = $request->file('matric_certificate')
                ->store('employees/docs', 'public');
        }

        if ($request->hasFile('plus_two_certificate')) {
            $data['plus_two_certificate'] = $request->file('plus_two_certificate')
                ->store('employees/docs', 'public');
        }

        if ($request->hasFile('bachelor_degree_certificate')) {
            $data['bachelor_degree_certificate'] = $request->file('bachelor_degree_certificate')
                ->store('employees/docs', 'public');
        }

        if ($request->hasFile('master_degree_certificate')) {
            $data['master_degree_certificate'] = $request->file('master_degree_certificate')
                ->store('employees/docs', 'public');
        }

        if ($request->hasFile('address_proof')) {
            $data['address_proof'] = $request->file('address_proof')
                ->store('employees/docs', 'public');
        }

        if ($request->hasFile('last_company_release_letter')) {
            $data['last_company_release_letter'] = $request->file('last_company_release_letter')
                ->store('employees/docs', 'public');
        }

        if ($request->hasFile('last_company_offer_letter')) {
            $data['last_company_offer_letter'] = $request->file('last_company_offer_letter')
                ->store('employees/docs', 'public');
        }

        if ($request->hasFile('salary_slip_1')) {
            $data['salary_slip_1'] = $request->file('salary_slip_1')
                ->store('employees/docs', 'public');
        }

        if ($request->hasFile('salary_slip_2')) {
            $data['salary_slip_2'] = $request->file('salary_slip_2')
                ->store('employees/docs', 'public');
        }

        if ($request->hasFile('salary_slip_3')) {
            $data['salary_slip_3'] = $request->file('salary_slip_3')
                ->store('employees/docs', 'public');
        }

        if ($request->hasFile('bank_passbook_page')) {
            $data['bank_passbook_page'] = $request->file('bank_passbook_page')
                ->store('employees/docs', 'public');
        }

        $user = User::create(

            ['role_id' => 8, 'name' => $request->firstname.' '.$request->lastname,
                'email' => $request->email, 'phone' => $request->phone,
                'status' => 1, 'password' => Hash::make('Emp@1234')],

        );

        if ($user) {
            $data['user_id'] = $user->id;
            $employee = Employee::create($data);
        }

        // Store salary details
        if ($employee) {
            $salaryData = [
                'basic' => $request->basic,
                'house_rent' => $request->house_rent,
                'medical' => $request->medical,
                'transport' => $request->transport,
                'phone_bill' => $request->phone_bill,
                'internet_bill' => $request->internet_bill,
                'special' => $request->special,
                'provident_fund' => $request->provident_fund,
                'income_tax' => $request->income_tax,
                'health_insurance' => $request->health_insurance,
                'life_insurance' => $request->life_insurance,
            ];

            $employee->salary()->create($salaryData);

            if ($employee) {
                EmployeeHierarchy::create([
                    'employee_id' => $employee->id,
                    'team_lead_id' => $request->team_lead_id ?: null,
                    'manager_id' => $request->manager_id ?: null,
                    'hr_id' => $request->hr_id ?: null,
                ]);
            }

        }

        return redirect()->back()->with('success', 'Employee created successfully.');
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {

        //
        // $employee = Employee::findOrFail($id);

        $employee = Employee::with([
            'department',
            'designation',
            'schedule',
            'salary',
            'attendances',
            'leaves',
            'payrolls',
            'user',
        ])->findOrFail($id);
        // dd($employee);
        $departments = Department::all();
        $designations = Designation::all();
        $schedules = Schedule::all();

        return view('admin.employee.show', compact('employee', 'departments', 'designations', 'schedules'));

    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($id)
    {
        //
        $employee = Employee::findOrFail($id);
        $departments = Department::all();
        $designations = Designation::all();
        $schedules = Schedule::all();

        return view('admin.employee.edit', compact('employee', 'departments', 'designations', 'schedules'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateEmployeeRequest $request, Employee $employee)
    {
        //
        $employee->update($request->all());
        $salary = $employee->salary ?: new Salary;
        $salary->fill($request->all());
        $employee->salary()->save($salary);

        // return Redirect::route('admin.employee.edit')->with('success', 'Employee updated successfully');
        return back()->with('success', 'Employee updated successfully');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Employee $employee)
    {
        //
        $employee->delete();

        return back()->with('success', 'Employee deleted successfully');
    }
    // Add to EmployeeController (or wherever your employee routes point)

    public function getByDepartment(Request $request)
    {

        $employees = Employee::where('department_id', $request->department_id)
            ->orWhere('department_id', 2)
            ->select('id', 'firstname', 'lastname')
            ->get()
            ->map(fn ($e) => ['id' => $e->id, 'name' => $e->firstname.' '.$e->lastname]);

        return response()->json($employees);
    }

    public function getHrDepartmentData(Request $request)
    {

        $employees = Employee::where('department_id', '2')
            ->select('id', 'firstname', 'lastname')
            ->get()
            ->map(fn ($e) => ['id' => $e->id, 'name' => $e->firstname.' '.$e->lastname]);

        return response()->json($employees);
    }

    public function hierarchyChain(Employee $employee)
    {
        $hierarchy = EmployeeHierarchy::where('employee_id', $employee->id)->first();

        $chain = [];

        if ($hierarchy) {
            if ($hierarchy->team_lead_id) {
                $tl = Employee::find($hierarchy->team_lead_id);
                $chain[] = 'Team Lead: '.$tl?->firstname.' '.$tl?->lastname;
            }
            if ($hierarchy->manager_id) {
                $mgr = Employee::find($hierarchy->manager_id);
                $chain[] = 'Manager: '.$mgr?->firstname.' '.$mgr?->lastname;
            }
            if ($hierarchy->hr_id) {
                $hr = Employee::find($hierarchy->hr_id);
                $chain[] = 'HR: '.$hr?->firstname.' '.$hr?->lastname;
            }
        }

        return response()->json(['chain' => $chain]);
    }
}
