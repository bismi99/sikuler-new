<?php

namespace App\Http\Controllers;

use App\Models\Proposal;
use Auth;
use Illuminate\Http\Request;

class ProposalController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        if (Auth::user()->role == 'Admin') {
            $items = Proposal::orderBy('id', 'DESC')->paginate(5);
        }else{
            $items = Proposal::where('ekskul', Auth::user()->profile->ekskul)->orderBy('id', 'DESC')->paginate(5);
        }
        return view('pages.admin.proposal.index', compact('items'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('pages.admin.proposal.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $request->validate([
            'ekskul' => ['required', 'string', 'max:255'],
            'title' => ['required', 'string', 'max:255'],
            'file' => ['mimes:doc,docx,ppt,pptx,pdf'],
            'description' => ['required'],
        ]);
        $ekskul = $request->ekskul;
        $title = $request->title;
        $description = $request->description;
        if (request()->hasFile('file')) {
            $file = request()->file('file');
            $files = md5($file->getClientOriginalName() . time()) . "." . $file->getClientOriginalExtension();
            $file->move('./file', $files);
        }
        // dd($request);
        Proposal::create([
            'ekskul' => $ekskul,
            'title' => $title,
            'description' => $description,
            'file' => $files

        ]);

        return redirect()->route('proposal.index')->with('success', 'Tambah data sukses.');
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {

    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $item = Proposal::findOrFail($id);
        return view('pages.admin.proposal.edit', compact('item'));
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
        $proposal = Proposal::findOrFail($id);
        $request->validate([
            'ekskul' => ['required'],
            'title' => ['required', 'string', 'max:255'],
            'file' => ['mimes:doc,docx,ppt,pptx,pdf'],
            'description' => ['required'],
        ]);
        $proposal->ekskul = $request->ekskul;
        $proposal->title = $request->title;
        $proposal->description = $request->description;
        if (request()->hasFile('file')) {
            $file = request()->file('file');
            $files = md5($file->getClientOriginalName() . time()) . "." . $file->getClientOriginalExtension();
            $file->move('./file', $files);
            $proposal['file'] = "$files";
        }

        if($proposal->save()){
            return redirect()->route('proposal.index')
            ->with('success', 'edit data successfully.');
        }else{
            return redirect()->route('proposal.index')
            ->with('danger', 'gagal edit data.');
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
        $item = Proposal::findOrFail($id);
        $item->delete();
        return redirect()->route('proposal.index')
                        ->with('success','berhasil menghapus proposal.');
    }

}
