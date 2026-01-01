<x-layouts.admin title="Scan QR Code Check-in">
    <div class="max-w-4xl mx-auto">
        <!-- Header -->
        <div class="flex items-center justify-between mb-6">
            <div>
                <h2 class="text-xl font-bold text-gray-900">Scan QR Code</h2>
                <p class="text-gray-500">Scan QR Code tamu untuk melakukan Check-in</p>
            </div>
            <x-ui.button variant="secondary" href="{{ route('admin.booking.index') }}">
                Kembali
            </x-ui.button>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <!-- Scanner Section -->
            <x-ui.card>
                <div class="text-center p-4">
                    <div id="reader" class="overflow-hidden rounded-lg bg-gray-100"></div>
                    <p class="mt-4 text-sm text-gray-500">Arahkan kamera ke QR Code di HP Tamu</p>

                    <div id="loading-indicator" class="hidden mt-4">
                        <div class="flex items-center justify-center gap-2 text-primary-600">
                            <svg class="animate-spin h-5 w-5" xmlns="http://www.w3.org/2000/svg" fill="none"
                                viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor"
                                    stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor"
                                    d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z">
                                </path>
                            </svg>
                            <span>Memproses...</span>
                        </div>
                    </div>
                </div>
            </x-ui.card>

            <!-- Result Section -->
            <div class="space-y-6">
                <!-- Instructions -->
                <x-ui.card>
                    <h3 class="font-semibold text-gray-900 mb-2">Panduan Penggunaan</h3>
                    <ul class="list-disc list-inside text-sm text-gray-600 space-y-1">
                        <li>Pastikan browser diizinkan mengakses kamera.</li>
                        <li>Pastikan QR Code terlihat jelas dan cukup cahaya.</li>
                        <li>Sistem akan otomatis memproses saat QR terdeteksi.</li>
                    </ul>
                </x-ui.card>

                <!-- Last Scan Result -->
                <div id="scan-result" class="hidden">
                    <x-ui.card>
                        <div class="text-center mb-4">
                            <div id="result-icon-success"
                                class="hidden mx-auto flex items-center justify-center h-12 w-12 rounded-full bg-green-100 mb-2">
                                <svg class="h-6 w-6 text-green-600" fill="none" viewBox="0 0 24 24"
                                    stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M5 13l4 4L19 7" />
                                </svg>
                            </div>
                            <div id="result-icon-error"
                                class="hidden mx-auto flex items-center justify-center h-12 w-12 rounded-full bg-red-100 mb-2">
                                <svg class="h-6 w-6 text-red-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M6 18L18 6M6 6l12 12" />
                                </svg>
                            </div>
                            <h3 id="result-title" class="text-lg font-bold text-gray-900">Scan Berhasil</h3>
                            <p id="result-message" class="text-sm text-gray-600">Booking berhasil dicheck-in.</p>
                        </div>

                        <div class="border-t pt-4">
                            <div class="grid grid-cols-2 gap-4 text-sm">
                                <div>
                                    <p class="text-gray-500">Nama Tamu</p>
                                    <p id="guest-name" class="font-medium">-</p>
                                </div>
                                <div>
                                    <p class="text-gray-500">Kavling</p>
                                    <p id="kavling-name" class="font-medium">-</p>
                                </div>
                                <div>
                                    <p class="text-gray-500">Kode Booking</p>
                                    <p id="booking-code" class="font-mono font-medium">-</p>
                                </div>
                                <div>
                                    <p class="text-gray-500">Status</p>
                                    <p id="booking-status" class="font-medium">-</p>
                                </div>
                            </div>
                        </div>

                        <div class="mt-4 pt-4 border-t text-center">
                            <button onclick="resumeScanning()"
                                class="text-primary-600 hover:text-primary-700 font-medium text-sm">
                                Scan QR Code Lainnya
                            </button>
                        </div>
                    </x-ui.card>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
        <script src="https://unpkg.com/html5-qrcode" type="text/javascript"></script>
        <script>
            const html5QrCode = new Html5Qrcode("reader");
            let isScanning = true;
            let isProcessing = false;

            // Beep sound for success interaction
            const audioCtx = new (window.AudioContext || window.webkitAudioContext)();
            function playBeep(success = true) {
                if (audioCtx.state === 'suspended') audioCtx.resume();
                const oscillator = audioCtx.createOscillator();
                const gainNode = audioCtx.createGain();
                oscillator.connect(gainNode);
                gainNode.connect(audioCtx.destination);

                oscillator.type = 'sine';
                oscillator.frequency.setValueAtTime(success ? 880 : 300, audioCtx.currentTime); // A5 for success, low beep for error

                gainNode.gain.setValueAtTime(0.1, audioCtx.currentTime);

                oscillator.start();
                setTimeout(() => {
                    oscillator.stop();
                }, success ? 200 : 500);
            }

            function onScanSuccess(decodedText, decodedResult) {
                if (!isScanning || isProcessing) return;

                isProcessing = true;
                html5QrCode.pause(); // Pause scanning

                document.getElementById('loading-indicator').classList.remove('hidden');

                // Send to Backend
                fetch('{{ route('admin.booking.scan-check-in') }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    body: JSON.stringify({ code: decodedText })
                })
                    .then(response => response.json())
                    .then(data => {
                        document.getElementById('loading-indicator').classList.add('hidden');
                        showResult(data.status === 'success', data.message, data.booking);
                        playBeep(data.status === 'success');
                    })
                    .catch(error => {
                        document.getElementById('loading-indicator').classList.add('hidden');
                        showResult(false, 'Terjadi kesalahan sistem.', null);
                        playBeep(false);
                    });
            }

            function onScanFailure(error) {
                // handle scan failure, usually better to ignore and keep scanning.
                // console.warn(`Code scan error = ${error}`);
            }

            function showResult(success, message, booking) {
                const resultCard = document.getElementById('scan-result');
                const iconSuccess = document.getElementById('result-icon-success');
                const iconError = document.getElementById('result-icon-error');
                const title = document.getElementById('result-title');
                const msg = document.getElementById('result-message');

                resultCard.classList.remove('hidden');

                if (success) {
                    iconSuccess.classList.remove('hidden');
                    iconError.classList.add('hidden');
                    title.innerText = 'Check-in Berhasil';
                    title.className = 'text-lg font-bold text-green-700';
                } else {
                    iconSuccess.classList.add('hidden');
                    iconError.classList.remove('hidden');
                    title.innerText = 'Gagal';
                    title.className = 'text-lg font-bold text-red-700';
                }

                msg.innerText = message;

                if (booking) {
                    document.getElementById('guest-name').innerText = booking.user ? booking.user.name : 'Guest';
                    document.getElementById('kavling-name').innerText = booking.kavling ? booking.kavling.nama : '-';
                    document.getElementById('booking-code').innerText = booking.code;
                    document.getElementById('booking-status').innerText = booking.status;
                } else {
                    document.getElementById('guest-name').innerText = '-';
                    document.getElementById('kavling-name').innerText = '-';
                    document.getElementById('booking-code').innerText = '-';
                    document.getElementById('booking-status').innerText = '-';
                }
            }

            function resumeScanning() {
                document.getElementById('scan-result').classList.add('hidden');
                isScanning = true;
                isProcessing = false;
                html5QrCode.resume();
            }

            // Start Scanner
            Html5Qrcode.getCameras().then(devices => {
                if (devices && devices.length) {
                    const cameraId = devices[0].id;
                    html5QrCode.start(
                        cameraId,
                        {
                            fps: 10,
                            qrbox: { width: 250, height: 250 }
                        },
                        onScanSuccess,
                        onScanFailure
                    ).catch(err => {
                        alert('Gagal memulai kamera: ' + err);
                    });
                } else {
                    alert('Tidak ada kamera ditemukan pada perangkat ini.');
                }
            }).catch(err => {
                alert('Gagal mengakses kamera, pastikan izin diberikan.');
            });
        </script>
    @endpush
</x-layouts.admin>