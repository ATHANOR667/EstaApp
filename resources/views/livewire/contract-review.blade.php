<div class="max-w-3xl mx-auto p-4 sm:p-6">
    <h1 class="text-2xl sm:text-3xl font-bold text-gray-900 dark:text-gray-100 mb-6">Revue du contrat</h1>

    @if (session()->has('success'))
        <div class="bg-green-100 dark:bg-green-800 border border-green-400 dark:border-green-700 text-green-700 dark:text-green-100 px-4 py-3 rounded mb-4">
            {{ session('success') }}
        </div>
    @endif

    <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-4 sm:p-6 mb-6">
        <h2 class="text-lg sm:text-xl font-semibold text-gray-800 dark:text-gray-100 mb-4">Détails de la prestation</h2>
        <p><strong>Artiste :</strong> {{ $contrat->prestation->artiste->nom }}</p>
        <p><strong>Date :</strong> {{ \Carbon\Carbon::parse($contrat->prestation->date_prestation)->locale('fr')->isoFormat('D MMMM YYYY') }}</p>
        <p><strong>Lieu :</strong> {{ $contrat->prestation->lieu_prestation }}</p>
        <p><strong>Statut :</strong> {{ \App\Models\Contrat::STATUSES[$contrat->status] ?? ucfirst($contrat->status) }}</p>
    </div>

    <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-4 sm:p-6 mb-6">
        <h2 class="text-lg sm:text-xl font-semibold text-gray-800 dark:text-gray-100 mb-4">Contenu du contrat</h2>
        <div class="prose dark:prose-invert max-w-none">
            {!! $contrat->content !!}
        </div>
    </div>

    @if ($contrat->status == 'pending_organizer')
        <form wire:submit.prevent="acceptContract" class="mb-6">
            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Votre signature</label>
                <canvas id="signature-pad" class="border border-gray-300 w-full h-40 sm:h-48 mt-2"></canvas>
                <div class="mt-2 flex space-x-2">
                    <button type="button" id="clear-signature" class="px-3 py-1 rounded-md bg-gray-300 text-gray-800">Effacer</button>
                    <button type="button" id="save-signature" class="px-3 py-1 rounded-md bg-blue-600 text-white">Enregistrer</button>
                </div>
                <input type="hidden" wire:model="signatureContractant">
                @error('signatureContractant') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
            </div>
            <button type="submit" class="mt-4 px-5 py-2 rounded-md bg-green-600 text-white hover:bg-green-700">Accepter et signer</button>
        </form>

        <form wire:submit.prevent="rejectContract">
            <div>
                <label for="motif" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Motif de refus</label>
                <textarea id="motif" wire:model="motif" rows="4" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm"></textarea>
                @error('motif') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
            </div>
            <button type="submit" class="mt-4 px-5 py-2 rounded-md bg-red-600 text-white hover:bg-red-700">Rejeter</button>
        </form>
    @endif

    <div class="mt-6">
        <button wire:click="downloadPdf" class="px-5 py-2 rounded-md bg-blue-600 text-white hover:bg-blue-700">Télécharger PDF</button>
    </div>

    @push('scripts')
        <script src="https://cdn.jsdelivr.net/npm/signature_pad@4.0.0/dist/signature_pad.umd.min.js"></script>
        <script>
            document.addEventListener('DOMContentLoaded', () => {
                const canvas = document.querySelector('#signature-pad');
                if (canvas) {
                    canvas.width = window.innerWidth < 640 ? window.innerWidth - 40 : 400;
                    canvas.height = window.innerWidth < 640 ? 150 : 200;
                    const signaturePad = new SignaturePad(canvas);
                    document.querySelector('#clear-signature').addEventListener('click', () => signaturePad.clear());
                    document.querySelector('#save-signature').addEventListener('click', () => {
                        const dataUrl = signaturePad.toDataURL('image/png');
                    @this.set('signatureContractant', dataUrl);
                    });
                }
            });
        </script>
    @endpush
</div>
