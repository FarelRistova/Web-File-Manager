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

                @if ($folders->isEmpty())
                    <div class="alert alert-info">
                        No deleted folders found.
                    </div>
                @else
                    <div class="table-responsive">
                        <table class="table table-hover align-middle">
                            <thead class="table-light">
                                <tr>
                                    <th class="fw-semibold">Name</th>
                                    <th class="fw-semibold text-center">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($folders as $folder)
                                    <tr class="cursor-pointer"
                                        onclick="window.location='{{ route('manager.files.index', $folder->id) }}'">
                                        <td class="text-nowrap ps-4">
                                            <i class="fas fa-folder me-2 text-warning"></i>
                                            {{ $folder->name }}
                                        </td>
                                        <td>
                                            <div class="d-flex align-items-center justify-content-center"
                                                onclick="event.stopPropagation()">
                                                <!-- Tombol Rename -->
                                                <button type="button" class="btn btn-sm btn-outline-warning me-2"
                                                    data-bs-toggle="modal"
                                                    data-bs-target="#renameFolderModal{{ $folder->id }}" title="Rename">
                                                    <i class="fas fa-edit me-1"></i>Rename
                                                </button>

                                                <!-- Tombol Delete -->
                                                <form action="{{ route('manager.folders.destroy', $folder->id) }}"
                                                    method="POST" onsubmit="event.stopPropagation()">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="btn btn-sm btn-outline-danger"
                                                        title="Delete">
                                                        <i class="fas fa-trash me-1"></i>Delete
                                                    </button>
                                                </form>
                                            </div>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @endif
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
                                <input type="text" class="form-control" id="folderName{{ $folder->id }}"
                                    name="name" value="{{ $folder->name }}" required>
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

    {{-- Auto Hide Alert --}}
    <script>
        setTimeout(() => {
            let alert = document.querySelector('.alert');
            if (alert) {
                alert.classList.add('fade');
                setTimeout(() => alert.remove(), 500);
            }
        }, 3000);
    </script>
@endsection
