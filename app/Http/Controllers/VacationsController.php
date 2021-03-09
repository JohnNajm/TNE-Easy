<?php

namespace App\Http\Controllers;
use Auth;
use App\Models\Vacation;
use App\Models\User;
use Illuminate\Http\Request;

class VacationsController extends Controller
{
    /**
    * Display a listing of the resource.
    *
    * @return \Illuminate\Http\Response
    */
    public function index() // Displays all Vacation requests
    {
        if (request()->status) {
            $vacations = Vacation::all()->where('status', '=', request()->status);
        }else{
        $vacations = Vacation::all();
        }
        return view('list')->with(['vacations'=>$vacations]);
    }
    
    public function individual() // Displays Vacations for user based on the session
    {   
        if (auth::check()){
            $uid = auth()->user()->id;

            if (request()->status) {
                $vacations = Vacation::all()->where('status', '=', request()->status)
                                            ->where('user_id', '=', $uid)->sortby('status');
            }else{
            $vacations = Vacation::all()->where('user_id', '=', $uid)->sortby('status');
            }

            return view('check')->with(['user' => auth()->user(),
                                        'vacations' => $vacations]);
        } else {
            return view('check')->withErrors('You must login to view your vacations');
        }
    }
    
    /**
    * Show the form for creating a new resource.
    *
    * @return \Illuminate\Http\Response
    */
    public function create()
    {
        if (auth::check()){
            $uid = auth()->user()->id;
            $remainderarray = $this->getremaining($uid);

            $remaining = Vacation::all()->where('user_id', '=', $uid)
                                    ->where('status', '<>', 'REJECTED');

            return view('apply')->with(['user' => auth()->user(),
                                        'remaining' => $remaining,
                                        'remainder' => $remainderarray]);
        } else {
            return view('apply')->withErrors('You must login to apply for a  vacation');
        }
    }
    
    /**
    * Store a newly created resource in storage.
    *
    * @param  \Illuminate\Http\Request  $request
    * @return \Illuminate\Http\Response
    */
    public function store(Request $request)
    {
        if ($request->startDate >= $request->endDate){
            return back()->withErrors('Your request is invalid, check dates and try again.');
        }
        if ($request->endDate < date("Y-m-d",time())){
            return back()->withErrors('Your request cannot be in the past, check dates and try again.');
        } 
        if ($this->exceeded($request->remainder, $request->startDate, $request->endDate)){
            return back()->withErrors('Sorry! You have exceeded your 30 days limit.');
        } else {
            $uid = Auth::user()->id;
            
            $vacation = Vacation::create([
                'user_id' => $uid,
                'resolved_by' => NULL,
                'startDate' => $request->startDate,
                'endDate' => $request->endDate,
                ]);
            return back()->with('success_message', "Request sent successfully! Awaiting admin Confirmation");
            }
        }
        
        /**
        * Display the specified resource.
        *
        * @param  int  $id
        * @return \Illuminate\Http\Response
        */
        public function show($id)
        {
            $vacation = Vacation::where('id', '=' , $id)->firstOrFail();
            return $vacation;
        }
        
        public function user($id)
        {
            $user = User::where('id', '=', $id)->firstOrFail();

            if (request()->status) {
                $vacations = Vacation::all()->where('status', '=', request()->status)
                                            ->where('user_id', '=', $id);
            }else{
            $vacations = Vacation::all()->where('user_id', '=', $id);
            }
            return view('listuser')->with(['vacations'=>$vacations,
                                        'user' => $user,]);
        }
        /**
        * Show the form for editing the specified resource.
        *
        * @param  int  $id
        * @return \Illuminate\Http\Response
        */
        public function edit($id)
        {
            //
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
            $uid = auth()->user()->id;
            $vacation = Vacation::find($id);
            
            if ($request->status == "APPROVE"){

                $vacation->status = 'APPROVED';
                $vacation->resolved_by = $uid;
                $vacation->save();
                
                return back()->with('success_message', 'Operation Successful: Vacation Approved');

            }else if($request->status == "REJECT"){     
                
                $vacation->status = 'REJECTED';
                $vacation->resolved_by = $uid;
                $vacation->save();

                return back()->with('success_message', 'Operation Successful: Vacation Rejected');  
            }else{
                return back()->withErrors('Something went wrong, please try again later');  
            }
        }
        
        /**
        * Remove the specified resource from storage.
        *
        * @param  int  $id
        * @return \Illuminate\Http\Response
        */
        public function destroy($id)
        {
            //
        }


        
        protected function exceeded($remainder,$start,$end)
        {
            $remainder = (json_decode($remainder, true));

            $start=date_create($start);
            $end=date_create($end);

            $diff = date_diff($start,$end);

            $year=$start->format('Y');
            // check if year is in array and compare requested days to remaining days
            if(array_key_exists($year, $remainder)){
                if($remainder[$year] - $diff->days >= 0){
                    // requested time is less that the remaining -> OK
                    return FALSE;
                }else{
                    // requested time is more than the remaining -> NO
                    return TRUE;
                }
            }else{

                if ($diff->days > 30){
                    //year not in array but request is more than 30 days -> NO
                    return TRUE;
                }else {
                    //year not in array and request is less than 30 days -> OK
                    echo FALSE;
                }
                
            }

            return FALSE;
        }
        
        protected function getremaining($uid)
        {
            $applied = Vacation::all()->where('user_id', '=', $uid)
                                    ->where('status', '<>', 'REJECTED');
            
            // Default amount of days
            $vacationDays = 30;
            
            $remainderarray=[];
            foreach($applied as $item)
            {
                //check if year exists, if not create year and initialize it
                $year= $item->startDate->year;

                if(!array_key_exists($year, $remainderarray)){
                    $remainderarray[$year] = $vacationDays;
                }
                $diff = date_diff($item->startDate,$item->endDate); 
                
                $remainderarray[$year] = $remainderarray[$year] - $diff->days;
            }
            // returns associative array of the remaining vacation days,
            return $remainderarray;
        }
    }
    