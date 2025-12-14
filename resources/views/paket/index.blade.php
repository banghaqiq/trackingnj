@extends('layouts.app')

@section('title', __('paket.all_packages'))

@section('content')
<div class="row mb-4">
    <div class="col">
        <h2><i class="bi bi-list"></i> {{ __('paket.all_packages') }}</h2>
    </div>
    <div class="col-auto">
        <a href="{{ route('paket.create') }}" class="btn btn-primary">
            <i class="bi bi-plus-circle"></i> {{ __('paket.create_package') }}
        </a>
    </div>
</div>

<div class="card mb-4">
    <div class="card-body">
        <form method="GET" action="{{ route('paket.index') }}" class="row g-3">
            <div class="col-md-4">
                <label class="form-label">{{ __('paket.search') }}</label>
                <input type="text" name="search" class="form-control" 
                       value="{{ request('search') }}"
                       placeholder="{{ __('paket.search_placeholder') }}">
            </div>
            
            <div class="col-md-2">
                <label class="form-label">{{ __('paket.filter_by_status') }}</label>
                <select name="status" class="form-select">
                    <option value="">{{ __('paket.all_status') }}</option>
                    @foreach(\App\Enums\PaketStatus::cases() as $status)
                        <option value="{{ $status->value }}" 
                                {{ request('status') === $status->value ? 'selected' : '' }}>
                            {{ $status->label() }}
                        </option>
                    @endforeach
                </select>
            </div>
            
            <div class="col-md-2">
                <label class="form-label">{{ __('paket.start_date') }}</label>
                <input type="date" name="tanggal_mulai" class="form-control" 
                       value="{{ request('tanggal_mulai') }}">
            </div>
            
            <div class="col-md-2">
                <label class="form-label">{{ __('paket.end_date') }}</label>
                <input type="date" name="tanggal_akhir" class="form-control" 
                       value="{{ request('tanggal_akhir') }}">
            </div>
            
            <div class="col-md-2 d-flex align-items-end">
                <button type="submit" class="btn btn-primary me-2">
                    <i class="bi bi-search"></i> {{ __('paket.filter') }}
                </button>
                <a href="{{ route('paket.index') }}" class="btn btn-secondary">
                    <i class="bi bi-x-circle"></i> {{ __('paket.reset') }}
                </a>
            </div>
        </form>
    </div>
</div>

<div class="card">
    <div class="card-body">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <div>
                <select class="form-select form-select-sm" id="perPageSelect" onchange="changePerPage(this.value)">
                    <option value="10" {{ request('per_page') == 10 ? 'selected' : '' }}>10</option>
                    <option value="25" {{ request('per_page') == 25 ? 'selected' : '' }}>25</option>
                    <option value="50" {{ request('per_page') == 50 ? 'selected' : '' }}>50</option>
                </select>
                <small class="text-muted ms-2">{{ __('paket.per_page') }}</small>
            </div>
            <div class="text-muted">
                {{ __('paket.showing') }} {{ $paket->firstItem() ?? 0 }} 
                {{ __('paket.to') }} {{ $paket->lastItem() ?? 0 }} 
                {{ __('paket.of') }} {{ $paket->total() }} 
                {{ __('paket.results') }}
            </div>
        </div>

        <div class="table-responsive">
            <table class="table table-hover">
                <thead class="table-light">
                    <tr>
                        <th>{{ __('paket.tracking_code') }}</th>
                        <th>{{ __('paket.recipient_name') }}</th>
                        <th>{{ __('paket.region') }}</th>
                        <th>{{ __('paket.dormitory') }}</th>
                        <th>{{ __('paket.status') }}</th>
                        <th>{{ __('paket.received_date') }}</th>
                        <th>{{ __('paket.actions') }}</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($paket as $item)
                        <tr class="{{ $item->trashed() ? 'table-secondary' : '' }}">
                            <td>
                                <strong>{{ $item->kode_resi }}</strong>
                                @if($item->trashed())
                                    <span class="badge bg-danger ms-1">{{ __('paket.deleted_package') }}</span>
                                @endif
                            </td>
                            <td>
                                {{ $item->nama_penerima }}
                                @if($item->keluarga)
                                    <span class="badge bg-info">{{ __('paket.family_package') }}</span>
                                @endif
                            </td>
                            <td>{{ $item->wilayah?->nama_wilayah ?? '-' }}</td>
                            <td>{{ $item->asrama?->nama_asrama ?? '-' }}</td>
                            <td>
                                <span class="badge bg-{{ $item->status->value === 'belum_diambil' ? 'warning' : 
                                    ($item->status->value === 'diambil' ? 'info' : 
                                    ($item->status->value === 'diterima' ? 'success' : 
                                    ($item->status->value === 'salah_wilayah' ? 'danger' : 'secondary'))) }}">
                                    {{ $item->status->label() }}
                                </span>
                            </td>
                            <td>{{ $item->tanggal_diterima?->format('d/m/Y H:i') }}</td>
                            <td>
                                <div class="btn-group" role="group">
                                    <a href="{{ route('paket.show', $item) }}" class="btn btn-sm btn-info">
                                        <i class="bi bi-eye"></i>
                                    </a>
                                    @if(!$item->trashed())
                                        <a href="{{ route('paket.edit', $item) }}" class="btn btn-sm btn-warning">
                                            <i class="bi bi-pencil"></i>
                                        </a>
                                        <form action="{{ route('paket.destroy', $item) }}" method="POST" class="d-inline">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-sm btn-danger" 
                                                    onclick="return confirm('{{ __('paket.confirm_delete') }}')">
                                                <i class="bi bi-trash"></i>
                                            </button>
                                        </form>
                                    @else
                                        <form action="{{ route('paket.restore', $item->id) }}" method="POST" class="d-inline">
                                            @csrf
                                            <button type="submit" class="btn btn-sm btn-success" 
                                                    onclick="return confirm('{{ __('paket.confirm_restore') }}')">
                                                <i class="bi bi-arrow-clockwise"></i>
                                            </button>
                                        </form>
                                        @if(Auth::user()->role->value === 'admin' || Auth::user()->role->value === 'petugas_pos')
                                            <form action="{{ route('paket.force-destroy', $item->id) }}" method="POST" class="d-inline">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-sm btn-dark" 
                                                        onclick="return confirm('{{ __('paket.confirm_force_delete') }}')">
                                                    <i class="bi bi-trash-fill"></i>
                                                </button>
                                            </form>
                                        @endif
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="text-center py-4">
                                <i class="bi bi-inbox" style="font-size: 3rem;"></i>
                                <p class="mt-2">{{ __('paket.no_packages_found') }}</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="d-flex justify-content-center">
            {{ $paket->links() }}
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    function changePerPage(value) {
        const url = new URL(window.location.href);
        url.searchParams.set('per_page', value);
        window.location.href = url.toString();
    }
</script>
@endpush
