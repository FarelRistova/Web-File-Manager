@extends('layouts.app')

@section('content')
<div class="container">
    <h1>Upload File</h1>
    <form action="{{ route('manager.files.store', $folder) }}" method="POST" enctype="multipart/form-data">
        @csrf
        <div class="form-group">
            <label for="file">Pilih File</label>
            <input type="file" name="file" id="file" class="form-control" required>
        </div>
        <button type="submit" class="btn btn-primary mt-3">Upload</button>
    </form>
</div>
@endsection
