<?php

namespace App\Http\Controllers;

use App\Http\Requests\HadiriUndanganRequest;
use App\Http\Requests\StoreUndanganRequest;
use App\Jobs\SendUndanganNotificationJob;
use App\Models\Role;
use App\Models\Undangan;
use Illuminate\Support\Facades\Auth;

class UndanganController extends Controller
{
    public function index()
    {
        $user = Auth::user();

        $query = Undangan::with('roles', 'menghadiri')->latest('tanggal');

        if (! $user->isAdmin()) {
            $query->untukRole($user->role);
        }

        $undangans = $query->paginate(10)->withQueryString();
        $roles = Role::orderBy('nama')->get();

        return view('undangan.index', [
            'undangans' => $undangans,
            'roles' => $roles,
            'isAdmin' => $user->isAdmin(),
        ]);
    }

    public function store(StoreUndanganRequest $request)
    {
        $undangan = Undangan::create([
            ...$request->safe()->except('bidang_terkait'),
            'created_by' => Auth::id(),
        ]);

        $undangan->roles()->sync($request->input('bidang_terkait', []));

        // Dikirim lewat queue -> tidak memblokir response ke admin yang baru saja submit form.
        SendUndanganNotificationJob::dispatch($undangan);

        return back()->with('notif', ['type' => 'success', 'message' => 'Undangan telah dibuat!']);
    }

    public function update(StoreUndanganRequest $request, Undangan $undangan)
    {
        $undangan->update($request->safe()->except('bidang_terkait'));
        $undangan->roles()->sync($request->input('bidang_terkait', []));

        return back()->with('notif', ['type' => 'success', 'message' => 'Undangan telah diubah!']);
    }

    public function destroy(Undangan $undangan)
    {
        $undangan->delete();

        return back()->with('notif', ['type' => 'warning', 'message' => 'Data berhasil dihapus!']);
    }

    /**
     * Menggantikan handler status di dashboard.php lama (isset($_POST['status'])).
     */
    public function hadiri(HadiriUndanganRequest $request, Undangan $undangan)
    {
        if ($request->hasFile('gambar')) {
            $undangan->bukti_path = $request->file('gambar')->store('bukti-undangan', 'public');
        }

        $undangan->status_kegiatan = 'Terlaksana';
        $undangan->menghadiri_user_id = Auth::id();
        $undangan->delegasi_keterangan = $request->keterangan;
        $undangan->save();

        return back()->with('notif', ['type' => 'success', 'message' => 'Data berhasil disimpan!']);
    }
}
