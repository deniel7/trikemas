<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Requests;
use Datatables;
use Flash;
use Auth;
use Hash;
use DB;
use App\SysUser;
use App\SysUserAttribute;
use App\SysMenu;
use App\SysRole;
use App\SysRoleDtl;
use File;

class SystemController extends Controller
{
    
    /**
     * Displays datatables front end view
     *
     * @return \Illuminate\View\View
     */
    public function index() {
        //
    }
    
    // --- ROLE ---
    
    public function indexRole() {
        if (in_array(900, session()->get('allowed_menus'))) {
            return view('system.role.index');
        }
        else {
            //
        }
    }
    
    /**
     * Process datatables ajax request.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function datatablesRole() {
        $list = SysRole::select('id', 'rolename', 'description');
        
        return Datatables::of($list)
                ->addColumn('action', function ($list) {
                    if ($list->rolename != 'Administrator') {
                        $html  = '<div class="text-center btn-group btn-group-justified">';
                        $html .= '<a href="/system/role/detail/' . $list->id . '" title="Detail"><button type="button" class="btn btn-sm bg-purple"><i class="fa fa-search"></i></button></a> '; 
                        $html .= '<a href="/system/role/edit/' . $list->id . '" title="Edit"><button type="button" class="btn btn-sm btn-warning"><i class="fa fa-pencil"></i></button></a> '; 
                        $html .= '<a href="/system/role/delete/' . $list->id . '" title="Delete" onclick="confirmDeleteRole(event, \'' . $list->id . '\', \'' . $list->rolename . '\');"><button type="button" class="btn btn-sm btn-danger"><i class="fa fa-trash"></i></button></a>';
                        $html .= '</div>';
                    }
                    else {
                        $html  = '<div class="text-center btn-group btn-group-justified">';
                        $html .= '<a href="/system/role/detail/' . $list->id . '" title="Detail"><button type="button" class="btn btn-sm bg-purple"><i class="fa fa-search"></i></button></a> '; 
                        $html .= '</div>';
                    }
                    return $html;
                })
                ->make(true);
    }
    
    // detail view
    public function detailRole($id) {
        if (in_array(900, session()->get('allowed_menus'))) {
            $role = SysRole::find($id);
            $detail = $role->detail()->orderBy('id_menu')->get();
            $count = count($detail);
            $firstSectionCount = ceil($count / 2);
            
            $data['role'] = $role;
            $data['detail'] = $detail;
            $data['count'] = $count;
            $data['firstSectionCount'] = $firstSectionCount;
            
            return view('system.role.detail', $data);
        }
        else {
            //
        }
    }
    
    // add view
    public function addRole() {
        if (in_array(900, session()->get('allowed_menus'))) {
            $menus = SysMenu::select('id_menu', 'title')->orderBy('id_menu')->get();
            $count = count($menus);
            $firstSectionCount = ceil($count / 2);
            
            $data['menus'] = $menus;
            $data['count'] = $count;
            $data['firstSectionCount'] = $firstSectionCount;
            
            return view('system.role.add', $data);
        }
        else {
            //
        }
    }
    
    // edit view
    public function editRole($id) {
        if (in_array(900, session()->get('allowed_menus'))) {
            $menus = SysMenu::select('id_menu', 'title')->orderBy('id_menu')->get();
            $count = count($menus);
            $firstSectionCount = ceil($count / 2);
            $role = SysRole::find($id);
            
            $data['menus'] = $menus;
            $data['count'] = $count;
            $data['firstSectionCount'] = $firstSectionCount;
            $data['role'] = $role;
            $data['role_dtl'] = $role->detailMenu();
            
            return view('system.role.edit', $data);
        }
        else {
            //
        }
    }
    
    // do save
    public function saveRole(Request $request) {
        $count = SysRole::where('rolename', $request->name)->count();
        
        // check exist
        if ($count > 0) {
            Flash::error('Error: Rolename ' . $request->name . ' already exist.');
            return redirect('/system/role/add')->withInput();
        }
        else {
            DB::beginTransaction();
            try {
                // insert header
                $role = new SysRole;
                $role->rolename = $request->name;
                $role->description = $request->description;
                $role->created_by = Auth::check() ? Auth::user()->username : '';
                $role->save();
                
                $curId = $role->id;
                
                // insert detail
                foreach($request->menu as $menu) {
                    $role_dtl = new SysRoleDtl;
                    $role_dtl->id_hdr = $curId;
                    $role_dtl->id_menu = $menu;
                    $role_dtl->save();
                }
                
                DB::commit();
                return redirect('/system/role');
            }
            catch(\Illuminate\Database\QueryException $e) { 
                Flash::error('Error (' . $e->errorInfo[1] . '): ' . $e->errorInfo[2] . '.');
                
                DB::rollback();
                return redirect('/system/role/add')->withInput();
            }
        }
    }
    
    // do update
    public function updateRole($id, Request $request) {
        $count = SysRole::where('rolename', $request->name)->count();
        $role = SysRole::find($id);
        
        // check exist
        if ($count == 0 || ($count == 1 && $role->rolename == $request->name)) {
            DB::beginTransaction();
            try {
                
                // delete detail first
                SysRoleDtl::where('id_hdr', $id)->delete();
                
                // update header
                $role->rolename = $request->name;
                $role->description = $request->description;
                $role->updated_by = Auth::check() ? Auth::user()->username : '';
                $role->save();
                
                // insert detail
                foreach($request->menu as $menu) {
                    $role_dtl = new SysRoleDtl;
                    $role_dtl->id_hdr = $id;
                    $role_dtl->id_menu = $menu;
                    $role_dtl->save();
                }
                
                DB::commit();
                return redirect('/system/role');
            }
            catch(\Illuminate\Database\QueryException $e) { 
                Flash::error('Error (' . $e->errorInfo[1] . '): ' . $e->errorInfo[2] . '.');
                
                DB::rollback();
                return redirect('/system/role/edit/' . $id)->withInput();
            }
        }
        else {
            Flash::error('Error: Rolename ' . $request->name . ' already exist.');
            return redirect('/system/role/edit/' . $id)->withInput();
        }
    }
    
    // do delete
    public function deleteRole($id) {
        $role = SysRole::find($id);
        
        DB::beginTransaction();
        try {
            // delete detail
            SysRoleDtl::where('id_hdr', $id)->delete();
            
            // delete header
            $role->delete();
            DB::commit();
            echo 'success';
        }
        catch(\Illuminate\Database\QueryException $e) {
            DB::rollback();
            echo 'Error (' . $e->errorInfo[1] . '): ' . $e->errorInfo[2] . '.';
        }
    }
    
    
    // --- USER ---
    
    public function indexUser() {
        if (in_array(900, session()->get('allowed_menus'))) {
            return view('system.user.index');
        }
        else {
            //
        }
    }
    
    /**
     * Process datatables ajax request.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function datatablesUser() {
        $list = SysUser::select('id', 'username', 'name', 'email', 'rolename', 'active');
        
        return Datatables::of($list)
                ->addColumn('action', function ($list) {
                    if ($list->username != 'admin') {
                        $html  = '<div class="text-center btn-group btn-group-justified">';
                        $html .= '<a href="/system/user/edit/' . $list->id . '" title="Edit"><button type="button" class="btn btn-sm btn-warning"><i class="fa fa-pencil"></i></button></a> '; 
                        $html .= '<a href="/system/user/delete/' . $list->id . '" title="Delete" onclick="confirmDeleteUser(event, \'' . $list->id . '\', \'' . $list->username . '\');"><button type="button" class="btn btn-sm btn-danger"><i class="fa fa-trash"></i></button></a>';
                        $html .= '</div>';
                    }
                    else {
                        $html  = '<div class="text-center btn-group btn-group-justified">';
                        $html .= '</div>';
                    }
                    return $html;
                })
                ->editColumn('active', function($list) {
                    return $list->active ? 'Y' : 'N';
                })
                ->make(true);
    }
    
    // add view
    public function addUser() {
        if (in_array(900, session()->get('allowed_menus'))) {
            $data['role'] = SysRole::select('id', 'rolename')->orderBy('rolename')->get();
            
            return view('system.user.add', $data);
        }
        else {
            //
        }
    }
    
    // edit view
    public function editUser($id) {
        if (in_array(900, session()->get('allowed_menus'))) {
            $data['role'] = SysRole::select('id', 'rolename')->orderBy('rolename')->get();
            $data['user'] = SysUser::find($id);
            
            // foto
            $foto = '';
            $user_attributes = SysUserAttribute::where('id_user', $id)->where('attribute_name', 'photo')->get();
            if ($user_attributes->count() > 0) {
                $user_attribute = $user_attributes->first();
                $foto = $user_attribute->attribute_value;
            }
            $data['foto'] = $foto;
            
            return view('system.user.edit', $data);
        }
        else {
            //
        }
    }
    
    // do save
    public function saveUser(Request $request) {
        $count = SysUser::where('username', $request->username)->count();
        
        // check exist
        if ($count > 0) {
            Flash::error('Error: Username ' . $request->username . ' already exist.');
            return redirect('/system/user/add')->withInput();
        }
        else {
            DB::beginTransaction();
            try {
                $user = new SysUser;
                $user->username = $request->username;
                $user->name = $request->name;
                $user->email = $request->email;
                $user->rolename = $request->rolename;
                $user->active = $request->active;
                $user->password = Hash::make($request->password);
                $user->created_by = Auth::check() ? Auth::user()->username : '';
                $user->save();
                
                // upload file => foto
                $newFileName = "";
                // Verifying File Presence 
                if ($request->hasFile('foto')) {
                    // Validating Successful Uploads
                    if ($request->file('foto')->isValid()) {
                        $ext = $request->file('foto')->getClientOriginalExtension();
                        $newFileName = sha1_file($request->file('foto')) . '.' . $ext;
                        $request->file('foto')->move(config('constants.user.photo'), $newFileName);
                    
                        $user_attribute = new SysUserAttribute;
                        $user_attribute->id_user = $user->id;
                        $user_attribute->attribute_name = 'photo';
                        $user_attribute->attribute_value = $newFileName;
                        $user_attribute->save();
                    }
                }
                
                DB::commit();
                return redirect('/system/user');
            }
            catch(\Illuminate\Database\QueryException $e) { 
                Flash::error('Error (' . $e->errorInfo[1] . '): ' . $e->errorInfo[2] . '.');
                
                DB::rollback();
                return redirect('/system/user/add')->withInput();
            }
        }
    }
    
    // do update
    public function updateUser($id, Request $request) {
        $count = SysUser::where('username', $request->username)->count();
        $user = SysUser::find($id);
        
        // check exist
        if ($count == 0 || ($count == 1 && $user->username == $request->username)) {
            DB::beginTransaction();
            try {
                $user->username = $request->username;
                $user->name = $request->name;
                $user->email = $request->email;
                $user->rolename = $request->rolename;
                $user->active = $request->active;
                if ($request->password) {
                    $user->password = Hash::make($request->password);
                }
                $user->updated_by = Auth::check() ? Auth::user()->username : '';
                $user->save();
                
                $user_attributes = SysUserAttribute::where('id_user', $id)->where('attribute_name', 'photo')->get();
                
                // Verifying File Presence => foto
                if ($request->hasFile('foto')) {
                    
                    // Validating Successful Uploads
                    if ($request->file('foto')->isValid()) {
                        $ext = $request->file('foto')->getClientOriginalExtension();
                        $newFileName = sha1_file($request->file('foto')) . '.' . $ext;
                        $request->file('foto')->move(config('constants.user.photo'), $newFileName);
                            
                        // change photo
                        if ($user_attributes->count() > 0) {
                            $user_attribute = $user_attributes->first();
                            $foto = $user_attribute->attribute_value;
                            
                            // delete original file first
                            if ($foto && File::exists(config('constants.user.photo') . '/' . $foto)) {
                                File::delete(config('constants.user.photo') . '/' . $foto);    
                            }
                            
                            $user_attribute->attribute_value = $newFileName;
                            $user_attribute->save();
                        }
                        // brand new
                        else {
                            $user_attribute = new SysUserAttribute;
                            $user_attribute->id_user = $id;
                            $user_attribute->attribute_name = 'photo';
                            $user_attribute->attribute_value = $newFileName;
                            $user_attribute->save();
                        }
                    }
                }
                else {
                    // remove photo
                    if ($user_attributes->count() > 0) {
                        $user_attribute = $user_attributes->first();
                        $foto = $user_attribute->attribute_value;
                        
                        if ($foto != $request->foto_asal) {
                            // delete original file first
                            if ($foto && File::exists(config('constants.user.photo') . '/' . $foto)) {
                                File::delete(config('constants.user.photo') . '/' . $foto);    
                            }
                            $user_attribute->attribute_value = $request->foto_asal;
                            $user_attribute->save();
                        }
                    }
                }
                
                DB::commit();
                return redirect('/system/user');
            }
            catch(\Illuminate\Database\QueryException $e) { 
                Flash::error('Error (' . $e->errorInfo[1] . '): ' . $e->errorInfo[2] . '.');
                
                DB::rollback();
                return redirect('/system/user/edit/' . $id)->withInput();
            }
        }
        else {
            Flash::error('Error: Username ' . $request->username . ' already exist.');
            return redirect('/system/user/edit/' . $id)->withInput();
        }
    }
    
    // do delete
    public function deleteUser($id) {
        $user = SysUser::find($id);
        
        DB::beginTransaction();
        try {
            $user->delete();
            
            // foto
            $user_attributes = SysUserAttribute::where('id_user', $id)->where('attribute_name', 'photo')->get();
            // delete foto
            if ($user_attributes->count() > 0) {
                $user_attribute = $user_attributes->first();
                $foto = $user_attribute->attribute_value;
                            
                // delete file first
                if ($foto && File::exists(config('constants.user.photo') . '/' . $foto)) {
                    File::delete(config('constants.user.photo') . '/' . $foto);    
                }
          
                $user_attribute->delete();
            }
            
            DB::commit();
            echo 'success';
        }
        catch(\Illuminate\Database\QueryException $e) {
            DB::rollback();
            echo 'Error (' . $e->errorInfo[1] . '): ' . $e->errorInfo[2] . '.';
        }
    }
    
}
