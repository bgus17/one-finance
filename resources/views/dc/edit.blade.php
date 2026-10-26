@extends('layouts.app')

@section('content')

<div class="card">

```
<div class="card-header">
    <h4>Edit DC</h4>
</div>

<div class="card-body">

    <form action="{{ route('dc.update',$dc->id) }}"
          method="POST">

        @csrf
        @method('PUT')

        <div class="mb-3">

            <label>User Login</label>

            <select name="user_id"
                    class="form-control">

                @foreach($user as $item)

                <option value="{{ $item->id }}"
                    {{ $dc->user_id == $item->id ? 'selected' : '' }}>

                    {{ $item->name }}

                </option>

                @endforeach

            </select>

        </div>

        <div class="mb-3">

            <label>Wilayah</label>

            <select name="wilayah_id"
                    class="form-control">

                @foreach($wilayah as $item)

                <option value="{{ $item->id }}"
                    {{ $dc->wilayah_id == $item->id ? 'selected' : '' }}>

                    {{ $item->nama_wilayah }}

                </option>

                @endforeach

            </select>

        </div>

        <div class="mb-3">

            <label>Nama DC</label>

            <input type="text"
                   name="nama_dc"
                   class="form-control"
                   value="{{ $dc->nama_dc }}">

        </div>

        <div class="mb-3">

            <label>No HP</label>

            <input type="text"
                   name="no_hp"
                   class="form-control"
                   value="{{ $dc->no_hp }}">

        </div>

        <div class="mb-3">

            <label>Alamat</label>

            <textarea name="alamat"
                      class="form-control"
                      rows="3">{{ $dc->alamat }}</textarea>

        </div>

        <div class="mb-3">

            <label>Tanggal Masuk</label>

            <input type="date"
                   name="tanggal_masuk"
                   class="form-control"
                   value="{{ $dc->tanggal_masuk }}">

        </div>

        <div class="mb-3">

            <label>Status</label>

            <select name="status"
                    class="form-control">

                <option value="aktif"
                    {{ $dc->status == 'aktif' ? 'selected' : '' }}>
                    Aktif
                </option>

                <option value="nonaktif"
                    {{ $dc->status == 'nonaktif' ? 'selected' : '' }}>
                    Nonaktif
                </option>

            </select>

        </div>

        <button type="submit"
                class="btn btn-success">

            Update

        </button>

        <a href="{{ route('dc.index') }}"
           class="btn btn-secondary">

            Kembali

        </a>

    </form>

</div>
```

</div>

@endsection
