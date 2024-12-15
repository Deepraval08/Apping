@extends('layouts.main')


@section('content')
    <div class="container">
        <h1 class="text-center">Dashboard</h1>

        <a class="btn btn-primary" href="{{ route('category.index') }}">Manage Category</a>
        <a class="btn btn-primary" href="{{ route('product.index') }}">Manage Product</a>
    </div>
@endsection