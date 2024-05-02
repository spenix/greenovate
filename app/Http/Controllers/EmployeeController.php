<?php

namespace App\Http\Controllers;

use App\Models\Employee;
use App\Http\Requests\{StoreEmployeeRequest, UpdateEmployeeRequest};
use App\Models\{BloodType, Designation, EducationalAttainment, EducationalLevel, EmployeeType, FamilyBackground, RelationshipType, WorkExperience};
use Illuminate\Http\Request;
use DataTables;
use Illuminate\Support\Facades\Redirect;
use Inertia\Inertia;


class EmployeeController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return Inertia::render('Employee/Index', [
            'systemSetup' => [
                'nameShort' => config('app.name_short', 'Laravel'),
                'appName' => config('app.name_upper', 'Laravel'),
                'appLogo1' => config('app.logo1', 'Laravel'),
            ],
            'bloodTypes' => BloodType::where('status', 'Y')->get(),
            'employeeTypes' => EmployeeType::where('status', 'Y')->get(),
            'designations' => Designation::where('status', 'Y')->get(),
            'relationshipTypes' => RelationshipType::where('status', 'Y')->get(),
            'educationalLevels' => EducationalLevel::where('status', 'Y')->get(),
            'years' => range(1970, date('Y')),
            'current_year' => date('Y')
        ]);
    }

    public function show_table_data(Request $request)
    {
        if ($request->ajax()) {
            $data = Employee::with([
                'familyBackground',
                'workExperience',
                'educationalAttainment'
            ])->join('employee_types', 'employee_types.id', 'employees.employee_type_id')
                ->selectRaw("employees.*, CONCAT(employees.firstname, ' ', employees.lastname) as employee_name, employee_types.name as employee_type");
            if ($request->department) {
                $data->where('designation_id', $request->department);
            }

            if ($request->employee_type) {
                $data->where('employee_type_id', $request->employee_type);
            }

            return datatables::of($data->get())
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
    public function store(StoreEmployeeRequest $request)
    {
        try {

            $payload = [
                'firstname' => $request->firstname,
                'middlename' => $request->middlename,
                'lastname' => $request->lastname,
                'gender' => $request->gender,
                'suffix' => $request->suffix,
                'blood_type_id' => $request->blood_type,
                'birthdate' => $request->birthdate,
                'employee_no' => $request->employee_id,
                'contact_no' => $request->contact_no,
                'email' => $request->email,
                'employee_type_id' => $request->employee_type,
                'designation_id' => $request->department,
                'date_hired' => $request->date_hired,
                'sss' => $request->sss,
                'tin' => $request->tin,
                'pag_ibig' => $request->pag_ibig,
                'philhealth' => $request->philhealth,
                'rate' => $request->rate,
            ];
            //code...
            $isExist = Employee::where([
                'firstname' => $request->firstname,
                'middlename' => $request->middlename,
                'lastname' => $request->lastname,
                'gender' => $request->gender
            ])->count();

            if ($isExist) {
                return back()->withErrors([
                    'errorMessage' => 'The Employee already exists.',
                ]);
            }


            $isExistID = Employee::where([
                'employee_no' => $request->employee_id,
            ])->count();

            if ($isExistID) {
                return back()->withErrors([
                    'errorMessage' => 'The employee ID has already been taken.',
                ])->onlyInput('employee_id');
            }

            $emp = Employee::create($payload);

            if ($emp->id) {
                $familyBackgoundArr = [];
                foreach ($request->familyBackgound as $key => $value) {
                    $familyBackgoundArr[] = [
                        'employee_id' => $emp->id,
                        'name' => $value['name'],
                        'relationship_type_id' => $value['relationship'],
                        'address' => $value['address'],
                        'contact_no' => $value['contact_no'],
                        'birthdate' => $value['birthdate'],
                        'occupation' => $value['occupation'],
                        'created_at' => now(),
                    ];
                }
                if (count($familyBackgoundArr)) {
                    FamilyBackground::insert($familyBackgoundArr);
                }

                $workExperienceArr = [];
                foreach ($request->workExperience as $key => $value) {
                    $workExperienceArr[] = [
                        'employee_id' => $emp->id,
                        'company_name' => $value['company_name'],
                        'position' => $value['position'],
                        'date_start' => $value['date_from'],
                        'date_end' => $value['date_to'],
                        'created_at' => now(),
                    ];
                }
                if (count($workExperienceArr)) {
                    WorkExperience::insert($workExperienceArr);
                }

                $educationalAttainmentArr = [];
                foreach ($request->educationalAttainment as $key => $value) {
                    $educationalAttainmentArr[] = [
                        'employee_id' => $emp->id,
                        'school_name' => $value['school_name'],
                        'address' => $value['address'],
                        'educational_level_id' => $value['school_level'],
                        'year_start' => $value['year_from'],
                        'year_end' => $value['year_to'],
                        'created_at' => now(),
                    ];
                }
                if (count($educationalAttainmentArr)) {
                    EducationalAttainment::insert($educationalAttainmentArr);
                }
            }

            return Redirect::route('employees');
        } catch (\Throwable $e) {
            return redirect()->back()->withErrors([
                'errorMessage' => $e->getMessage(),
            ]);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(Employee $employee, $id)
    {
        $data = $employee::with([
            'familyBackground',
            'workExperience',
            'educationalAttainment'
        ])->find($id);
        return response()->json($data);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Employee $employee)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateEmployeeRequest $request, $id)
    {
        try {

            $payload = [
                'firstname' => $request->firstname,
                'middlename' => $request->middlename,
                'lastname' => $request->lastname,
                'gender' => $request->gender,
                'suffix' => $request->suffix,
                'blood_type_id' => $request->blood_type,
                'birthdate' => $request->birthdate,
                'employee_no' => $request->employee_id,
                'contact_no' => $request->contact_no,
                'email' => $request->email,
                'employee_type_id' => $request->employee_type,
                'designation_id' => $request->department,
                'date_hired' => $request->date_hired,
                'sss' => $request->sss,
                'tin' => $request->tin,
                'pag_ibig' => $request->pag_ibig,
                'philhealth' => $request->philhealth,
                'rate' => $request->rate,
            ];
            //code...
            $isExist = Employee::where([
                'firstname' => $request->firstname,
                'middlename' => $request->middlename,
                'lastname' => $request->lastname,
                'gender' => $request->gender
            ])->where('id', '!=', $id)->count();

            if ($isExist) {
                return back()->withErrors([
                    'errorMessage' => 'The Employee already exists.',
                ]);
            }


            $isExistID = Employee::where([
                'employee_no' => $request->employee_id,
            ])->where('id', '!=', $id)->count();

            if ($isExistID) {
                return back()->withErrors([
                    'employee_id' => 'The employee ID has already been taken.',
                ])->onlyInput('employee_id');
            }

            Employee::find($id)->update($payload);

            if ($id) {
                FamilyBackground::where('employee_id', $id)->delete();
                WorkExperience::where('employee_id', $id)->delete();
                EducationalAttainment::where('employee_id', $id)->delete();
                $familyBackgoundArr = [];
                foreach ($request->familyBackgound as $key => $value) {
                    $familyBackgoundArr[] = [
                        'employee_id' => $id,
                        'name' => $value['name'],
                        'relationship_type_id' => $value['relationship'],
                        'address' => $value['address'],
                        'contact_no' => $value['contact_no'],
                        'birthdate' => $value['birthdate'],
                        'occupation' => $value['occupation'],
                        'created_at' => now(),
                    ];
                }
                if (count($familyBackgoundArr)) {
                    FamilyBackground::insert($familyBackgoundArr);
                }

                $workExperienceArr = [];
                foreach ($request->workExperience as $key => $value) {
                    $workExperienceArr[] = [
                        'employee_id' => $id,
                        'company_name' => $value['company_name'],
                        'position' => $value['position'],
                        'date_start' => $value['date_from'],
                        'date_end' => $value['date_to'],
                        'created_at' => now(),
                    ];
                }
                if (count($workExperienceArr)) {
                    WorkExperience::insert($workExperienceArr);
                }

                $educationalAttainmentArr = [];
                foreach ($request->educationalAttainment as $key => $value) {
                    $educationalAttainmentArr[] = [
                        'employee_id' => $id,
                        'school_name' => $value['school_name'],
                        'address' => $value['address'],
                        'educational_level_id' => $value['school_level'],
                        'year_start' => $value['year_from'],
                        'year_end' => $value['year_to'],
                        'created_at' => now(),
                    ];
                }
                if (count($educationalAttainmentArr)) {
                    EducationalAttainment::insert($educationalAttainmentArr);
                }
            }

            return Redirect::route('employees');
        } catch (\Throwable $e) {
            return redirect()->back()->withErrors([
                'errorMessage' => $e->getMessage(),
            ]);
        }
    }



    /**
     * Remove the specified resource from storage.
     */
    public function terminate(Employee $employee, $id, $status, Request $request)
    {
        try {
            $employee::find($id)->update(['terminate' => $status, 'reason' => ($status == 'Y' ? $request->reason : null), 'termination_date' => ($status == 'Y' ? now() : null)]);
            return Redirect::route('employees')->withSuccess('employees');
        } catch (\Throwable $e) {
            return redirect()->back()->withErrors([
                'errorMessage' => $e->getMessage(),
            ]);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Employee $employee, $id)
    {
        try {
            $employee::find($id)->delete();
            return Redirect::route('employees');
        } catch (\Throwable $e) {
            return redirect()->back()->withErrors([
                'errorMessage' => $e->getMessage(),
            ]);
        }
    }
}
