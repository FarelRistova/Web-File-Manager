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
                    <h1 class="h3 mb-0 text-gray-800">{{ $folder->name }}</h1>
                    <div class="btn-group">
                        <button type="button" class="btn btn-primary" data-bs-toggle="modal"
                            data-bs-target="#createFileModal">
                            <i class="fas fa-plus me-2"></i>Create File
                        </button>
                        <a href="{{ route('manager.folders.index', $folder) }}" class="btn btn-outline-primary">
                            <i class="fas fa-arrow-left me-2"></i>Back
                        </a>
                        <a href="{{ route('manager.files.history') }}" class="btn btn-outline-primary">
                            <i class="fas fa-history me-2"></i>History
                        </a>
                    </div>
                </div>

                <div class="table-responsive">
                    <table class="table table-hover align-middle" id="filesTable">
                        <thead class="table-light">
                            <tr>
                                <th class="fw-semibold">File Name</th>
                                <th class="fw-semibold">Size</th>
                                <th class="fw-semibold">Upload Date</th>
                                <th class="fw-semibold">Actions</th>
                                <th class="fw-semibold">Preview</th>
                            </tr>
                        </thead>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <!-- Rename File Modals -->
    @foreach ($files as $file)
        <div class="modal fade" id="renameFileModal{{ $file->id }}" tabindex="-1"
            aria-labelledby="renameFileModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Rename File</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <form action="{{ route('manager.files.update', $file->id) }}" method="POST">
                        @csrf
                        @method('PUT')
                        <div class="modal-body">
                            <div class="mb-3">
                                <label for="new_name" class="form-label">New Name</label>
                                <input type="text" class="form-control" name="new_name" value="{{ $file->name }}"
                                    required>
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

    <!-- Create File Modal -->
    <div class="modal fade" id="createFileModal" tabindex="-1" aria-labelledby="createFileModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="createFileModalLabel">Create New File</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form action="{{ route('manager.files.store', ['folder' => $folder->id]) }}" method="POST"
                    enctype="multipart/form-data">
                    @csrf
                    <div class="modal-body">
                        <div class="mb-3">
                            <label for="file" class="form-label">Choose File</label>
                            <input type="file" class="form-control" id="file" name="file" required>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-primary">Upload</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    {{-- Auto Hide Alert --}}
    <script>
        $(document).ready(function() {

            $('#filesTable').DataTable({
                processing: true,
                serverSide: true,
                ajax: "{{ route('manager.datatableFile', $folder->id) }}",
                columns: [{
                        data: 'name',
                        name: 'name'
                    },
                    {
                        data: 'size',
                        name: 'size',
                        render: function(data) {
                            return (data / 1000).toFixed() + ' KB';
                        }
                    },
                    {
                        data: 'created_at',
                        name: 'created_at',
                        render: function(data) {
                            return new Date(data).toLocaleDateString('id-ID');
                        }
                    },
                    {
                        data: 'actions',
                        name: 'actions',
                        orderable: false,
                        searchable: false,
                        render: function(data, type, row) {
                            return `
                            <div class="dropdown">
                                <button class="btn btn-sm btn-outline-secondary dropdown-toggle" type="button"
                                    id="dropdownMenuButton${row.id}" data-bs-toggle="dropdown"
                                    aria-expanded="false">
                                    Actions
                                </button>
                                <ul class="dropdown-menu shadow-sm"
                                    aria-labelledby="dropdownMenuButton${row.id}">
                                    <li>
                                        <button class="dropdown-item" data-bs-toggle="modal"
                                            data-bs-target="#renameFileModal${row.id}">
                                            <i class="fas fa-edit text-warning me-2"></i>Rename
                                        </button>
                                    </li>
                                    <li><hr class="dropdown-divider"></li>
                                    <li>
                                        <form action="{{ route('manager.files.destroy', '') }}/${row.id}" method="POST">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="dropdown-item">
                                                <i class="fas fa-trash text-danger me-2"></i>Delete
                                            </button>
                                        </form>
                                    </li>
                                    <li><hr class="dropdown-divider"></li>
                                    <li>
                                        <a href="{{ route('manager.files.download', '') }}/${row.id}" class="dropdown-item">
                                            <i class="fas fa-download text-success me-2"></i>Download
                                        </a>
                                    </li>
                                </ul>
                            </div>`;
                        }
                    },
                    {
                        data: 'preview',
                        name: 'preview',
                        orderable: false,
                        searchable: false,
                        render: function(data, type, row) {
                            return `<a href="{{ Storage::url('${row.path}') }}" target="_blank" class="btn btn-sm btn-outline-primary">
                        <i class="fas fa-eye me-2"></i>View File
                    </a>`;
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
