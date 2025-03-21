@extends('layout.app')

@section('title', 'Posts Index')

@section('content')
    <div class="container mt-4">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <h2 class="text-primary">All Posts</h2>
            <a href="{{ route('posts.create') }}" class="btn btn-success">➕ Create Post</a>
        </div>

        <div class="table-responsive">
            <table class="table table-striped table-hover text-center">
                <thead class="table-dark">
                <tr>
                    <th>#</th>
                    <th>Title</th>
                    <th>Posted By</th>
                    <th>Created At</th>
                    <th>Actions</th>
                    <th>Likes</th>
                </tr>
                </thead>
                <tbody>
                @forelse ($postsFromDB as $index => $post)
                    <tr>
                        <td>{{ $index + 1 }}</td>
                        <td>{{ $post->title }}</td>
                        <td>{{ $post->user->name }}</td>
                        <td>{{ $post->created_at->format('Y-M-d') }}</td>

                        <td>
                            <a href="{{ route('posts.show', $post->id) }}" class="btn btn-info btn-sm">👁 View</a>

                        @if(auth()->id() === $post->user_id)
                                <a href="{{ route('posts.edit', $post->id) }}" class="btn btn-primary btn-sm">✏ Edit</a>
                                <form action="{{ route('posts.destroy', $post->id) }}" method="POST" class="d-inline">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-danger btn-sm">🗑 Delete</button>
                                </form>
                            @endif
                        </td>

                        <td>
                            <div class="card-body">
                                <form action="{{ route('posts.like', $post) }}" method="POST">
                                    @csrf
                                    @if($post->likes->contains('user_id', auth()->id()))
                                        <button type="submit" class="btn btn-danger">
                                            👎 Unlike ({{ $post->likes->count() }})
                                        </button>
                                    @else
                                        <button type="submit" class="btn btn-primary">
                                            👍 Like ({{ $post->likes->count() }})
                                        </button>
                                    @endif
                                </form>
                            </div>
                        </td>

                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="text-muted">No posts available.</td>
                    </tr>
                @endforelse
                </tbody>
            </table>
        </div>
    </div>
@endsection
