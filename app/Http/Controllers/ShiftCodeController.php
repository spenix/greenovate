<?php

namespace App\Http\Controllers;

use App\Models\{ShiftCode, Shift};
use App\Http\Requests\StoreShiftCodeRequest;
use App\Http\Requests\UpdateShiftCodeRequest;
use Inertia\Inertia;
use DataTables;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redirect;


class ShiftCodeController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return Inertia::render('ShiftCode/Index', [
            'systemSetup' => [
                'nameShort' => config('app.name_short', 'Laravel'),
                'appName' => config('app.name_upper', 'Laravel'),
                'appLogo1' => config('app.logo1', 'Laravel'),
            ],
            'shifts' => Shift::where('status', 'Y')->get(),
            'days_list' => ['Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday', 'Sunday']
        ]);
    }

    public function show_table_data(Request $request)
    {
        if ($request->ajax()) {
            $data = ShiftCode::join('shifts', 'shifts.id', 'shift_codes.shift_id')
            ->selectRaw('shift_codes.id, shifts.name shift, IF(shift_codes.status = "Y", "Active", "Inactive") status, shift_codes.days')->get();
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
    public function store(StoreShiftCodeRequest $request)
    {
        try {
            $payload = ['shift_id' => $request->shift];
            //code...
            $isExist = ShiftCode::where($payload)->count();
            if ($isExist) {
                return back()->withErrors([
                    'shift' => 'The shift has already been taken.',
                ])->onlyInput('shift');
            }
            $payload['clock_in'] = $request->time_in;
            $payload['break_out'] = $request->break_out;
            $payload['break_in'] = $request->break_in;
            $payload['clock_out'] = $request->time_out;
            $payload['days'] = $request->days;
            $payload['status'] = $request->status;
            ShiftCode::create($payload);
            return Redirect::route('shift-code');
        } catch (\Throwable $e) {
            return redirect()->back()->withErrors([
                'errorMessage' => $e->getMessage(),
            ]);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(ShiftCode $shiftCode, $id)
    {
        $data = $shiftCode::find($id);
        return response()->json($data);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(ShiftCode $shiftCode)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateShiftCodeRequest $request, $id)
    {
        try {
            $payload = ['shift_id' => $request->shift];
            //code...
            $isExist = ShiftCode::where($payload)->where('id', '!=', $id)->count();
            if ($isExist) {
                return back()->withErrors([
                    'shift' => 'The shift has already been taken.',
                ])->onlyInput('shift');
            }
            $payload['clock_in'] = $request->time_in;
            $payload['break_out'] = $request->break_out;
            $payload['break_in'] = $request->break_in;
            $payload['clock_out'] = $request->time_out;
            $payload['days'] = $request->days;
            $payload['status'] = $request->status;
            ShiftCode::find($id)->update($payload);
            return Redirect::route('shift-code');
        } catch (\Throwable $e) {
            return redirect()->back()->withErrors([
                'errorMessage' => $e->getMessage(),
            ]);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(ShiftCode $shiftCode, $id)
    {
        try {
            $shiftCode::find($id)->delete();
            return Redirect::route('shift-code');
        } catch (\Throwable $e) {
            return redirect()->back()->withErrors([
                'errorMessage' => $e->getMessage(),
            ]);
        }
    }
}
