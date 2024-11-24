<?php

namespace App\Http\Controllers;
use Illuminate\Http\Request;
use App\Models\Rayon;
use App\Models\User;

class RayonController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $rayon = Rayon::where('rayon', 'LIKE', '%'.$request->search.'%')->with('user')->orderBy('rayon', 'ASC')->simplePaginate(10);
        return view('admin.rayon.index', compact('rayon'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $user = User::all();
        return view('admin.rayon.create', compact('user'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate ([
            'rayon' => 'required',
            'user' => 'required'
        ]);

        $user = $request->user;
        foreach($user as $key)
        $detailFormat = User::find($key);

        $rayonFormat = [
            "name_user" => $detailFormat['name']
        ];

        $rayon = Rayon::create([
            'rayon' => $request->rayon,
            'user' => $rayonFormat,
        ]);

        if($rayon) {
            return redirect()->route('rayon.index')->with('success', 'Data berhasil ditambahkan!');
        } else {
            return redirect()->back()->with('failed', 'Data gagal di edit');
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
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
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $rombel = Rayon::where('id', $id)->delete();
        if($rombel) {
            return redirect()->back()->with('success' , 'Data berhasil dihapus!');
        } else {
            return redirect()->back()->with('failed', 'Data gagal dihapus');
        }
    }
}