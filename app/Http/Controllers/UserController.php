<?php

namespace App\Http\Controllers;
use Illuminate\Http\Request;
use App\Models\Pegawai;
use App\Models\Task;
use App\Models\Jabatan;
use App\Models\Cabang;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class UserController extends Controller
{
    public function index(Request $request){
        $cari = $request->cari;
        // $datas = Pegawai::all();
        $user = User::where('name', 'LIKE', '%'.$cari.'%')
            ->paginate(5);
        $user->withPath('user');
        $user->appends($request->all());
        return view('user.home', compact(
            'user', 'cari'
        ));
    }
    
    function validator(Request $user)
    {
        return Validator::make($user, [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'role' => ['required'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
        ]);
    }

    function simpanUser(Request $user)
    {
        return User::create([
            'name' => $user['name'],
            'email' => $user['email'],
            'role' => $user['role'],
            'password' => Hash::make($user['password']),
        ]);

        User::create($validateData);
        session()->flash('pesan',"Penambahan Data {$validateData['nama']} berhasil");
        return redirect(route('user.index'));

    }

    // public function simpanUser(array $request){
    //     $validateData = $request->validate([
    //         'name' => 'required|min:1|max:50',
    //         'email' => 'required',
    //         'password' => Hash::make($request['password']),
    //         'role' => 'required',
    //     ]);
        
    //     User::create($validateData);
    //     session()->flash('pesan',"Penambahan Data {$validateData['name']} berhasil");
    //     return redirect(route('user.index'));
    // }

    
    public function updateUser(Request $request)
    {
        $user = User::where('id', '=', Auth::user()->id)
                    ->update([
                            'name' => $request->name,
                            'email' => $request->email,
                            'password' => Hash::make($request->password),
                            'role' => $request->role,
                    ]);

        return redirect()->route('user.index');
    }


    public function deleteUser($id){
        $data =User::where('id',$id)->first();
        $data->delete();

        return redirect()->route('user.index')->with('msg',"Data {$data['name']} berhasil dihapus" );
    }
}
