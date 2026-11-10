(() => {
    'use strict';
    const root = document.querySelector('[data-cart-root]');
    if (!root) return;
    const rows = () => [...root.querySelectorAll('[data-cart-row]')];
    const message = root.querySelector('#cart-message');
    const retry = root.querySelector('[data-cart-retry]');
    const money = value => new Intl.NumberFormat('vi-VN').format(value) + ' đ';
    let timer, busy = false, uncertain = false;
    const valid = value => /^\d+$/.test(value) && Number(value) >= 1 && Number(value) <= 2147483647;
    const dirty = () => rows().some(row => row.querySelector('[data-cart-quantity]').value !== row.dataset.quantity);

    function show(text, error = false) {
        message.hidden = !text;
        message.className = 'alert py-2 ' + (error ? 'alert-danger' : 'alert-success');
        message.querySelector('[data-cart-message-text]').textContent = text;
        retry.hidden = !uncertain;
    }
    function controls() {
        root.setAttribute('aria-busy', String(busy));
        root.querySelector('#cart-saving').hidden = !busy;
        rows().forEach(row => {
            row.querySelector('[data-cart-quantity]').disabled = busy || uncertain;
            row.querySelector('[data-cart-remove]').disabled = busy || uncertain;
            row.querySelectorAll('[data-quantity-step]').forEach(button => {
                button.disabled = busy || uncertain || (button.dataset.quantityStep === '-1' && Number(row.querySelector('[data-cart-quantity]').value) <= 1);
            });
        });
        const blocked = busy || uncertain || dirty();
        const checkout = root.querySelector('[data-cart-checkout]');
        checkout.classList.toggle('disabled', blocked);
        checkout.setAttribute('aria-disabled', String(blocked));
    }
    function render(data) {
        const items = new Map(data.items.map(item => [item.key, item]));
        rows().forEach(row => {
            const item = items.get(row.dataset.key);
            if (!item) { row.remove(); return; }
            row.dataset.quantity = String(item.quantity);
            row.querySelector('[data-cart-quantity]').value = item.quantity;
            row.querySelector('[data-unit-price]').textContent = money(item.price);
            row.querySelector('[data-line-total]').textContent = money(item.subtotal);
            row.querySelector('[data-cart-name]').textContent = item.name;
        });
        root.querySelector('[data-cart-count]').textContent = data.count;
        root.querySelector('[data-cart-total]').textContent = money(data.total);
        document.querySelectorAll('.cart-count').forEach(el => { el.textContent = data.count; });
        document.querySelectorAll('.store-cart').forEach(el => el.setAttribute('aria-label', 'Giỏ hàng, ' + data.count + ' sản phẩm'));
        root.querySelector('#cart-filled').hidden = data.count === 0;
        root.querySelector('#cart-empty').hidden = data.count !== 0;
    }
    async function request(operation, row, quantity) {
        const body = new URLSearchParams({ csrf_token: root.dataset.csrf, operation });
        if (row) body.set('key', row.dataset.key);
        if (quantity !== undefined) body.set('quantity', quantity);
        const response = await fetch(root.dataset.endpoint, { method: 'POST', body, credentials: 'same-origin', headers: { Accept: 'application/json' } });
        const data = await response.json();
        if (!Array.isArray(data.items) || typeof data.success !== 'boolean') throw new Error('Invalid cart response');
        return data;
    }
    async function update(operation, row, quantity) {
        if (busy) return;
        clearTimeout(timer);
        // Save other pending manual edits before this operation, in order.
        const pending = rows().filter(item => !uncertain && item !== row && item.querySelector('[data-cart-quantity]').value !== item.dataset.quantity)
            .map(item => ({ row: item, quantity: item.querySelector('[data-cart-quantity]').value }));
        if (pending.some(item => !valid(item.quantity))) { show('Số lượng phải là số nguyên từ 1 trở lên.', true); return; }
        busy = true; controls();
        try {
            for (const item of pending) {
                const result = await request('set', item.row, item.quantity);
                if (!result.success) { render(result); show(result.message, true); return; }
            }
            const data = await request(operation, row, quantity);
            uncertain = false;
            render(data);
            show(data.message, !data.success);
        } catch (error) {
            // A lost response may still have changed the session. Read it back before checkout.
            try {
                const data = await request('state');
                if (!data.success) throw new Error('Cannot synchronize cart');
                uncertain = false; render(data);
                show('Kết nối bị gián đoạn. Giỏ hàng đã được đồng bộ, vui lòng kiểm tra số lượng.', true);
            } catch (syncError) {
                uncertain = true;
                show('Không thể kết nối. Hãy thử lại để đồng bộ giỏ hàng trước khi thanh toán.', true);
            }
        } finally { busy = false; controls(); }
    }
    function save(row) {
        const input = row.querySelector('[data-cart-quantity]');
        clearTimeout(timer);
        if (!valid(input.value)) {
            input.value = row.dataset.quantity;
            show('Số lượng phải là số nguyên từ 1 trở lên.', true);
            controls(); return;
        }
        if (input.value !== row.dataset.quantity) update('set', row, input.value);
        else controls();
    }
    root.addEventListener('input', event => {
        if (!event.target.matches('[data-cart-quantity]')) return;
        clearTimeout(timer); controls();
        const row = event.target.closest('[data-cart-row]');
        timer = setTimeout(() => save(row), 600);
    });
    root.addEventListener('change', event => {
        if (event.target.matches('[data-cart-quantity]')) save(event.target.closest('[data-cart-row]'));
    });
    root.addEventListener('submit', event => {
        if (!event.target.matches('[data-quantity-form]')) return;
        event.preventDefault(); save(event.target.closest('[data-cart-row]'));
    });
    root.addEventListener('click', event => {
        const checkout = event.target.closest('[data-cart-checkout]');
        if (checkout && (busy || uncertain || dirty())) { event.preventDefault(); return; }
        const button = event.target.closest('[data-quantity-step], [data-cart-remove]');
        if (!button || busy || uncertain || button.disabled) return;
        const row = button.closest('[data-cart-row]');
        if (button.matches('[data-cart-remove]')) { update('remove', row); return; }
        const input = row.querySelector('[data-cart-quantity]');
        const current = valid(input.value) ? Number(input.value) : Number(row.dataset.quantity);
        input.value = Math.max(1, Math.min(2147483647, current + Number(button.dataset.quantityStep)));
        save(row);
    });
    retry.addEventListener('click', () => update('state'));
    controls();
})();
