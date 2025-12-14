@extends('layouts.app')

@section('title', __('paket.edit_package'))

@section('content')
<div class="row mb-4">
    <div class="col">
        <h2><i class="bi bi-pencil"></i> {{ __('paket.edit_package') }}</h2>
    </div>
</div>

<div class="card">
    <div class="card-body">
        <form method="POST" action="{{ route('paket.update', $paket) }}">
            @csrf
            @method('PUT')
            
            <div class="row mb-4">
                <div class="col-md-6">
                    <label for="kode_resi" class="form-label">
                        {{ __('paket.tracking_code') }} <span class="text-danger">*</span>
                    </label>
                    <input type="text" 
                           class="form-control @error('kode_resi') is-invalid @enderror" 
                           id="kode_resi" 
                           name="kode_resi" 
                           value="{{ old('kode_resi', $paket->kode_resi) }}" 
                           required>
                    @error('kode_resi')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
            </div>

            <div class="row mb-3">
                <div class="col-md-6">
                    <label for="nama_penerima" class="form-label">
                        {{ __('paket.recipient_name') }} <span class="text-danger">*</span>
                    </label>
                    <input type="text" 
                           class="form-control @error('nama_penerima') is-invalid @enderror" 
                           id="nama_penerima" 
                           name="nama_penerima" 
                           value="{{ old('nama_penerima', $paket->nama_penerima) }}" 
                           required>
                    @error('nama_penerima')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                
                <div class="col-md-6">
                    <label for="telepon_penerima" class="form-label">
                        {{ __('paket.recipient_phone') }} <span class="text-danger">*</span>
                    </label>
                    <input type="text" 
                           class="form-control @error('telepon_penerima') is-invalid @enderror" 
                           id="telepon_penerima" 
                           name="telepon_penerima" 
                           value="{{ old('telepon_penerima', $paket->telepon_penerima) }}" 
                           required>
                    @error('telepon_penerima')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
            </div>

            <div class="row mb-3">
                <div class="col-md-6">
                    <label for="wilayah_id" class="form-label">{{ __('paket.region') }}</label>
                    <select class="form-select @error('wilayah_id') is-invalid @enderror" 
                            id="wilayah_id" 
                            name="wilayah_id"
                            onchange="filterAsrama()">
                        <option value="">{{ __('paket.region') }}</option>
                        @foreach($wilayah as $item)
                            <option value="{{ $item->id }}" 
                                    {{ old('wilayah_id', $paket->wilayah_id) == $item->id ? 'selected' : '' }}>
                                {{ $item->nama_wilayah }}
                            </option>
                        @endforeach
                    </select>
                    @error('wilayah_id')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                
                <div class="col-md-6">
                    <label for="asrama_id" class="form-label">{{ __('paket.dormitory') }}</label>
                    <select class="form-select @error('asrama_id') is-invalid @enderror" 
                            id="asrama_id" 
                            name="asrama_id">
                        <option value="">{{ __('paket.dormitory') }}</option>
                        @foreach($asrama as $item)
                            <option value="{{ $item->id }}" 
                                    data-wilayah="{{ $item->wilayah_id }}"
                                    {{ old('asrama_id', $paket->asrama_id) == $item->id ? 'selected' : '' }}>
                                {{ $item->nama_asrama }} ({{ $item->wilayah->nama_wilayah }})
                            </option>
                        @endforeach
                    </select>
                    @error('asrama_id')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
            </div>

            <div class="row mb-3">
                <div class="col-md-6">
                    <label for="nomor_kamar" class="form-label">{{ __('paket.room_number') }}</label>
                    <input type="text" 
                           class="form-control @error('nomor_kamar') is-invalid @enderror" 
                           id="nomor_kamar" 
                           name="nomor_kamar" 
                           value="{{ old('nomor_kamar', $paket->nomor_kamar) }}">
                    @error('nomor_kamar')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                
                <div class="col-md-6">
                    <label for="nama_pengirim" class="form-label">{{ __('paket.sender_name') }}</label>
                    <input type="text" 
                           class="form-control @error('nama_pengirim') is-invalid @enderror" 
                           id="nama_pengirim" 
                           name="nama_pengirim" 
                           value="{{ old('nama_pengirim', $paket->nama_pengirim) }}">
                    @error('nama_pengirim')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
            </div>

            <div class="row mb-3">
                <div class="col-md-12">
                    <label for="alamat_lengkap" class="form-label">{{ __('paket.full_address') }}</label>
                    <textarea class="form-control @error('alamat_lengkap') is-invalid @enderror" 
                              id="alamat_lengkap" 
                              name="alamat_lengkap" 
                              rows="3">{{ old('alamat_lengkap', $paket->alamat_lengkap) }}</textarea>
                    @error('alamat_lengkap')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
            </div>

            <div class="row mb-3">
                <div class="col-md-12">
                    <label for="keterangan" class="form-label">{{ __('paket.notes') }}</label>
                    <textarea class="form-control @error('keterangan') is-invalid @enderror" 
                              id="keterangan" 
                              name="keterangan" 
                              rows="2">{{ old('keterangan', $paket->keterangan) }}</textarea>
                    @error('keterangan')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
            </div>

            <div class="row mb-4">
                <div class="col-md-6">
                    <div class="form-check">
                        <input class="form-check-input" 
                               type="checkbox" 
                               id="tanpa_wilayah" 
                               name="tanpa_wilayah" 
                               value="1"
                               {{ old('tanpa_wilayah', $paket->tanpa_wilayah) ? 'checked' : '' }}>
                        <label class="form-check-label" for="tanpa_wilayah">
                            {{ __('paket.without_region') }}
                        </label>
                    </div>
                </div>
                
                <div class="col-md-6">
                    <div class="form-check">
                        <input class="form-check-input" 
                               type="checkbox" 
                               id="keluarga" 
                               name="keluarga" 
                               value="1"
                               {{ old('keluarga', $paket->keluarga) ? 'checked' : '' }}>
                        <label class="form-check-label" for="keluarga">
                            {{ __('paket.family_package') }}
                        </label>
                    </div>
                </div>
            </div>

            <div class="d-flex justify-content-end gap-2">
                <a href="{{ route('paket.show', $paket) }}" class="btn btn-secondary">
                    <i class="bi bi-x-circle"></i> {{ __('paket.back') }}
                </a>
                <button type="submit" class="btn btn-primary">
                    <i class="bi bi-save"></i> {{ __('paket.update') }}
                </button>
            </div>
        </form>
    </div>
</div>
@endsection

@push('scripts')
<script>
    function filterAsrama() {
        const wilayahId = document.getElementById('wilayah_id').value;
        const asramaSelect = document.getElementById('asrama_id');
        const options = asramaSelect.querySelectorAll('option');
        
        options.forEach(option => {
            if (option.value === '') {
                option.style.display = 'block';
                return;
            }
            
            const optionWilayah = option.getAttribute('data-wilayah');
            if (!wilayahId || optionWilayah === wilayahId) {
                option.style.display = 'block';
            } else {
                option.style.display = 'none';
            }
        });
    }
    
    document.addEventListener('DOMContentLoaded', function() {
        filterAsrama();
    });
</script>
@endpush
