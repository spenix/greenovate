<?php

namespace App\Http\Controllers;

use App\Models\{EmpBasicSalary, Department, Designation};
use App\Http\Requests\StoreEmpBasicSalaryRequest;
use App\Http\Requests\UpdateEmpBasicSalaryRequest;
use Illuminate\Http\Request;
use Inertia\Inertia;
use DataTables;
use Illuminate\Support\Facades\Redirect;

class EmpBasicSalaryController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return Inertia::render('BasicSalary/Index', [
            'systemSetup' => [
                'nameShort' => config('app.name_short', 'Laravel'),
                'appName' => config('app.name_upper', 'Laravel'),
                'appLogo1' => config('app.logo1', 'Laravel'),
            ],
            'departments' => Department::where('status', 'Y')->get(),
            'designations' => Designation::where('status', 'Y')->get(),
        ]);
    }

    public function show_table_data(Request $request)
    {
        if ($request->ajax()) {
            $data = EmpBasicSalary::join('designations', 'designations.id', 'emp_basic_salaries.designation_id')
            ->join('departments', 'departments.id', 'designations.department_id')
            ->selectRaw('emp_basic_salaries.*, designations.name as designation, departments.name as department, IF(emp_basic_salaries.status = "Y", "Active", "Inactive") status')
            ->get();
            return datatables::of($data)
                ->addIndexColumn()
                ->make(true);
        }
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
    public function store(StoreEmpBasicSalaryRequest $request)
    {
        try {
            $payload = [
                'designation_id' => $request->position
            ];
            //code...
            $isExist = EmpBasicSalary::where($payload)->count();
            if ($isExist) {
                return back()->withErrors([
                    'position' => 'This position has already been setup.',
                ])->onlyInput('position');
            }
            $payload['basic_salary'] = $request->basic_salary;
            $payload['status'] = $request->status;
            EmpBasicSalary::create($payload);
            return Redirect::route('basic-salary');
        } catch (\Throwable $e) {
            return redirect()->back()->withErrors([
                'errorMessage' => $e->getMessage(),
            ]);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(EmpBasicSalary $empBasicSalary, $id)
    {
        $data = empBasicSalary::find($id);
        return response()->json($data);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(EmpBasicSalary $empBasicSalary)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateEmpBasicSalaryRequest $request, $id)
    {
        try {
            $payload = [
                'designation_id' => $request->position
            ];
            //code...
            $isExist = EmpBasicSalary::where($payload)->where('id', '!=', $id)->count();
            if ($isExist) {
                return back()->withErrors([
                    'position' => 'This position has already been setup.',
                ])->onlyInput('position');
            }
            $payload['basic_salary'] = $request->basic_salary;
            $payload['status'] = $request->status;
            EmpBasicSalary::find($id)->update($payload);
            return Redirect::route('basic-salary');
        } catch (\Throwable $e) {
            return redirect()->back()->withErrors([
                'errorMessage' => $e->getMessage(),
            ]);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(EmpBasicSalary $empBasicSalary)
    {
        try {
            $empBasicSalary::find($id)->delete();
            return Redirect::route('basic-salary');
        } catch (\Throwable $e) {
            return redirect()->back()->withErrors([
                'errorMessage' => $e->getMessage(),
            ]);
        }
    }
}
