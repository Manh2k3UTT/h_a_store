document.addEventListener('DOMContentLoaded', () => {
    const btnCancel = document.getElementById('btn-widget-cancel');

    if (btnCancel) {
        btnCancel.addEventListener('click', (e) => {
            e.stopPropagation(); // Bật dòng này để ngăn sự kiện click lan ra bản đồ
            cancelAllActions();
        });
    }
});

function cancelAllActions() {
    if (typeof map === 'undefined') return;

    // 1. Dừng chế độ đo đạc đang chạy (nếu có)
    if (typeof window.stopAreaMeasurement === 'function') {
        window.stopAreaMeasurement();
    }
    if (typeof window.stopLengthMeasurement === 'function') {
        window.stopLengthMeasurement();
    }

    // 2. Xóa các lớp hình đã hoàn tất
    if (typeof window.clearAllAreaMeasurements === 'function') {
        window.clearAllAreaMeasurements();
    }
    if (typeof window.clearAllLengthMeasurements === 'function') {
        window.clearAllLengthMeasurements();
    }

    // 3. Đặt lại con trỏ chuột
    map.getContainer().style.cursor = '';
}

window.cancelAllActions = cancelAllActions;