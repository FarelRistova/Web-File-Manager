<?php

namespace App\Http\Controllers;

use App\Models\File;
use App\Models\Folder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class FileController extends Controller
{

    public function __construct()
    {
        $this->middleware('auth'); // Memastikan user harus login
    }

    public function index($folderid)
    {
        if (!Auth::check()) {
            return redirect()->route('login');
        }

        $folder = Folder::where('id', $folderid)
            ->where('user_id', Auth::id())
            ->firstOrFail();

        $files = File::where('folder_id', $folderid)
            ->where('user_id', Auth::id())
            ->get();

        return view('files.index', compact('files', 'folder'));
    }

    // Menampilkan form upload file
    public function create($folderid)
    {
        $folder = Folder::where('id', $folderid)
            ->where('user_id', Auth::id())
            ->firstOrFail();

        return view('files.create', compact('folder'));
    }

    public function store(Request $request, $folderid)
    {
        $request->validate([
            'file' => 'required|file',
        ]);

        $folder = Folder::where('id', $folderid)
            ->where('user_id', Auth::id())
            ->firstOrFail();

        $file = $request->file('file');

        $path = $file->store('uploads/' . $folderid, 'public');

        File::create([
            'name' => $file->getClientOriginalName(),
            'path' => $path,
            'size' => $file->getSize(),
            'user_id' => Auth::id(),
            'folder_id' => $folderid,
        ]);

        return redirect()->route('manager.files.index', $folderid)
            ->with('success', 'File uploaded successfully.');
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'new_name' => 'required|string|max:255',
        ]);

        $file = File::findOrFail($id);

        // Ambil informasi nama file tanpa ekstensi
        // $originalName = pathinfo($file->name, PATHINFO_FILENAME);
        $extension = pathinfo($file->name, PATHINFO_EXTENSION);

        // Hindari menambahkan ekstensi berulang kali
        $newNameWithoutExt = pathinfo($request->new_name, PATHINFO_FILENAME);

        // Gabungkan nama baru dengan ekstensi lama
        $newFileName = $newNameWithoutExt . ($extension ? '.' . $extension : '');

        // Perbarui nama file di database
        $file->name = $newFileName;
        $file->save();

        return redirect()->back()->with('success', 'Nama file berhasil diperbarui!');
    }


    // Menghapus file
    public function destroy(File $file)
    {
        if ($file->user_id !== Auth::id()) {
            abort(403, 'Unauthorized action.');
        }

        Storage::disk('public')->delete($file->path);

        $file->delete();

        return redirect()->route('manager.files.index', $file->folder_id)
            ->with('error', 'File deleted successfully.');
    }

    public function restoreFile($id)
    {
        $file = File::withTrashed()
            ->where('id', $id)
            ->where('user_id', Auth::id())
            ->firstOrFail();

        $file->restore();

        return redirect()->route('manager.files.index', $file->folder_id)
            ->with('success', 'File restored successfully.');
    }

    public function forceDeleteFile($id)
    {
        $file = File::withTrashed()
            ->where('id', $id)
            ->where('user_id', Auth::id())
            ->firstOrFail();

        Storage::disk('public')->delete($file->path);

        $file->forceDelete();

        return redirect()->route('manager.files.index', $file->folder_id)
            ->with('success', 'File permanently deleted successfully.');
    }

    public function historyFile()
    {
        // Ambil semua file yang sudah dihapus (soft delete)
        $files = File::onlyTrashed()->where('user_id', Auth::id())->get();

        // Pastikan ada folder yang bisa digunakan untuk tombol Back
        $folder = $files->first()?->folder ?? Folder::where('user_id', Auth::id())->first();

        // Tampilkan view history file
        return view('files.history', compact('files', 'folder'));
    }

    public function download($id)
    {
        $file = File::findOrFail($id);
        $filePath = storage_path("app/public/" . $file->path);

        // Debugging: Cek apakah file ada
        if (!file_exists($filePath)) {
            dd("File tidak ditemukan: " . $filePath);
        }

        return response()->download($filePath, $file->name);
    }
}
