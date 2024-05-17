<?php

namespace App\Http\Controllers;

use App\Models\SystemCalendar;
use App\Http\Requests\StoreSystemCalendarRequest;
use App\Http\Requests\UpdateSystemCalendarRequest;
use Inertia\Inertia;
use DataTables;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redirect;

class SystemCalendarController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return Inertia::render('SystemCalendar/Index', [
            'systemSetup' => [
                'nameShort' => config('app.name_short', 'Laravel'),
                'appName' => config('app.name_upper', 'Laravel'),
                'appLogo1' => config('app.logo1', 'Laravel'),
            ],
        ]);
    }

    public function show_table_data(Request $request)
    {
        if ($request->ajax()) {
            $data = SystemCalendar::get();
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
    public function store(StoreSystemCalendarRequest $request)
    {
        try {
            $payload = ['name' => $request->title];
            //code...
            $isExist = SystemCalendar::where($payload)->count();
            if ($isExist) {
                return back()->withErrors([
                    'title' => 'The title has already been taken.',
                ])->onlyInput('title');
            }
            $payload['description'] = $request->description;
            $payload['start_date'] = $request->start_date;
            $payload['end_date'] = $request->end_date;
            SystemCalendar::create($payload);
            return Redirect::route('system-calendar');
        } catch (\Throwable $e) {
            return redirect()->back()->withErrors([
                'errorMessage' => $e->getMessage(),
            ]);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(SystemCalendar $systemCalendar, $id)
    {
        $data = $systemCalendar::find($id);
        return response()->json($data);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(SystemCalendar $systemCalendar)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateSystemCalendarRequest $request, $id)
    {
        try {
            $payload = ['name' => $request->title];
            //code...
            $isExist = SystemCalendar::where($payload)->where('id', '!=', $id)->count();
            if ($isExist) {
                return back()->withErrors([
                    'title' => 'The title has already been taken.',
                ])->onlyInput('title');
            }
            $payload['description'] = $request->description;
            $payload['start_date'] = $request->start_date;
            $payload['end_date'] = $request->end_date;
            SystemCalendar::find($id)->update($payload);
            return Redirect::route('system-calendar');
        } catch (\Throwable $e) {
            return redirect()->back()->withErrors([
                'errorMessage' => $e->getMessage(),
            ]);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(SystemCalendar $systemCalendar, $id)
    {
        try {
            $systemCalendar::find($id)->delete();
            return Redirect::route('system-calendar');
        } catch (\Throwable $e) {
            return redirect()->back()->withErrors([
                'errorMessage' => $e->getMessage(),
            ]);
        }
    }
}
