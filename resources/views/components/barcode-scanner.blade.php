<div class="modal fade" id="barcodeScannerModal" tabindex="-1" aria-labelledby="barcodeScannerModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="barcodeScannerModalLabel">
                    <i class="bi bi-upc-scan"></i> {{ __('paket.barcode_scanner') }}
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div id="scanner-container" style="position: relative; width: 100%; height: 400px;">
                    <div id="interactive" class="viewport" style="width: 100%; height: 100%;"></div>
                </div>
                <div class="alert alert-info mt-3" id="scanner-status">
                    <i class="bi bi-info-circle"></i> {{ __('paket.scanning_in_progress') }}
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                    {{ __('paket.stop_scanning') }}
                </button>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
    let scannerActive = false;
    
    document.getElementById('barcodeScannerModal').addEventListener('shown.bs.modal', function() {
        if (!scannerActive) {
            startScanner();
        }
    });
    
    document.getElementById('barcodeScannerModal').addEventListener('hidden.bs.modal', function() {
        stopScanner();
    });
    
    function startScanner() {
        scannerActive = true;
        
        Quagga.init({
            inputStream: {
                name: "Live",
                type: "LiveStream",
                target: document.querySelector('#interactive'),
                constraints: {
                    width: 640,
                    height: 480,
                    facingMode: "environment"
                },
            },
            decoder: {
                readers: [
                    "code_128_reader",
                    "ean_reader",
                    "ean_8_reader",
                    "code_39_reader",
                    "code_39_vin_reader",
                    "codabar_reader",
                    "upc_reader",
                    "upc_e_reader",
                    "i2of5_reader"
                ]
            },
            locate: true,
            locator: {
                patchSize: "medium",
                halfSample: true
            },
        }, function(err) {
            if (err) {
                console.error(err);
                document.getElementById('scanner-status').innerHTML = 
                    '<i class="bi bi-exclamation-triangle"></i> ' + 
                    '{{ __('paket.camera_permission_required') }}';
                return;
            }
            Quagga.start();
        });
        
        Quagga.onDetected(function(result) {
            if (result && result.codeResult && result.codeResult.code) {
                const code = result.codeResult.code;
                document.getElementById('kode_resi').value = code;
                stopScanner();
                
                const modal = bootstrap.Modal.getInstance(document.getElementById('barcodeScannerModal'));
                modal.hide();
                
                // Check if resi already exists
                checkResiExists(code);
            }
        });
    }
    
    function stopScanner() {
        if (scannerActive) {
            Quagga.stop();
            scannerActive = false;
        }
    }
    
    function checkResiExists(kodeResi) {
        fetch('{{ route('paket.check-resi') }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': window.csrfToken
            },
            body: JSON.stringify({ kode_resi: kodeResi })
        })
        .then(response => response.json())
        .then(data => {
            if (data.exists) {
                alert('Kode resi sudah terdaftar dalam sistem!');
                document.getElementById('kode_resi').value = '';
                document.getElementById('kode_resi').focus();
            }
        })
        .catch(error => console.error('Error:', error));
    }
</script>
@endpush
