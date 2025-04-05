@extends('layouts.admin')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3">Manage Bags</h1>
        <a href="{{ route('bags.create') }}" class="btn btn-primary">
            <i class="bi bi-plus"></i> Add New Bag
        </a>
    </div>

    @if(session('success'))
        <div class="alert alert-success">
            {{ session('success') }}
        </div>
    @endif

    <div class="card shadow">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered">
                    <thead>
                        <tr>
                            <th>Image</th>
                            <th>Name</th>
                            <th>Price</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($bags as $bag)
                        <tr>
                            <td>
                                <img src="{{ asset($bag->image) }}" alt="{{ $bag->name }}" width="50">
                            </td>
                            <td>{{ $bag->name }}</td>
                            <td>${{ number_format($bag->price, 2) }}</td>
                            <td>
                                <a href="{{ route('bags.edit', $bag) }}" class="btn btn-sm btn-primary">
                                    <i class="bi bi-pencil"></i>
                                </a>
                                <form action="{{ route('bags.destroy', $bag) }}" method="POST" class="d-inline">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm('Are you sure?')">
                                        <i class="bi bi-trash"></i>
                                    </button>
                                </form>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            {{ $bags->links() }}
        </div>
    </div>
</div>
@endsection