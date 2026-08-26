const GEOSERVER_URL = "http://localhost:8080/geoserver/quyhoach/wms";

// Duy nhất 1 biến quản lý Marker trên toàn bản đồ
let currentMarker = null;
let activeWmsLayers = {};
let layerOpacities = {}; 

// ==========================================
// 1. NẠP GIAO DIỆN SIDEBAR TỪ FILE DỰNG SẴN
// ==========================================
window.addEventListener('DOMContentLoaded', function () {
    fetch('project-sidebar.html')
        .then(response => response.text())
        .then(html => {
            const container = document.getElementById('sidebar-container');
            if (container) {
                container.innerHTML = html;
                const btnClose = document.getElementById('btn-close-sidebar');
                if (btnClose) btnClose.addEventListener('click', resetSidebar);
            }
        })
        .catch(err => console.error("Lỗi nạp sidebar:", err));
});

// ==========================================
// 2. LẮNG NGHE SỰ KIỆN CLICK TRÊN BẢN ĐỒ
// ==========================================
window.addEventListener('load', function () {
    if (typeof map === 'undefined') return;

    map.on('click', function (e) {
        if (window.isMeasuringArea || window.isMeasuringLength) {
            return;
        }

        const mapContainer = map.getContainer();
        if (mapContainer && mapContainer.style.cursor === 'crosshair') {
            return;
        }
        queryGeoServer(e.latlng);
    });
});

// ==========================================
// 3. TRUY VẤN GEOSERVER (GetFeatureInfo)
// ==========================================
function queryGeoServer(latlng) {
    // Luôn xóa Marker cũ và cắm Marker mới tại vị trí click
    if (currentMarker) {
        map.removeLayer(currentMarker);
    }
    currentMarker = L.marker(latlng).addTo(map);

    const point = map.latLngToContainerPoint(latlng, map.getZoom());
    const size = map.getSize();

    const params = {
        request: 'GetFeatureInfo',
        service: 'WMS',
        srs: 'EPSG:4326',
        styles: '',
        transparent: true,
        version: '1.1.1',
        format: 'image/png',
        bbox: map.getBounds().toBBoxString(),
        height: size.y,
        width: size.x,
        layers: 'quyhoach:du_an',
        query_layers: 'quyhoach:du_an',
        info_format: 'application/json',
        x: Math.round(point.x),
        y: Math.round(point.y),
        buffer: 20
    };

    const url = GEOSERVER_URL + L.Util.getParamString(params, GEOSERVER_URL);

    fetch(url)
        .then(res => res.json())
        .then(data => {
            // Nếu có dữ liệu mới -> Cập nhật thông tin Sidebar
            if (data.features && data.features.length > 0) {
                renderSidebarDetail(data.features[0].properties);
            }
            // Nếu không có dữ liệu -> Bỏ qua, Marker vẫn ở vị trí mới và Sidebar giữ nguyên dữ liệu cũ
        })
        .catch(err => {
            console.error("Lỗi kết nối GeoServer:", err);
        });
}

