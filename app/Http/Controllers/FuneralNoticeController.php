<?php

namespace App\Http\Controllers;

use App\Models\FuneralNotice;
use App\Models\Deceased;
use Illuminate\Http\Request;

class FuneralNoticeController extends Controller
{

    public function index()
    {
        $notices = FuneralNotice::with('deceased')->latest()->get();

        return view('funeral.index', compact('notices'));
    }

    public function create()
    {
        $deceaseds = Deceased::all();

        return view('funeral.create', compact('deceaseds'));
    }

    public function store(Request $request)
    {

        $request->validate([

            'deceased_id'=>'required',

            'theme'=>'required',

            'language'=>'required'

        ]);

        $deceased = Deceased::findOrFail($request->deceased_id);

        $announcement =

"It is with deep sorrow that the family announces the passing of ".
$deceased->name.
". Funeral ceremonies shall take place according to the schedule communicated by the family. Friends and loved ones are invited to join in paying their final respects.";

        FuneralNotice::create([

            'deceased_id'=>$deceased->id,

            'announcement'=>$announcement,

            'theme'=>$request->theme,

            'language'=>$request->language

        ]);

        return redirect()->route('funeral.index')
                ->with('success','AI Faire-Part generated successfully.');

    }

}