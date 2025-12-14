@extends('layouts.app')

@section('title', __('paket.incoming_packages'))

@section('content')
<div class="row mb-4">
    <div class="col">
        <h2><i class="bi bi-inbox"></i> {{ __('paket.incoming_packages') }}</h2>
        <p class="text-muted">Paket dengan status: Belum Diambil dan Diambil</p>
    </div>
    <div class="col-auto">
        <a href="{{ route('paket.create') }}" class="btn btn-primary">
            <i class="bi bi-plus-circle"></i> {{ __('paket.create_package') }}
        </a>
    </div>
</div>

<div class="card mb-4">
    <div class="card-body">
        <form method="GET" action="{{ route('paket.masuk') }}" class="row g-3">
            <div class="col-md-6">
                <label class="form-label">{{ __('paket.search') }}</label>
                <input type="text" name="search" class="form-control" 
                       value="{{ request('search') }}"
                       placeholder="{{ __('paket.search_placeholder') }}">
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
                <a href="{{ route('paket.masuk') }}" class="btn btn-secondary">
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
                        <th>{{ __('paket.recipient_phone') }}</th>
                        <th>{{ __('paket.region') }}</th>
                        <th>{{ __('paket.dormitory') }}</th>
                        <th>{{ __('paket.status') }}</th>
                        <th>{{ __('paket.received_date') }}</th>
                        <th>{{ __('paket.actions') }}</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($paket as $item)
                        <tr>
                            <td><strong>{{ $item->kode_resi }}</strong></td>
                            <td>
                                {{ $item->nama_penerima }}
                                @if($item->keluarga)
                                    <span class="badge bg-info">{{ __('paket.family_package') }}</span>
                                @endif
                            </td>
                            <td>{{ $item->telepon_penerima }}</td>
                            <td>
                                @if($item->tanpa_wilayah)
                                    <span class="badge bg-secondary">{{ __('paket.without_region') }}</span>
                                @else
                                    {{ $item->wilayah?->nama_wilayah ?? '-' }}
                                @endif
                            </td>
                            <td>{{ $item->asrama?->nama_asrama ?? '-' }}</td>
                            <td>
                                <span class="badge bg-{{ $item->status->value === 'belum_diambil' ? 'warning' : 'info' }}">
                                    {{ $item->status->label() }}
                                </span>
                            </td>
                            <td>{{ $item->tanggal_diterima?->format('d/m/Y H:i') }}</td>
                            <td>
                                <div class="btn-group" role="group">
                                    <a href="{{ route('paket.show', $item) }}" class="btn btn-sm btn-info" 
                                       title="{{ __('paket.view') }}">
                                        <i class="bi bi-eye"></i>
                                    </a>
                                    <a href="{{ route('paket.edit', $item) }}" class="btn btn-sm btn-warning" 
                                       title="{{ __('paket.edit') }}">
                                        <i class="bi bi-pencil"></i>
                                    </a>
                                    @if(Auth::user()->role->value === 'petugas_pos' && $item->status->value === 'belum_diambil')
                                        <button type="button" class="btn btn-sm btn-success" 
                                                onclick="updateStatus({{ $item->id }}, 'diambil')"
                                                title="Tandai Diambil">
                                            <i class="bi bi-check-circle"></i>
                                        </button>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="text-center py-4">
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
    
    function updateStatus(paketId, status) {
        if (!confirm('Apakah Anda yakin ingin mengubah status paket ini?')) {
            return;
        }
        
        fetch(`/paket/${paketId}/status`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': window.csrfToken
            },
            body: JSON.stringify({ 
                status: status,
                catatan: 'Status diubah dari paket masuk'
            })
        })
        .then(response => response.json())
        .then(data => {
            window.location.reload();
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Terjadi kesalahan saat mengubah status');
        });
    }
</script>
@endpush
