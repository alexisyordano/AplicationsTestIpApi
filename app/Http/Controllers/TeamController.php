<?php

namespace App\Http\Controllers;
use App\Models\Team;
use App\Models\ResultsTestsLatency;
use Illuminate\Support\Facades\Validator;  
use Illuminate\Http\Request;
use Carbon\Carbon;

class TeamController extends Controller
{

    public function index()
    {
        $team = Team::all();


        $data = [
            'data' => $team,
            'status' => 200
        ];

        return response()->json($data, 200);
    }
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|max:255',
            'adress' => 'required|unique:team,adress',
            'description' => 'required',
        ]);

        if ($validator->fails()) {
            $data = [
                'message' => 'Data validation error',
                'errors' => $validator->errors(),
                'status' => 400
            ];
            return response()->json($data, 400);
        }

        $team = Team::create([
            'name' => $request->name,
            'adress' => $request->adress,
            'description' => $request->description,
        ]);

        if (!$team) {
            $data = [
                'message' => 'Error creating team',
                'status' => 500
            ];
            return response()->json($data, 500);
        }

        $data = [
            'data' => $team,
            'status' => 201
        ];

        return response()->json($data, 201);
    }

    public function update(Request $request)
    {
        $team = Team::find($request->id);

        if (!$team) {
            $data = [
                'message' => 'resource not found',
                'status' => 404
            ];
            return response()->json($data, 404);
        }

        $validator = Validator::make($request->all(), [
            'name' => 'required|max:255',
            'adress' => 'required',
            'description' => 'required',
        ]);

        if ($validator->fails()) {
            $data = [
                'message' => 'Data validation error',
                'errors' => $validator->errors(),
                'status' => 400
            ];
            return response()->json($data, 400);
        }

        $team->name = $request->name;
        $team->adress = $request->adress;
        $team->description = $request->description;

        $team->save();

        $data = [
            'message' => 'updated record',
            'data' => $team,
            'status' => 200
        ];

        return response()->json($data, 200);

    }

    public function destroy(Request $request)
    {
        $team = Team::join('result_latency_test', 'team.id', '=', 'result_latency_test.id_team')
                    ->select('team.*', 'result_latency_test.*')
                    ->where('team.id', $request->id)
                    ->first();

        //eliminate equipment without lactation test
        if (!$team) {
            $team = Team::find($request->id);
            $team->delete();
            $data = [
                'message' => 'confirm',
                'status' => 200
            ];

            return response()->json($data, 200);
        }

        //It cannot be eliminated. It has lactation tests
        if($team->response){
            return response()->json(false, 200);
        }

    }

    public function search(Request $request)
    {
        $search = $request->query('search');

        if ($search) {
            $teams = Team::where('name', 'like', '%' . $search . '%')->get();
        } else {
            $teams = Team::all();
        }

        return response()->json(['data' => $teams]);
    }

    public function show($id)
    {
        // return response()->json($id, 200);
        $team = Team::find($id);

        if (!$team) {
            $data = [
                'message' => 'resource not found',
                'status' => 404
            ];
            return response()->json($data, 404);
        }

        $data = [
            $team,
            'status' => 200
        ];

        return response()->json($data, 200);
    }

    public function test(Request $request)
    {
        
        $request->validate([
            'test' => 'required|ip',
            'id' => 'required|exists:team,id',
            
        ]);

        $testip = $request->input('test');

        //  Execute the ping command and capture the output and status
        exec("ping -n 3 $testip", $output, $status);

        $now = Carbon::now();
        $output = mb_convert_encoding($output, 'UTF-8', 'UTF-8');

        $result = ResultsTestsLatency::create([
            'id_team' => $request->id,
            'date' => $now,
            'response' => $status,
            'description' => implode(', ', $output),
        ]);

        $response = ['descriptions' => $output, 'status' => $status];

        if ($status !== 0) {
        // error
         return response()->json($response);;
        }
        else{
            // success
         return response()->json($response);
       }   
    }


    public function datechart(Request $request){
        $results = ResultsTestsLatency::whereBetween('date', [$request->startDate, $request->endDate])->get();
        return response()->json($results, 200);  
    }
}
