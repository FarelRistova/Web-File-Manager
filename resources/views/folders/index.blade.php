@extends('layouts.app')

@section('content')
    <div class="container py-4">
        <div class="card shadow-sm">
            <div class="card-body">

                {{-- Alert Session Success & Error --}}
                @if (session('success'))
                    <div class="alert alert-success alert-dismissible fade show" role="alert">
                        <i class="fas fa-check-circle me-2"></i> {{ session('success') }}
                    </div>
                @endif
                @if (session('error'))
                    <div class="alert alert-danger alert-dismissible fade show" role="alert">
                        <i class="fas fa-exclamation-circle me-2"></i> {{ session('error') }}
                    </div>
                @endif

                <div class="d-flex justify-content-between align-items-center mb-4">
                    <h1 class="h3 mb-0 text-gray-800">Folders</h1>
                    <div class="btn-group">
                        <button type="button" class="btn btn-primary" data-bs-toggle="modal"
                            data-bs-target="#createFolderModal">
                            <i class="fas fa-plus me-2"></i>New Folder
                        </button>
                        <a href="{{ route('manager.folders.history') }}" class="btn btn-outline-primary">
                            <i class="fas fa-history me-2"></i>History
                        </a>
                    </div>
                </div>

                <div class="table-responsive">
                    <table class="table table-hover align-middle" id="foldersTable">
                        <thead class="table-light">
                            <tr>
                                <th class="fw-semibold">Name</th>
                                <th class="fw-semibold text-center">Created At</th>
                                <th class="fw-semibold text-center">Actions</th>
                            </tr>
                        </thead>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <!-- Rename Folder Modals -->
    @foreach ($folders as $folder)
        <div class="modal fade" id="renameFolderModal{{ $folder->id }}" tabindex="-1"
            aria-labelledby="renameFolderModalLabel{{ $folder->id }}" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Rename Folder</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <form action="{{ route('manager.folders.update', $folder->id) }}" method="POST">
                        @csrf
                        @method('PUT')
                        <div class="modal-body">
                            <div class="mb-3">
                                <label for="folderName{{ $folder->id }}" class="form-label">Folder Name</label>
                                <input type="text" class="form-control" id="folderName{{ $folder->id }}" name="name"
                                    value="{{ $folder->name }}" required>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancel</button>
                            <button type="submit" class="btn btn-primary">Update</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    @endforeach

    <!-- Create Folder Modal -->
    <div class="modal fade" id="createFolderModal" tabindex="-1" aria-labelledby="createFolderModalLabel"
        aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="createFolderModalLabel">Create New Folder</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form action="{{ route('manager.folders.store') }}" method="POST">
                    @csrf
                    <div class="modal-body">
                        <div class="mb-3">
                            <label for="folderName" class="form-label">Folder Name</label>
                            <input type="text" class="form-control" id="folderName" name="name" required>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-primary">Create</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <style>
        .cursor-pointer {
            cursor: pointer;
        }

        .btn-icon {
            padding: 0.25rem 0.5rem;
        }
    </style>

    {{-- DataTable Initialization --}}
    <script>
        $(document).ready(function() {
            $('#foldersTable').DataTable({
                processing: true,
                serverSide: true,
                ajax: "{{ route('manager.datatableFolder') }}",
                columns: [{
                        data: 'name',
                        name: 'name',
                        render: function(data, type, row) {
                            return `<div class="ps-4 cursor-pointer" onclick="window.location='{{ route('manager.files.index', '') }}/${row.id}'">
                                <i class="fas fa-folder me-2 text-warning"></i> ${data}
                            </div>`;
                        }
                    },
                    {
                        data: 'created_at',
                        name: 'created_at',
                        className: 'text-center',
                        render: function(data) {
                            return new Date(data).toLocaleDateString('id-ID', {
                                day: '2-digit',
                                month: 'short',
                                year: 'numeric',
                                hour: '2-digit',
                                minute: '2-digit'
                            });
                        }
                    },
                    {
                        data: 'actions',
                        name: 'actions',
                        orderable: false,
                        searchable: false,
                        className: 'text-center',
                        render: function(data, type, row) {
                            return `<div class="d-flex align-items-center justify-content-center" onclick="event.stopPropagation()">
                                <!-- Tombol Rename -->
                                <button type="button" class="btn btn-sm btn-outline-warning me-2"
                                    data-bs-toggle="modal"
                                    data-bs-target="#renameFolderModal${row.id}" title="Rename">
                                    <i class="fas fa-edit me-1"></i>Rename
                                </button>

                                <!-- Tombol Delete -->
                                <form action="{{ route('manager.folders.destroy', '') }}/${row.id}"
                                    method="POST" onsubmit="event.stopPropagation()">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-outline-danger"
                                        title="Delete">
                                        <i class="fas fa-trash me-1"></i>Delete
                                    </button>
                                </form>
                            </div>`;
                        }
                    }
                ]
            });
        });

        // Auto-hide alerts after 3 seconds
        setTimeout(function() {
            $('.alert').fadeOut('fast');
        }, 3000); // 3000ms = 3 detik
    </script>
@endsection
