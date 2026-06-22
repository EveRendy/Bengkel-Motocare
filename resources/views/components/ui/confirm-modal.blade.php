<div id="confirmModal" class="fixed inset-0 z-[80] hidden items-center justify-center bg-black/70 backdrop-blur-sm px-4">
    <div class="w-full max-w-md rounded-2xl border border-gray-700 bg-gray-800 shadow-2xl">
        <div class="p-6">
            <div id="confirmModalIcon" class="mb-4 flex h-12 w-12 items-center justify-center rounded-xl border border-red-500/30 bg-red-500/10 text-red-400">
                <span class="h-3 w-3 rounded-full bg-current"></span>
            </div>
            <h3 id="confirmModalTitle" class="text-lg font-semibold text-gray-100">Konfirmasi Aksi</h3>
            <p id="confirmModalMessage" class="mt-2 text-sm leading-6 text-gray-400">Apakah Anda yakin ingin melanjutkan?</p>
        </div>
        <div class="flex justify-end gap-3 border-t border-gray-700 px-6 py-4">
            <button type="button" id="confirmModalCancel" class="px-4 py-2 text-sm font-medium text-gray-400 hover:text-gray-200">
                Batal
            </button>
            <button type="button" id="confirmModalConfirm" class="rounded-lg bg-red-600 px-4 py-2 text-sm font-semibold text-white shadow hover:bg-red-700">
                Ya, Lanjutkan
            </button>
        </div>
    </div>
</div>

<script>
    (function () {
        const modal = document.getElementById('confirmModal');
        const title = document.getElementById('confirmModalTitle');
        const message = document.getElementById('confirmModalMessage');
        const icon = document.getElementById('confirmModalIcon');
        const confirmButton = document.getElementById('confirmModalConfirm');
        const cancelButton = document.getElementById('confirmModalCancel');
        let targetForm = null;

        function setVariant(variant) {
            const isDanger = variant !== 'warning';
            icon.className = isDanger
                ? 'mb-4 flex h-12 w-12 items-center justify-center rounded-xl border border-red-500/30 bg-red-500/10 text-red-400'
                : 'mb-4 flex h-12 w-12 items-center justify-center rounded-xl border border-orange-500/30 bg-orange-500/10 text-orange-400';
            confirmButton.className = isDanger
                ? 'rounded-lg bg-red-600 px-4 py-2 text-sm font-semibold text-white shadow hover:bg-red-700'
                : 'rounded-lg bg-orange-600 px-4 py-2 text-sm font-semibold text-white shadow hover:bg-orange-700';
        }

        function closeModal() {
            modal.classList.add('hidden');
            modal.classList.remove('flex');
            targetForm = null;
        }

        document.addEventListener('click', function (event) {
            const trigger = event.target.closest('[data-confirm-submit]');
            if (!trigger) {
                return;
            }

            const form = trigger.closest('form');
            if (!form || !form.reportValidity()) {
                return;
            }

            targetForm = form;
            title.textContent = trigger.dataset.confirmTitle || 'Konfirmasi Aksi';
            message.textContent = trigger.dataset.confirmMessage || 'Apakah Anda yakin ingin melanjutkan?';
            confirmButton.textContent = trigger.dataset.confirmText || 'Ya, Lanjutkan';
            setVariant(trigger.dataset.confirmVariant || 'danger');

            modal.classList.remove('hidden');
            modal.classList.add('flex');
        });

        confirmButton.addEventListener('click', function () {
            if (targetForm) {
                targetForm.submit();
            }
            closeModal();
        });

        cancelButton.addEventListener('click', closeModal);
        modal.addEventListener('click', function (event) {
            if (event.target === modal) {
                closeModal();
            }
        });
        document.addEventListener('keydown', function (event) {
            if (event.key === 'Escape' && !modal.classList.contains('hidden')) {
                closeModal();
            }
        });
    })();
</script>
