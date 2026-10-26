@extends('layouts.app')

@section('content')

<div class="card">

    <div class="card-header">
        <h4>Edit Wilayah</h4>
    </div>

    <div class="card-body">

        <form action="/wilayah/update/{{ $wilayah->id }}"
              method="POST">

            @csrf
            @method('PUT')

            <div class="mb-3">
                <label>Nama Wilayah</label>

                <input
                    type="text"
                    name="nama_wilayah"
                    value="{{ $wilayah->nama_wilayah }}"
                    class="form-control"
                    required>
            </div>

            <div class="mb-3">
                <label>Keterangan</label>

                <textarea
                    name="keterangan"
                    class="form-control">{{ $wilayah->keterangan }}</textarea>
            </div>

            <button class="btn btn-primary">
                Update
            </button>

            <a href="/wilayah"
               class="btn btn-secondary">
               Kembali
            </a>

        </form>

    </div>

</div>

@endsection