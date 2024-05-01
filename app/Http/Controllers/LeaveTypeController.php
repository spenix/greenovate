<?php

namespace App\Http\Controllers;

use App\Models\LeaveType;
use App\Http\Requests\StoreLeaveTypeRequest;
use App\Http\Requests\UpdateLeaveTypeRequest;
use App\Models\LeaveEntitlement;
use Inertia\Inertia;
use DataTables;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redirect;

class LeaveTypeController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        // $data = LeaveType::join('leave_entitlements', 'leave_entitlements.id', 'leave_types.leave_entitlement_id')
        //     ->selectRaw('leave_types.*, leave_entitlements.name leave_entitlement');
        return Inertia::render('LeaveType/Index', [
            'systemSetup' => [
                'nameShort' => config('app.name_short', 'Laravel'),
                'appName' => config('app.name_upper', 'Laravel'),
                'appLogo1' => config('app.logo1', 'Laravel'),
            ],
            'leaveEntitlements' => LeaveEntitlement::get(),
            // 'LeaveTypes' => $data
        ]);
    }

    public function show_table_data(Request $request)
    {
        if ($request->ajax()) {
            $data = LeaveType::join('leave_entitlements', 'leave_entitlements.id', 'leave_types.leave_entitlement_id')
                ->selectRaw('leave_types.*, leave_entitlements.name leave_entitlement');
            return datatables::of($data)
                ->addIndexColumn()
                ->filterColumn('leave_entitlement', function ($query, $keyword) {
                    $sql = "leave_entitlements.name like ?";
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
    public function store(StoreLeaveTypeRequest $request)
    {
        try {
            $payload = ['name' => $request->leave_type, 'leave_entitlement_id' => $request->leave_entitlement];
            //code...
            $isExist = LeaveType::where($payload)->count();
            if ($isExist) {
                return back()->withErrors([
                    'leave_type' => 'The leave type has already been taken.',
                ])->onlyInput('leave_type');
            }
            LeaveType::create($payload);
            return Redirect::route('leave-types');
        } catch (\Throwable $e) {
            return redirect()->back()->withErrors([
                'errorMessage' => $e->getMessage(),
            ]);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(LeaveType $leaveType, $id)
    {
        $data = LeaveType::find($id);
        return response()->json($data);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(LeaveType $leaveType)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateLeaveTypeRequest $request, $id)
    {
        try {
            $payload = ['name' => $request->leave_type, 'leave_entitlement_id' => $request->leave_entitlement];
            //code...
            $isExist = LeaveType::where($payload)->where('id', '!=', $id)->count();
            if ($isExist) {
                return back()->withErrors([
                    'leave_type' => 'The leave type has already been taken.',
                ])->onlyInput('leave_type');
            }
            LeaveType::find($id)->update($payload);
            return Redirect::route('leave-types');
        } catch (\Throwable $e) {
            return redirect()->back()->withErrors([
                'errorMessage' => $e->getMessage(),
            ]);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(LeaveType $leaveType, $id)
    {
        try {
            $leaveType::find($id)->delete();
            return Redirect::route('leave-types');
        } catch (\Throwable $e) {
            return redirect()->back()->withErrors([
                'errorMessage' => $e->getMessage(),
            ]);
        }
    }
}
