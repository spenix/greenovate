<?php

namespace App\Http\Controllers;

use App\Models\Department;
use App\Models\Designation;
use Illuminate\Http\Request;
use Inertia\Inertia;
use DataTables;
use Illuminate\Support\Facades\Redirect;

class DesignationController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return Inertia::render('CodeLibrary/Designation/Index', [
            'systemSetup' => [
                'nameShort' => config('app.name_short', 'Laravel'),
                'appName' => config('app.name_upper', 'Laravel'),
                'appLogo1' => config('app.logo1', 'Laravel'),
            ],
            'departments' => Department::where('status', 'Y')->get()
        ]);
    }

    public function show_table_data(Request $request)
    {
        if ($request->ajax()) {
            $data = Designation::join('departments', 'departments.id', 'designations.department_id')
                ->selectRaw('designations.*, departments.name department, IF(designations.status = "Y", "Active", "Inactive") status');
            return datatables::of($data)
                ->addIndexColumn()
                ->filterColumn('department', function ($query, $keyword) {
                    $sql = "departments.name like ?";
                    $query->whereRaw($sql, ["%{$keyword}%"]);
                })
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
    public function store(Request $request)
    {
        try {
            $validated = $request->validate([
                'position' => 'required|string|max:255',
                'department' => 'required|integer|exists:departments,id',
                'status' => 'required|in:Y,N',
            ]);
            $payload = ['name' => $validated['position'], 'department_id' => $validated['department']];
            //code...
            $isExist = Designation::where($payload)->count();
            if ($isExist) {
                return back()->withErrors([
                    'position' => 'The position has already been taken.',
                ])->onlyInput('position');
            }
            $payload['status'] = $request->status;
            Designation::create($payload);
            return Redirect::route('positions');
        } catch (\Throwable $e) {
            return redirect()->back()->withErrors([
                'errorMessage' => $e->getMessage(),
            ]);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $data = Designation::find($id);
        return response()->json($data);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        try {
            $validated = $request->validate([
                'position' => 'required|string|max:255',
                'department' => 'required|integer|exists:departments,id',
                'status' => 'required|in:Y,N',
            ]);

            $payload = ['name' => $validated['position'], 'department_id' => $validated['department']];
            //code...
            $isExist = Designation::where($payload)->where('id', '!=', $id)->count();
            if ($isExist) {
                return back()->withErrors([
                    'position' => 'The position has already been taken.',
                ])->onlyInput('position');
            }
            $payload['status'] = $request->status;
            Designation::find($id)->update($payload);
            return Redirect::route('positions');
        } catch (\Throwable $e) {
            return redirect()->back()->withErrors([
                'errorMessage' => $e->getMessage(),
            ]);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        try {
            Designation::find($id)->delete();
            return Redirect::route('positions');
        } catch (\Throwable $e) {
            return redirect()->back()->withErrors([
                'errorMessage' => $e->getMessage(),
            ]);
        }
    }
}
