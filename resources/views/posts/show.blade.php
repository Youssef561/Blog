@extends('layout.app')

@section('title', 'Post Details')

@section('content')
    <div class="container mt-4">

        <!-- Post Information -->
        <div class="card shadow-sm mb-4">
            <div class="card-header bg-primary text-white">
                <h5 class="mb-0">📌 Post Details</h5>
            </div>
            <div class="card-body">
                <h4 class="card-title">{{ $post->title }}</h4>
                <p class="card-text">{{ $post->description }}</p>
            </div>
        </div>

        <!-- Post Creator Information -->
        <div class="card shadow-sm mb-4">
            <div class="card-header bg-secondary text-white">
                <h5 class="mb-0">👤 Author Details</h5>
            </div>
            <div class="card-body">
                <p><strong>Name:</strong> {{ $post->user->name }}</p>
                <p><strong>Email:</strong> {{ $post->user->email }}</p>
                <p><strong>Account Created At:</strong> {{ $post->user->created_at->format('Y-M-d') }}</p>
            </div>
        </div>

        <!-- Like Button -->
        <div class="text-center mb-4">
            <form action="{{ route('posts.like', $post) }}" method="POST">
                @csrf
                @if($post->likes->contains('user_id', auth()->id()))
                    <button type="submit" class="btn btn-danger btn-lg">
                        👎 Unlike ({{ $post->likes->count() }})
                    </button>
                @else
                    <button type="submit" class="btn btn-primary btn-lg">
                        👍 Like ({{ $post->likes->count() }})
                    </button>
                @endif
            </form>
        </div>

        <!-- Comment Form (Only Authenticated Users) -->
        @auth
            <div class="card shadow-sm mb-4">
                <div class="card-header bg-success text-white">
                    <h5 class="mb-0">💬 Leave a Comment</h5>
                </div>
                <div class="card-body">
                    <form action="{{ route('comments.store', $post->id) }}" method="POST">
                        @csrf
                        <div class="mb-3">
                            <label for="comment" class="form-label">Your Comment</label>
                            <textarea name="content" id="comment" class="form-control" rows="3" required></textarea>
                        </div>
                        <button type="submit" class="btn btn-success">Post Comment</button>
                    </form>
                </div>
            </div>
        @endauth

        <!-- Display Comments -->
        <div class="card shadow-sm">
            <div class="card-header bg-dark text-white">
                <h5 class="mb-0">🗨️ Comments ({{ $post->comments->count() }})</h5>
            </div>
            <div class="card-body">
                @forelse ($post->comments as $comment)
                    <div class="mb-3 border-bottom pb-2">
                        <strong class="text-primary">{{ $comment->user->name }}</strong> said:
                        <p>{{ $comment->content }}</p>
                        <small class="text-muted">{{ $comment->created_at->diffForHumans() }}</small>
                    </div>
                @empty
                    <p class="text-muted">No comments yet. Be the first to comment!</p>
                @endforelse
            </div>
        </div>

    </div>
@endsection
