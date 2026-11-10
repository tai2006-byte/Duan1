</main>
<footer class="admin-footer">Quản trị cửa hàng đồ chơi</footer>
</div>
<script src="views/assets/js/bootstrap.bundle.min.js"></script>
<script src="views/assets/js/admin-layout.js?v=1"></script>
<script src="views/assets/vendors/simple-datatables/simple-datatables.js"></script>
<script>
const adminTable = document.querySelector('#table1');
if (adminTable && adminTable.tBodies[0]?.rows.length && window.simpleDatatables) {
    new simpleDatatables.DataTable(adminTable, {
        perPage: 10, perPageSelect: [10, 25, 50],
        labels: {placeholder: 'Tìm trong bảng...', perPage: '{select}', noRows: 'Không có dữ liệu', noResults: 'Không tìm thấy kết quả', info: 'Hiển thị {start}–{end} / {rows} dòng'}
    });
    // Phiên bản thư viện này tự chèn nhãn tiếng Anh, nên thay bằng nhãn Việt.
    const wrapper = adminTable.closest('.dataTable-wrapper');
    const pageSize = wrapper?.querySelector('.dataTable-selector');
    const pageLabel = wrapper?.querySelector('.dataTable-dropdown label');
    if (pageSize && pageLabel) {
        pageSize.id = 'admin-table-page-size';
        pageLabel.htmlFor = pageSize.id;
        pageLabel.textContent = 'dòng mỗi trang';
    }
    wrapper?.querySelector('.dataTable-input')?.setAttribute('aria-label', 'Tìm trong bảng');
}
</script>
</body>
</html>
