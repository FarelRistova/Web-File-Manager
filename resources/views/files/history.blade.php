@extends('layouts.app')

@section('content')
    <div class="container">
        <h1 class="mb-4">Deleted Files</h1>

        <!-- Tombol Back -->
        <a href="{{ isset($folder) ? route('manager.files.index', optional($folder)->id) : route('manager.folders.index') }}"
            class="btn btn-outline-secondary mb-4">
            <i class="fas fa-arrow-left me-2"></i>Back
        </a>

        @if ($files->isEmpty())
            <div class="alert alert-info">No deleted files found.</div>
        @else
            <div class="row">
                @foreach ($files as $file)
                    <div class="col-md-6 mb-4">
                        <div class="card shadow-sm">
                            <div class="card-body">
                                <h5 class="card-title">{{ $file->name }}</h5>
                                <p class="card-text text-muted">
                                    <small>Deleted at: {{ $file->deleted_at->format('d M Y H:i') }}</small>
                                </p>

                                <div class="d-flex justify-content-end gap-2">
                                    <!-- Tombol Restore -->
                                    <form action="{{ route('manager.files.restore', $file->id) }}" method="POST"
                                        class="d-inline">
                                        @csrf
                                        @method('PATCH')
                                        <button type="submit" class="btn btn-sm btn-success">
                                            <i class="fas fa-undo"></i> Restore
                                        </button>
                                    </form>

                                    <!-- Tombol Force Delete dengan Modal -->
                                    <button type="button" class="btn btn-sm btn-danger" data-bs-toggle="modal"
                                        data-bs-target="#deleteModal"
                                        onclick="setDeleteAction('{{ route('manager.files.forceDelete', $file->id) }}')">
                                        <i class="fas fa-trash"></i> Delete Permanently
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        @endif
    </div>

    <!-- Modal Konfirmasi Penghapusan Permanen -->
    <div class="modal fade" id="deleteModal" tabindex="-1" aria-labelledby="deleteModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="deleteModalLabel">Confirm Permanent Deletion</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    Are you sure you want to permanently delete this file? This action cannot be undone.
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <form id="deleteForm" method="POST">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-danger">Delete Permanently</button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script>
        function setDeleteAction(action) {
            document.getElementById('deleteForm').action = action;
        }
    </script>
@endsection
