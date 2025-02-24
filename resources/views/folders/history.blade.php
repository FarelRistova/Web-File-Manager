@extends('layouts.app')

@section('content')
    <div class="container">
        <h1 class="mb-4">Deleted Folders</h1>

        <!-- Tombol Back -->
        <a href="{{ route('manager.folders.index') }}" class="btn btn-outline-secondary mb-4">
            <i class="fas fa-arrow-left me-2"></i> Back
        </a>

        @if ($folders->isEmpty())
            <div class="alert alert-info">
                No deleted folders found.
            </div>
        @else
            <div class="row">
                @foreach ($folders as $folder)
                    <div class="col-md-6 mb-4">
                        <div class="card shadow-sm">
                            <div class="card-body">
                                <h5 class="card-title">
                                    <i class="fas fa-folder text-warning me-2"></i> {{ $folder->name }}
                                </h5>
                                <div class="d-flex justify-content-end">
                                    <!-- Tombol Restore -->
                                    <form action="{{ route('manager.folders.restore', $folder->id) }}" method="POST"
                                        class="me-2">
                                        @csrf
                                        @method('PATCH')
                                        <button type="submit" class="btn btn-success btn-sm">
                                            <i class="fas fa-trash-restore me-1"></i> Restore
                                        </button>
                                    </form>

                                    <!-- Tombol Delete Permanently -->
                                    <button type="button" class="btn btn-danger btn-sm" data-bs-toggle="modal"
                                        data-bs-target="#deleteModal{{ $folder->id }}">
                                        <i class="fas fa-trash-alt me-1"></i> Delete Permanently
                                    </button>

                                    <!-- Modal Konfirmasi Hapus Permanen -->
                                    <div class="modal fade" id="deleteModal{{ $folder->id }}" tabindex="-1"
                                        aria-labelledby="deleteModalLabel{{ $folder->id }}" aria-hidden="true">
                                        <div class="modal-dialog">
                                            <div class="modal-content">
                                                <div class="modal-header">
                                                    <h5 class="modal-title" id="deleteModalLabel{{ $folder->id }}">Confirm
                                                        Deletion</h5>
                                                    <button type="button" class="btn-close" data-bs-dismiss="modal"
                                                        aria-label="Close"></button>
                                                </div>
                                                <div class="modal-body">
                                                    Apakah kamu yakin? folder yang sudah di hapus tidak bisa kembali lagi.
                                                </div>
                                                <div class="modal-footer">
                                                    <button type="button" class="btn btn-secondary"
                                                        data-bs-dismiss="modal">Cancel</button>
                                                    <!-- Form delete permanen (langsung ke route yang benar) -->
                                                    <form action="{{ route('manager.folders.forceDelete', $folder->id) }}"
                                                        method="POST" style="display:inline;">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="submit" class="btn btn-danger">Delete
                                                            Permanently</button>
                                                    </form>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        @endif
    </div>
@endsection
