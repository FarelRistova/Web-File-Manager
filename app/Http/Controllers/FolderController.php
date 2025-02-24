<?php

namespace App\Http\Controllers;

use App\Models\Folder;
use App\Models\File;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

class FolderController extends Controller
{

    public function __construct()
    {
        $this->middleware('auth'); // Memastikan user harus login
    }

    public function index()
    {
        if (!Auth::check()) {
            return redirect()->route('login');
        }

        $folders = Folder::where('user_id', Auth::id())->get();

        return view('folders.index', compact('folders'));
    }

    // Menampilkan form membuat folder
    public function create()
    {
        return view('folders.create');
    }

    // Menyimpan folder baru
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
        ]);

        Folder::create([
            'name' => $request->name,
            'user_id' => Auth::id(),
        ]);

        return redirect()->route('manager.folders.index')
            ->with('success', 'Folder created successfully.');
    }

    // Fungsi untuk mengupdate nama folder
    public function update(Request $request, $id)
    {
        // Validasi input
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255|unique:folders,name,' . $id,
        ]);

        // Jika validasi gagal, kembalikan pesan error
        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator) // Kirim error ke view
                ->withInput(); // Simpan input sebelumnya
        }

        // Cari folder berdasarkan ID
        $folder = Folder::findOrFail($id);

        // Update nama folder
        $folder->name = $request->name;
        $folder->save();

        // Redirect ke halaman index dengan pesan sukses
        return redirect()->route('manager.folders.index')->with('success', 'Folder renamed successfully.');
    }

    // Menghapus folder
    public function destroy(Folder $folder)
    {
        // Pastikan folder dimiliki oleh user yang sedang login
        if ($folder->user_id !== Auth::id()) {
            abort(403, 'Unauthorized action.');
        }

        // Hapus semua file dalam folder terkait dari storage
        foreach ($folder->files as $file) {
            Storage::disk('public')->delete($file->path);
        }

        // Hapus semua file dalam folder terkait dari database
        $folder->files()->delete();

        // Hapus folder
        $folder->delete();

        return redirect()->route('manager.folders.index')
            ->with('error', 'Folder deleted successfully.');
    }

    // Mengembalikan folder yang dihapus (restore)
    public function restoreFolder($id)
    {
        $folder = Folder::withTrashed()
            ->where('user_id', Auth::id())
            ->findOrFail($id);

        $folder->restore();

        return redirect()->route('manager.folders.history')
            ->with('success', 'Folder restored successfully.');
    }

    // Menghapus folder secara permanen
    public function forceDeleteFolder($id)
    {
        $folder = Folder::withTrashed()
            ->where('user_id', Auth::id())
            ->findOrFail($id);

        // Hapus semua file dalam folder terkait dari storage
        foreach ($folder->files as $file) {
            Storage::disk('public')->delete($file->path);
        }

        // Hapus semua file dalam folder terkait dari database
        $folder->files()->forceDelete();

        // Hapus folder secara permanen
        $folder->forceDelete();

        return redirect()->route('manager.folders.history')
            ->with('success', 'Folder permanently deleted successfully.');
    }

    public function historyFolder()
    {
        // Ambil folder yang dihapus (soft delete) milik user yang sedang login
        $folders = Folder::onlyTrashed()
                         ->where('user_id', Auth::id())
                         ->get();

        return view('folders.history', compact('folders'));
    }
}
