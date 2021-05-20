<?php

namespace App\Http\Controllers;

use App\Models\Complaint;
use App\Models\Job;
use App\Models\Media;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Config;

class ComplaintsController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $data['title'] = 'Daftar Pengaduan';
        $data['media'] = Media::orderBy('media', 'asc')->get();
        $data['jobs'] = Job::orderBy('pekerjaan', 'asc')->get();

        return view('pengaduan.index', $data);
    }
    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create(Request $request)
    {
        $data['title'] = 'Form Pengaduan';
        $data['media'] = Media::orderBy('media', 'asc')->get();
        $data['jobs'] = Job::orderBy('pekerjaan', 'asc')->get();
        $data['usertamu'] = false;
        if ($request->session()->get('user.level_id') == 9) {
            $data['usertamu'] = $request->session()->get('user');
        }
        // dd(Config::get('toastr'));
        return view('pengaduan.create', $data);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $validator = \Validator::make($request->all(), [
            'name' => 'required',
            'pekerjaan' => 'required',
            'email' => 'nullable|email',
            'media' => 'required',
            'subyek' => 'required',
            'uraian' => 'required',
            'foto_1' => 'image|mimes:jpeg,png,jpg,bmp,gif|max:2048',
            'foto_2' => 'image|mimes:jpeg,png,jpg,bmp,gif|max:2048',
            'foto_3' => 'image|mimes:jpeg,png,jpg,bmp,gif|max:2048',
            'kode_lanjutan' => 'nullable|exists:complaints,kode'
        ], [
            'kode_lanjutan.exists' => 'No. aduan lanjutan tidak tersedia.'

        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()->all()]);
        } else {
            $kode = date('Y-md-His');
            $pict_1 = null;
            $pict_2 = null;
            $pict_3 = null;
            if ($request->foto_1) {
                $pict_1 = $kode . '_1' . '.' . $request->foto_1->extension();
                $request->foto_1->move(public_path('upload-photo'), $pict_1);
            }
            if ($request->foto_2) {
                $pict_2 = $kode . '_2' . '.' . $request->foto_2->extension();
                $request->foto_2->move(public_path('upload-photo'), $pict_2);
            }
            if ($request->foto_3) {
                $pict_3 = $kode . '_3' . '.' . $request->foto_3->extension();
                $request->foto_3->move(public_path('upload-photo'), $pict_3);
            }

            Complaint::create(
                $request->except(['foto_1', 'foto_2', 'foto_3']) +
                    [
                        'kode' => $kode,
                        'status' => 0,
                        'pelapor' => $request->session()->get('user.id'),
                        'pict_1' => $pict_1,
                        'pict_2' => $pict_2,
                        'pict_3' => $pict_3
                    ]
            );
            return response()->json(['success' => 'Data telah disimpan. <br/> No. aduan <br/> ' . $kode]);
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
        //
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
        //
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
}