// ==========================================
// 4. ĐỔ DỮ LIỆU VÀO KHUNG CHI TIẾT
// ==========================================
function renderSidebarDetail(props) {
    const emptyState = document.getElementById('sidebar-empty');
    const detailState = document.getElementById('sidebar-detail');
    if (!emptyState || !detailState) return;

    emptyState.classList.add('hidden');
    detailState.classList.remove('hidden');

    document.getElementById('info-ten-du-an').innerText = props.ten_du_an || '--';
    
    let thongTinObj = {};
    if (typeof props.thong_tin === 'string') {
        try { thongTinObj = JSON.parse(props.thong_tin); } 
        catch (e) { thongTinObj = { "Mô tả": props.thong_tin }; }
    } else if (typeof props.thong_tin === 'object' && props.thong_tin !== null) {
        thongTinObj = props.thong_tin;
    }

    document.getElementById('info-dia-diem').innerText = thongTinObj["Địa điểm"] || props.dia_diem || '--';
    document.getElementById('info-dien-tich').innerText = thongTinObj["Diện tích"] || props.dien_tich || '--';
    document.getElementById('info-dan-so').innerText = thongTinObj["Dân số"] || props.dan_so || '--';
    document.getElementById('info-quyet-dinh').innerText = thongTinObj["Quyết định"] || props.quyet_dinh || thongTinObj["Mô tả"] || '--';
    document.getElementById('info-don-vi').innerText = thongTinObj["Cơ quan phê duyệt"] || props.don_vi || '--';

    // File đính kèm
    const filesContainer = document.getElementById('info-files-container');
    filesContainer.innerHTML = '';
    let fileList = [];
    if (typeof props.file_van_ban === 'string' && props.file_van_ban.trim() !== '') {
        try { fileList = JSON.parse(props.file_van_ban); } 
        catch (e) { fileList = [props.file_van_ban]; }
    } else if (Array.isArray(props.file_van_ban)) {
        fileList = props.file_van_ban;
    }

    if (fileList.length > 0) {
        fileList.forEach((filePath, idx) => {
            filesContainer.innerHTML += `
                <a href="${filePath}" target="_blank" class="file-item" style="display:block; margin-bottom:5px;">
                    📄 Văn bản pháp lý đính kèm ${idx + 1}
                </a>`;
        });
    } else {
        filesContainer.innerHTML = '<span style="color:#888; font-size:12px;">Không có file đính kèm</span>';
    }

    // Danh sách Layer Bản Vẽ
    const layersContainer = document.getElementById('info-layers-container');
    const template = document.getElementById('tpl-layer-item');
    layersContainer.innerHTML = '';

    let layerList = [];
    if (typeof props.raster_layers === 'string' && props.raster_layers.trim() !== '') {
        try { layerList = JSON.parse(props.raster_layers); } 
        catch (e) { layerList = [props.raster_layers]; }
    } else if (Array.isArray(props.raster_layers)) {
        layerList = props.raster_layers;
    }

    if (layerList.length > 0 && template) {
        layerList.forEach(layerName => {
            const clone = template.content.cloneNode(true);
            
            const nameEl = clone.querySelector('.layer-name');
            const checkboxEl = clone.querySelector('.layer-checkbox');
            const opacityEl = clone.querySelector('.layer-opacity');
            const opacityValEl = clone.querySelector('.opacity-val');
            
            nameEl.innerText = layerName;
            checkboxEl.checked = !!activeWmsLayers[layerName];
            
            const currentOpacity = layerOpacities[layerName] !== undefined ? layerOpacities[layerName] : 1;
            opacityEl.value = currentOpacity;
            if (opacityValEl) {
                opacityValEl.innerText = Math.round(currentOpacity * 100) + '%';
            }
            
            checkboxEl.addEventListener('change', (e) => toggleWmsLayer(layerName, e.target.checked, opacityEl));
            opacityEl.addEventListener('input', (e) => changeWmsOpacity(layerName, e.target.value, opacityValEl));
            
            layersContainer.appendChild(clone);
        });
    } else {
        layersContainer.innerHTML = '<span style="color:#888; font-size:12px;">Không có bản vẽ đi kèm</span>';
    }
}

// ==========================================
// 5. CÁC HÀM BỔ TRỢ
// ==========================================
function toggleWmsLayer(layerName, isChecked, opacityInputEl) {
    if (isChecked) {
        if (!map.getPane('rasterPane')) {
            map.createPane('rasterPane');
            map.getPane('rasterPane').style.zIndex = 500;
        }

        if (activeWmsLayers[layerName]) {
            map.removeLayer(activeWmsLayers[layerName]);
        }

        const currentOpacity = opacityInputEl ? parseFloat(opacityInputEl.value) : 1;
        layerOpacities[layerName] = currentOpacity;

        const wmsLayer = L.tileLayer.wms(GEOSERVER_URL, {
            layers: layerName,
            format: 'image/png',
            transparent: true,
            pane: 'rasterPane',
            maxZoom: 22,
            maxNativeZoom: 18,
            opacity: currentOpacity
        }).addTo(map);
        
        activeWmsLayers[layerName] = wmsLayer;
    } else {
        if (activeWmsLayers[layerName]) {
            map.removeLayer(activeWmsLayers[layerName]);
            delete activeWmsLayers[layerName];
        }
    }
}

function changeWmsOpacity(layerName, opacityValue, opacityValEl) {
    const opacity = parseFloat(opacityValue);
    
    layerOpacities[layerName] = opacity;
    
    if (opacityValEl) {
        opacityValEl.innerText = Math.round(opacity * 100) + '%';
    }
    
    if (activeWmsLayers[layerName]) {
        activeWmsLayers[layerName].setOpacity(opacity);
    }
}

function resetSidebar() {
    const emptyState = document.getElementById('sidebar-empty');
    const detailState = document.getElementById('sidebar-detail');

    if (emptyState && detailState) {
        detailState.classList.add('hidden');
        emptyState.classList.remove('hidden');
    }

    // Xóa marker duy nhất khỏi bản đồ khi người dùng đóng Sidebar
    if (currentMarker) {
        map.removeLayer(currentMarker);
        currentMarker = null;
    }
}