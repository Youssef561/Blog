@extends('layout.app')

@section('title', 'Create Post')

@section('content')
    <div class="container mt-4">

        <!-- Error Handling -->
        @if($errors->any())
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <strong>⚠️ Oops! Something went wrong.</strong>
                <ul class="mt-2">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        <!-- Create Post Form -->
        <div class="card shadow-sm">
            <div class="card-header bg-primary text-white">
                <h5 class="mb-0">📝 Create a New Post</h5>
            </div>
            <div class="card-body">
                <form method="POST" action="{{ route('posts.store') }}">
                    @csrf

                    <!-- Title Input -->
                    <div class="mb-3">
                        <label for="title" class="form-label fw-bold">Title</label>
                        <input type="text" name="title" class="form-control" id="title" value="{{ old('title') }}" required>
                    </div>

                    <!-- Description Input -->
                    <div class="mb-3">
                        <label for="description" class="form-label fw-bold">Description</label>
                        <textarea class="form-control" name="description" id="description" rows="4" required>{{ old('description') }}</textarea>
                    </div>

                    <!-- Post Creator (Disabled for Auth User) -->
                    <div class="mb-3">
                        <label class="form-label fw-bold">Post Creator</label>
                        <select name="post_creator" class="form-control" disabled>
                            <option value="{{ auth()->user()->id }}">{{ auth()->user()->name }}</option>
                        </select>
                    </div>

                    <!-- Submit Button -->
                    <div class="text-center">
                        <button type="submit" class="btn btn-success px-4">✅ Submit</button>
                        <a href="{{ route('posts.index') }}" class="btn btn-secondary px-4">🔙 Cancel</a>
                    </div>
                </form>
            </div>
        </div>

    </div>
@endsection
