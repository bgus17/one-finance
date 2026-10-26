@extends('layouts.app')

@section('content')

<div class="container">

<div class="card shadow-sm">

    <div class="card-header">

        <h4 class="mb-0">
            Edit Modal
        </h4>

    </div>

    <div class="card-body">

        @if ($errors->any())

            <div class="alert alert-danger">

                <ul class="mb-0">

                    @foreach ($errors->all() as $error)

                        <li>{{ $error }}</li>

                    @endforeach

                </ul>

            </div>

        @endif

        <form action="{{ route('modal.update', $modal->id) }}"
              method="POST">

            @csrf
            @method('PUT')

            <div class="mb-3">

                <label class="form-label">
                    Tanggal
                </label>

                <input
                    type="date"
                    name="tanggal"
                    class="form-control"
                    value="{{ $modal->tanggal }}"
                    required>

            </div>

            <div class="mb-3">

                <label class="form-label">
                    Nominal Modal
                </label>

                <input
                    type="number"
                    name="nominal"
                    class="form-control"
                    value="{{ $modal->nominal }}"
                    required>

            </div>

            <div class="mb-3">

                <label class="form-label">
                    Jenis Modal
                </label>

                <select
                    name="jenis"
                    class="form-control"
                    required>

                    <option value="modal_awal"
                        {{ $modal->jenis == 'modal_awal' ? 'selected' : '' }}>
                        Modal Awal
                    </option>

                    <option value="tambahan_modal"
                        {{ $modal->jenis == 'tambahan_modal' ? 'selected' : '' }}>
                        Tambahan Modal
                    </option>

                </select>

            </div>

            <div class="mb-3">

                <label class="form-label">
                    Keterangan
                </label>

                <textarea
                    name="keterangan"
                    class="form-control"
                    rows="3">{{ $modal->keterangan }}</textarea>

            </div>

            <button
                type="submit"
                class="btn btn-primary">

                Update

            </button>

            <a href="{{ route('modal.index') }}"
               class="btn btn-secondary">

                Kembali

            </a>

        </form>

    </div>

</div>

</div>

@endsection
