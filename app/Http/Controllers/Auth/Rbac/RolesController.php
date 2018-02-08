<?php

namespace App\Http\Controllers\Auth\Rbac;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;
use YaroslavMolchan\Rbac\Models\Role;

class RolesController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $models = Role::all();


        return View('rbac.roles.index', compact('models'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('rbac.roles.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name'      => 'required|max:255',
            'slug'     => 'required|max:255|unique:roles'
        ]);

        if ($validator->fails()) {
            return back()
                ->withErrors($validator);
        } else {
            $model               = new Role;
            $model->name         = $request->input('name');
            $model->slug        = $request->input('slug');
            $model->save();
            return redirect('rbac/roles')->with('success', 'Successfully created role!');
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
        $model = Role::find($id);

        return view('rbac.roles.show')->with(['model'=> $model]);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $model = Role::find($id);
        return view('rbac.roles.edit')->with(['model'=> $model]);
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
        $validator = Validator::make($request->all(), [
            'name'      => 'required|max:255',
            'slug'     => 'required|max:255'
        ]);

        if ($validator->fails()) {
            return back()
                ->withErrors($validator);
        } else {
            $model = Role::find($id);
            $model->name         = $request->input('name');
            $model->slug        = $request->input('slug');
            $model->save();
            return redirect('rbac/roles')->with('success', 'Successfully created role!');
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
        $model = Role::find($id);
        $model->delete();
        return redirect('rbac/roles')->with('success','role has been  deleted');
    }
}
