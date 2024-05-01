<?php

namespace App\Http\Controllers;

use App\Models\LeaveCredit;
use App\Http\Requests\StoreLeaveCreditRequest;
use App\Http\Requests\UpdateLeaveCreditRequest;
use App\Models\Employee;
use App\Models\EmployeeLeaveCredit;
use Illuminate\Support\Facades\Redirect;
use Inertia\Inertia;

class LeaveCreditController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {

        return Inertia::render('LeaveCredit/Index', [
            'systemSetup' => [
                'nameShort' => config('app.name_short', 'Laravel'),
                'appName' => config('app.name_upper', 'Laravel'),
                'appLogo1' => config('app.logo1', 'Laravel'),
            ],
            'data' => LeaveCredit::get(['id', 'leave_credit'])->first()
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreLeaveCreditRequest $request)
    {
        $data = LeaveCredit::create($request->validated());
        if ($data->id) {
            $emps = Employee::where('terminate', 'N')->get();
            $payload = [];
            foreach ($emps as $key => $value) {
                $payload[] = [
                    'employee_id' => $value['id'],
                    'leave_credit' => $data->leave_credit,
                    'year_applicable' => date('Y'),
                    'created_at' => now(),
                ];
            }
            EmployeeLeaveCredit::insert($payload);
        }
        return Redirect::route('leave-credits');
    }

    /**
     * Display the specified resource.
     */
    public function show(LeaveCredit $leaveCredit)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(LeaveCredit $leaveCredit)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateLeaveCreditRequest $request, $id)
    {
        LeaveCredit::find($id)->update(['leave_credit' => $request->leave_credit]);
        if ($id) {
            $emps = Employee::where('terminate', 'N')->get();
            $payload = [];
            $data = EmployeeLeaveCredit::where('year_applicable', date('Y'))->delete();
            foreach ($emps as $key => $value) {
                $payload[] = [
                    'employee_id' => $value['id'],
                    'leave_credit' => $request->leave_credit,
                    'year_applicable' => date('Y'),
                    'created_at' => now(),
                ];
            }
            EmployeeLeaveCredit::insert($payload);
        }
        return Redirect::route('leave-credits');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(LeaveCredit $leaveCredit)
    {
        //
    }
}
