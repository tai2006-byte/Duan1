(() => {
    const status = document.getElementById('order-status');
    const field = document.getElementById('cancel-reason-field');
    const reason = document.getElementById('cancel-reason');
    if (!status || !field || !reason) return;
    const sync = () => {
        const cancelling = status.value === 'Hủy đơn';
        field.hidden = !cancelling;
        reason.required = cancelling;
    };
    status.addEventListener('change', sync);
    sync();
})();
