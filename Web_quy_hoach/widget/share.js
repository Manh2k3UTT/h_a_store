// File: widget/share.js

// 1. Đọc tham số URL khi tải trang
window.addEventListener('load', function () {
    parseShareUrlParams();
});

// 2. Bắt sự kiện Click toàn cục (Event Delegation)
document.addEventListener('click', function (e) {
    // Click vào nút Chia sẻ (hoặc các phần tử con bên trong nút)
    const btnShare = e.target.closest('#btn-widget-share');
    if (btnShare) {
        console.log("-> Đã bấm nút Chia sẻ");
        openShareModal();
        return;
    }

    // Click vào nút Đóng Modal
    const btnClose = e.target.closest('#btn-close-share-modal');
    if (btnClose) {
        console.log("-> Đã bấm nút Đóng");
        closeShareModal();
        return;
    }

    // Click vào nút Sao chép
    const btnCopy = e.target.closest('#btn-copy-share-url');
    if (btnCopy) {
        console.log("-> Đã bấm nút Sao chép");
        copyShareUrl();
        return;
    }
});

// Hàm mở Modal
function openShareModal() {
    if (typeof map === 'undefined') {
        alert('Bản đồ chưa tải xong!');
        return;
    }

    // 1. Nếu chưa có Modal trong DOM, tự động khởi tạo HTML cho Modal
    let modal = document.getElementById('share-modal');
    if (!modal) {
        modal = createShareModalDOM();
    }

    // 2. Lấy thông tin vị trí bản đồ & Marker
    const center = map.getCenter();
    const zoom = map.getZoom();

    const url = new URL(window.location.origin + window.location.pathname);
    url.searchParams.set('lat', center.lat.toFixed(6));
    url.searchParams.set('lng', center.lng.toFixed(6));
    url.searchParams.set('zoom', zoom);

    if (typeof currentMarker !== 'undefined' && currentMarker) {
        const markerLatLng = currentMarker.getLatLng();
        url.searchParams.set('mlat', markerLatLng.lat.toFixed(6));
        url.searchParams.set('mlng', markerLatLng.lng.toFixed(6));
    }

    // 3. Đổ URL vào input và hiển thị Modal
    const inputEl = document.getElementById('share-url-input');
    if (inputEl) {
        inputEl.value = url.toString();
    }

    modal.classList.remove('hidden');
}

// Hàm tự động tạo HTML cho Share Modal nếu DOM chưa có
function createShareModalDOM() {
    const modalDiv = document.createElement('div');
    modalDiv.id = 'share-modal';
    modalDiv.className = 'widget-modal hidden';
    modalDiv.innerHTML = `
        <div class="widget-modal-content">
            <div class="widget-modal-header">
                <span>Chia sẻ vị trí bản đồ</span>
                <button id="btn-close-share-modal">&times;</button>
            </div>
            <div class="widget-modal-body">
                <input type="text" id="share-url-input" readonly>
                <button id="btn-copy-share-url">Sao chép</button>
            </div>
        </div>
    `;
    document.body.appendChild(modalDiv);
    return modalDiv;
}

// Hàm đóng Modal
function closeShareModal() {
    const modal = document.getElementById('share-modal');
    if (modal) {
        modal.classList.add('hidden');
    }
}

// Hàm Sao chép URL
function copyShareUrl() {
    const inputEl = document.getElementById('share-url-input');
    if (!inputEl || !inputEl.value) return;

    inputEl.select();
    navigator.clipboard.writeText(inputEl.value)
        .then(() => {
            const copyBtn = document.getElementById('btn-copy-share-url');
            if (copyBtn) {
                const originalText = copyBtn.innerText;
                copyBtn.innerText = 'Đã chép!';
                setTimeout(() => {
                    copyBtn.innerText = originalText;
                }, 2000);
            }
        })
        .catch(err => console.error('Lỗi sao chép:', err));
}

// Hàm đọc tham số URL
function parseShareUrlParams() {
    const params = new URLSearchParams(window.location.search);
    const lat = parseFloat(params.get('lat'));
    const lng = parseFloat(params.get('lng'));
    const zoom = parseInt(params.get('zoom'));

    const mlat = parseFloat(params.get('mlat'));
    const mlng = parseFloat(params.get('mlng'));

    const checkMapTimer = setInterval(() => {
        if (typeof map !== 'undefined' && map) {
            clearInterval(checkMapTimer);

            if (!isNaN(lat) && !isNaN(lng)) {
                map.setView([lat, lng], !isNaN(zoom) ? zoom : 16);
            }

            if (!isNaN(mlat) && !isNaN(mlng)) {
                const shareLatLng = L.latLng(mlat, mlng);
                if (typeof queryGeoServer === 'function') {
                    queryGeoServer(shareLatLng);
                }
            }
        }
    }, 100);
}