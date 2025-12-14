@extends('layouts.app')

@section('title', __('paket.package_details'))

@section('content')
<div class="row mb-4">
    <div class="col">
        <h2><i class="bi bi-box-seam"></i> {{ __('paket.package_details') }}</h2>
    </div>
    <div class="col-auto">
        <a href="{{ route('paket.edit', $paket) }}" class="btn btn-warning">
            <i class="bi bi-pencil"></i> {{ __('paket.edit') }}
        </a>
        <a href="{{ route('paket.index') }}" class="btn btn-secondary">
            <i class="bi bi-arrow-left"></i> {{ __('paket.back') }}
        </a>
    </div>
</div>

<div class="row">
    <div class="col-md-8">
        <div class="card mb-4">
            <div class="card-header">
                <h5 class="mb-0"><i class="bi bi-info-circle"></i> Informasi Paket</h5>
            </div>
            <div class="card-body">
                <div class="row mb-3">
                    <div class="col-md-4"><strong>{{ __('paket.tracking_code') }}:</strong></div>
                    <div class="col-md-8">
                        <span class="badge bg-primary fs-6">{{ $paket->kode_resi }}</span>
                    </div>
                </div>

                <div class="row mb-3">
                    <div class="col-md-4"><strong>{{ __('paket.status') }}:</strong></div>
                    <div class="col-md-8">
                        <span class="badge bg-{{ 
                            $paket->status->value === 'belum_diambil' ? 'warning' : 
                            ($paket->status->value === 'diambil' ? 'info' : 
                            ($paket->status->value === 'diterima' ? 'success' : 
                            ($paket->status->value === 'salah_wilayah' ? 'danger' : 'secondary'))) 
                        }} fs-6">
                            {{ $paket->status->label() }}
                        </span>
                    </div>
                </div>

                <hr>

                <div class="row mb-3">
                    <div class="col-md-4"><strong>{{ __('paket.recipient_name') }}:</strong></div>
                    <div class="col-md-8">{{ $paket->nama_penerima }}</div>
                </div>

                <div class="row mb-3">
                    <div class="col-md-4"><strong>{{ __('paket.recipient_phone') }}:</strong></div>
                    <div class="col-md-8">{{ $paket->telepon_penerima }}</div>
                </div>

                <div class="row mb-3">
                    <div class="col-md-4"><strong>{{ __('paket.region') }}:</strong></div>
                    <div class="col-md-8">
                        @if($paket->tanpa_wilayah)
                            <span class="badge bg-secondary">{{ __('paket.without_region') }}</span>
                        @else
                            {{ $paket->wilayah?->nama_wilayah ?? '-' }}
                        @endif
                    </div>
                </div>

                <div class="row mb-3">
                    <div class="col-md-4"><strong>{{ __('paket.dormitory') }}:</strong></div>
                    <div class="col-md-8">{{ $paket->asrama?->nama_asrama ?? '-' }}</div>
                </div>

                <div class="row mb-3">
                    <div class="col-md-4"><strong>{{ __('paket.room_number') }}:</strong></div>
                    <div class="col-md-8">{{ $paket->nomor_kamar ?? '-' }}</div>
                </div>

                @if($paket->alamat_lengkap)
                    <div class="row mb-3">
                        <div class="col-md-4"><strong>{{ __('paket.full_address') }}:</strong></div>
                        <div class="col-md-8">{{ $paket->alamat_lengkap }}</div>
                    </div>
                @endif

                <div class="row mb-3">
                    <div class="col-md-4"><strong>{{ __('paket.sender_name') }}:</strong></div>
                    <div class="col-md-8">{{ $paket->nama_pengirim ?? '-' }}</div>
                </div>

                @if($paket->keluarga)
                    <div class="row mb-3">
                        <div class="col-md-4"><strong>Tipe:</strong></div>
                        <div class="col-md-8">
                            <span class="badge bg-info">{{ __('paket.family_package') }}</span>
                        </div>
                    </div>
                @endif

                @if($paket->keterangan)
                    <div class="row mb-3">
                        <div class="col-md-4"><strong>{{ __('paket.notes') }}:</strong></div>
                        <div class="col-md-8">{{ $paket->keterangan }}</div>
                    </div>
                @endif

                <hr>

                <div class="row mb-3">
                    <div class="col-md-4"><strong>{{ __('paket.received_date') }}:</strong></div>
                    <div class="col-md-8">{{ $paket->tanggal_diterima?->format('d/m/Y H:i') }}</div>
                </div>

                @if($paket->tanggal_diambil)
                    <div class="row mb-3">
                        <div class="col-md-4"><strong>{{ __('paket.pickup_date') }}:</strong></div>
                        <div class="col-md-8">{{ $paket->tanggal_diambil->format('d/m/Y H:i') }}</div>
                    </div>
                @endif

                <div class="row mb-3">
                    <div class="col-md-4"><strong>{{ __('paket.received_by') }}:</strong></div>
                    <div class="col-md-8">{{ $paket->diterimaOleh?->name ?? '-' }}</div>
                </div>

                @if($paket->diantarOleh)
                    <div class="row mb-3">
                        <div class="col-md-4"><strong>{{ __('paket.delivered_by') }}:</strong></div>
                        <div class="col-md-8">{{ $paket->diantarOleh->name }}</div>
                    </div>
                @endif
            </div>
        </div>

        <div class="card">
            <div class="card-header">
                <h5 class="mb-0"><i class="bi bi-clock-history"></i> {{ __('paket.status_history') }}</h5>
            </div>
            <div class="card-body">
                @if($paket->statusLogs->isEmpty())
                    <p class="text-muted">Belum ada riwayat status</p>
                @else
                    <div class="timeline">
                        @foreach($paket->statusLogs->sortByDesc('created_at') as $log)
                            <div class="timeline-item mb-3">
                                <div class="d-flex">
                                    <div class="flex-shrink-0">
                                        <div class="bg-primary text-white rounded-circle p-2" style="width: 40px; height: 40px; display: flex; align-items: center; justify-content: center;">
                                            <i class="bi bi-arrow-right"></i>
                                        </div>
                                    </div>
                                    <div class="flex-grow-1 ms-3">
                                        <div class="d-flex justify-content-between">
                                            <div>
                                                @if($log->status_dari)
                                                    <span class="badge bg-secondary">{{ $log->status_dari->label() }}</span>
                                                    <i class="bi bi-arrow-right mx-1"></i>
                                                @endif
                                                <span class="badge bg-primary">{{ $log->status_ke->label() }}</span>
                                            </div>
                                            <small class="text-muted">{{ $log->created_at->format('d/m/Y H:i') }}</small>
                                        </div>
                                        <div class="mt-1">
                                            <small class="text-muted">
                                                <i class="bi bi-person"></i> {{ $log->diubahOleh?->name ?? 'Sistem' }}
                                            </small>
                                        </div>
                                        @if($log->catatan)
                                            <div class="mt-1">
                                                <small><strong>Catatan:</strong> {{ $log->catatan }}</small>
                                            </div>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @endif
            </div>
        </div>
    </div>

    <div class="col-md-4">
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0"><i class="bi bi-gear"></i> {{ __('paket.update_status') }}</h5>
            </div>
            <div class="card-body">
                <form method="POST" action="{{ route('paket.update-status', $paket) }}">
                    @csrf
                    
                    <div class="mb-3">
                        <label for="status" class="form-label">{{ __('paket.status') }}</label>
                        <select class="form-select" id="status" name="status" required>
                            @foreach(\App\Enums\PaketStatus::cases() as $status)
                                <option value="{{ $status->value }}" 
                                        {{ $paket->status->value === $status->value ? 'selected' : '' }}>
                                    {{ $status->label() }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="mb-3">
                        <label for="catatan" class="form-label">{{ __('paket.notes') }}</label>
                        <textarea class="form-control" id="catatan" name="catatan" rows="3"></textarea>
                        <small class="text-muted">Opsional</small>
                    </div>

                    <button type="submit" class="btn btn-primary w-100">
                        <i class="bi bi-arrow-repeat"></i> {{ __('paket.update_status') }}
                    </button>
                </form>
            </div>
        </div>

        <div class="card mt-3">
            <div class="card-header bg-danger text-white">
                <h6 class="mb-0"><i class="bi bi-exclamation-triangle"></i> Zona Bahaya</h6>
            </div>
            <div class="card-body">
                <form action="{{ route('paket.destroy', $paket) }}" method="POST">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-danger w-100 mb-2" 
                            onclick="return confirm('{{ __('paket.confirm_delete') }}')">
                        <i class="bi bi-trash"></i> {{ __('paket.delete') }}
                    </button>
                </form>

                @if(in_array(Auth::user()->role->value, ['admin', 'petugas_pos']) && $paket->trashed())
                    <form action="{{ route('paket.force-destroy', $paket->id) }}" method="POST">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-dark w-100" 
                                onclick="return confirm('{{ __('paket.confirm_force_delete') }}')">
                            <i class="bi bi-trash-fill"></i> {{ __('paket.delete_permanently') }}
                        </button>
                    </form>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection
