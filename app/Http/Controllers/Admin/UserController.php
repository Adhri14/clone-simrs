<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use RealRashid\SweetAlert\Facades\Alert;
use Spatie\Permission\Models\Role;

class UserController extends Controller
{
    public function index(Request $request)
    {
        $users_total = User::count();
        $users = User::with(['roles'])
            ->latest()
            ->where(function ($query) use ($request) {
                $query->where('name', "like", "%" . $request->search . "%");
            })
            ->simplePaginate();
        $roles = Role::pluck('name');
        return view('admin.user_index', compact([
            'request',
            'users_total',
            'users',
            'roles',
        ]));
    }
    public function edit(User $user)
    {
        $roles = Role::pluck('name');
        return view('admin.user_edit', compact(['user', 'roles']));
    }
    public function store(Request $request)
    {
        $request->validate([
            'username' => 'required|alpha_dash|unique:users,username,' . $request->id,
            'email' => 'required|email|unique:users,email,' . $request->id,
            'name' => 'required',
            'role' => 'required',
        ]);
        if (!empty($request['password'])) {
            $request['password'] = Hash::make($request['password']);
        } else {
            $request = Arr::except($request, array('password'));
        }
        $user = User::updateOrCreate(['id' => $request->id], $request->except(['_token', 'id', 'role']));
        DB::table('model_has_roles')->where('model_id', $request->id)->delete();
        $user->assignRole($request->role);
        Alert::success('Success', 'Data User Disimpan');
        return redirect()->route('user.index');
    }
    public function destroy(User $user)
    {
        $user->delete();
        Alert::success('Success', 'Data Telah Dihapus');
        return redirect()->route('user.index');
    }
    public function profile()
    {
        $user = Auth::user();
        $roles = Role::pluck('name', 'name')->all();
        $userRole = $user->roles->pluck('name', 'name')->all();
        // $genders = Gender::pluck('name', 'name')->all();
        // $agamas = Agama::pluck('name', 'name')->all();
        // $perkawinans = Perkawinan::pluck('name', 'name')->all();
        // $provinces = Province::pluck('name', 'id');
        // $cities = City::where('province_code', $user->province_id)->pluck('name', 'id')->all();
        // $districts = District::where('city_code', $user->city_id)->pluck('name', 'id')->all();
        // $villages = Village::where('district_code', $user->district_id)->pluck('name', 'id')->all();
        return view('admin.user_profile', compact(
            'user',
            'roles',
            'userRole',
        ));
    }
    public function profile_update(Request $request)
    {
        $user = Auth::user();
        $request->validate([
            'name' => 'required',
            'email' => 'unique:users,email,' . $user->id,
            'username' => 'required|alpha_dash|unique:users,username,' . $user->id,
        ]);
        $user->update($request->all());
        Alert::success('Success', 'Data Telah Disimpan');
        return redirect()->route('profil');
    }
    public function user_verifikasi(User $user, Request $request)
    {
        $user->update([
            'email_verified_at' => Carbon::now(),
            'user_id' => Auth::user()->id,
        ]);
        $wa = new WhatsappController();
        $request['message'] = "*Verifikasi Akun SIMRS WALED* \nAkun anda telah diverifikasi. Data akun anda sebagai berikut.\n\nNAMA : " . $user->name . "\nPHONE : " . $user->phone . "\nEMAIL : " . $user->email . "\n\nSilahkan gunakan akun ini baik-baik.";
        $request['number'] = $user->phone;
        $wa->send_message($request);
        Alert::success('Success', 'Akun telah diverifikasi');
        return back();
    }
}
