<?php

namespace App\Http\Controllers;

use App\MessageToAdmin;
use App\User;
use App\Http\Controllers\Controller;
use Yajra\Datatables\Facades\Datatables;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use App\Traits\StringTrait;
use Auth;
use Carbon\Carbon;
use JWTAuth;
use Tymon\JWTAuth\Exceptions\JWTException;
use DB;
use App\Filters\MessageToAdminFilters;

class MessageToAdminController extends Controller
{

    
    use StringTrait;
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {

        return view('master.messageToAdmin');
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function masterDataTable(){

        $userRole = Auth::user()->role;
        $userId = Auth::user()->id;

        if (($userRole == 'Master') || ($userRole == 'Master')) {
            $data = MessageToAdmin::where('message_to_admin.deleted_at', null)
                        ->join('users', 'message_to_admin.user_id', '=', 'users.id')
                        ->select('message_to_admin.*', 'users.email as user')
                        // ->orderBy('id', 'desc')
                        ->get();
        }
        else {
            $data = MessageToAdmin::where('message_to_admin.deleted_at', null)
                        ->where('message_to_admin.user_id', $userId)
                        ->join('users', 'message_to_admin.user_id', '=', 'users.id')
                        ->select('message_to_admin.*', 'users.email as user')
                        // ->orderBy('id', 'desc')
                        ->get();
        }


        return $this->makeTable($data);
    }

    // Data for select2 with Filters
    public function getDataWithFilters(MessageToAdminFilters $filters){
        $data = MessageToAdmin::filter($filters)->get();

        return $data;
    }

    // Datatable template
    public function makeTable($data){

        return Datatables::of($data)
                ->addColumn('action', function ($item) {

                   return
                    // "<a href='#messageToAdmin' data-id='".$item->id."' data-toggle='modal' class='btn btn-sm btn-warning edit-messageToAdmin'><i class='fa fa-pencil'></i></a>
                    "<button class='btn btn-danger btn-sm btn-delete deleteButton' data-toggle='confirmation' data-singleton='true' value='".$item->id."'><i class='fa fa-remove'></i></button>";

                })
                ->rawColumns(['action'])
                ->make(true);

    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $this->validate($request, [
            'subject' => 'required|string|max:255',
            'message' => 'required|string',
            ]);

        $user = User::find(Auth::user()->id);

        $request['user_id']= $user->id;
        $request['date']= Carbon::now();
        $messageToAdmin = MessageToAdmin::create($request->all());

        return response()->json(['url' => url('/messageToAdminNew')]);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {   
        $input['status']='read';
        $messageToAdmin = MessageToAdmin::find($id)->update($input);

        $data = MessageToAdmin::where('message_to_admin.deleted_at', null)
                    ->join('users', 'message_to_admin.user_id', '=', 'users.id')
                    ->select('message_to_admin.*', 'users.email as user')->find($id);

        return response()->json($data);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $data = MessageToAdmin::find($id);

        return response()->json($data);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $this->validate($request, [
            'subject' => 'required|string|max:255',
            'message' => 'required|string|max:255',
            ]);

        $user = User::find(Auth::user()->id);

        $request['user_id']= $user->id;
        $messageToAdmin = MessageToAdmin::find($id)->update($request->all());

        return response()->json(
            [
                'url' => url('/messageToAdmin'),
                'method' => $request->_method
            ]);
    }
    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {

        $messageToAdmin = MessageToAdmin::destroy($id);

        return response()->json($id);
    }


}
