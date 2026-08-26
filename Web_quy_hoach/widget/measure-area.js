(function () {
    let isMeasuringArea = false;
    let points = [];
    let polyline = null;
    let polygon = null;
    let areaLabelMarker = null;

    // Nhóm Layer chứa tất cả các hình đã vẽ hoàn chỉnh
    let completedMeasureGroup = null;

    document.addEventListener('DOMContentLoaded', () => {
        const btnArea = document.getElementById('btn-widget-measure-area');
        if (btnArea) {
            btnArea.addEventListener('click', toggleMeasureArea);
        }
    });

    function toggleMeasureArea() {
        if (typeof map === 'undefined') return;

        if (!isMeasuringArea && typeof window.stopLengthMeasurement === 'function') {
            window.stopLengthMeasurement();
        }

        if (!completedMeasureGroup) {
            completedMeasureGroup = L.layerGroup([], { pane: 'measurePane' }).addTo(map);
        }

        isMeasuringArea = !isMeasuringArea;
        Window.isMeasuringArea = isMeasuringArea;
        if (isMeasuringArea) {
            // Đổi con trỏ chuột thành hình chữ thập khi kích hoạt
            map.getContainer().style.cursor = 'crosshair';
            
            // Lắng nghe các sự kiện chuột trên bản đồ
            map.on('click', onMapClick);
            map.on('mousemove', onMapMouseMove);
            map.on('contextmenu', onMapRightClick);
        } else {
            resetCurrentDrawing();
            map.getContainer().style.cursor = '';
            map.off('click', onMapClick);
            map.off('mousemove', onMapMouseMove);
            map.off('contextmenu', onMapRightClick);
        }
    }

    // 1. Khi nhấn 1 điểm trên bản đồ: Chỉ lưu tọa độ, KHÔNG tạo chấm nhỏ
    function onMapClick(e) {
        if (!isMeasuringArea) return;

        const latlng = e.latlng;
        points.push(latlng);

        updateDrawing([latlng]);
    }

    // 2. Cập nhật đường nối và polygon theo con trỏ chuột
    function onMapMouseMove(e) {
        if (!isMeasuringArea || points.length === 0) return;
        updateDrawing([e.latlng]);
    }

    function updateDrawing(extraPoints = []) {
        const currentPoints = points.concat(extraPoints);

        // Vẽ đường nét đứt phác thảo
        if (!polyline) {
            polyline = L.polyline(currentPoints, {
                color: '#1677ff',
                weight: 2,
                dashArray: '5, 5',
                pane: 'measurePane'
            }).addTo(map);
        } else {
            polyline.setLatLngs(currentPoints);
        }

        // Khi có từ 3 điểm trở lên (tính cả điểm theo chuột)
        if (currentPoints.length >= 3) {
            if (!polygon) {
                polygon = L.polygon(currentPoints, {
                    color: '#1677ff',
                    weight: 2,
                    fillColor: '#1677ff',
                    fillOpacity: 0.2,
                    pane: 'measurePane'
                }).addTo(map);
            } else {
                polygon.setLatLngs(currentPoints);
            }

            // Hiển thị diện tích tạm thời ở tâm
            const areaText = calculateAreaText(currentPoints);
            const centerLatLng = polygon.getBounds().getCenter();

            if (!areaLabelMarker) {
                areaLabelMarker = createTooltipMarker(centerLatLng, areaText, 'measure-area-label').addTo(map);
            } else {
                areaLabelMarker.setLatLng(centerLatLng);
                areaLabelMarker.setIcon(createTooltipIcon(areaText, 'measure-area-label'));
            }
        }
    }

    // 3. Kết thúc vẽ bằng click chuột phải (nhấp đúp/bấm 2 lần)
    function onMapRightClick(e) {
        if (!isMeasuringArea || points.length < 3) return;

        // Xóa tạm thời các nét phác thảo
        if (polyline) map.removeLayer(polyline);
        if (polygon) map.removeLayer(polygon);
        if (areaLabelMarker) map.removeLayer(areaLabelMarker);

        // Vẽ Polygon cố định
        const finalPolygon = L.polygon(points, {
            color: '#1677ff',
            weight: 2,
            fillColor: '#1677ff',
            fillOpacity: 0.25,
            pane: 'measurePane'
        });

        // Nhãn diện tích ở trung tâm
        const finalAreaText = calculateAreaText(points);
        const centerLatLng = finalPolygon.getBounds().getCenter();
        const finalAreaLabel = createTooltipMarker(centerLatLng, finalAreaText, 'measure-area-label-final');

        const currentShapeGroup = L.layerGroup([finalPolygon, finalAreaLabel]);

        // Tạo nhãn độ dài cho từng cạnh
        for (let i = 0; i < points.length; i++) {
            const p1 = points[i];
            const p2 = points[(i + 1) % points.length];

            const lengthText = calculateDistanceText(p1, p2);
            const midLatLng = L.latLng((p1.lat + p2.lat) / 2, (p1.lng + p2.lng) / 2);
            
            const edgeLabel = createTooltipMarker(midLatLng, lengthText, 'measure-edge-label');
            currentShapeGroup.addLayer(edgeLabel);
        }

        // Đưa hình hoàn chỉnh vào nhóm chung
        completedMeasureGroup.addLayer(currentShapeGroup);

        // Reset trạng thái để sẵn sàng vẽ hình tiếp theo
        resetCurrentDrawing();
    }

    function resetCurrentDrawing() {
        points = [];
        if (polyline) map.removeLayer(polyline);
        if (polygon) map.removeLayer(polygon);
        if (areaLabelMarker) map.removeLayer(areaLabelMarker);
        polyline = null;
        polygon = null;
        areaLabelMarker = null;
    }

    // --- CÁC HÀM TÍNH TOÁN & TẠO NHÃN ---

    function calculateAreaText(latlngs) {
    if (typeof turf === 'undefined') return 'Chưa nạp Turf.js';
    
    const coordinates = latlngs.map(pt => [pt.lng, pt.lat]);
    coordinates.push(coordinates[0]);

    const polygonGeoJSON = turf.polygon([coordinates]);
    const areaSqMeters = turf.area(polygonGeoJSON);

    // Nếu diện tích lớn hơn hoặc bằng 1,000,000 m² (1 km²) thì chuyển sang km²
    if (areaSqMeters >= 1000000) {
        return (areaSqMeters / 1000000).toFixed(2) + ' km²';
    }
    
    // Tất cả các trường hợp nhỏ hơn 1 km² đều hiển thị dạng m²
    return Math.round(areaSqMeters).toLocaleString() + ' m²';
}

    function calculateDistanceText(p1, p2) {
        if (typeof turf === 'undefined') return '';
        const from = turf.point([p1.lng, p1.lat]);
        const to = turf.point([p2.lng, p2.lat]);
        const distanceKm = turf.distance(from, to, { units: 'kilometers' });

        if (distanceKm < 1) {
            return Math.round(distanceKm * 1000) + ' m';
        }
        return distanceKm.toFixed(2) + ' km';
    }

    function createTooltipIcon(text, className) {
        return L.divIcon({
            className: `measure-tooltip ${className}`,
            html: `<span>${text}</span>`,
            iconSize: null
        });
    }

    function createTooltipMarker(latlng, text, className) {
        return L.marker(latlng, {
            icon: createTooltipIcon(text, className),
            interactive: false
        });
    }

    window.clearAllAreaMeasurements = function () {
        if (completedMeasureGroup) {
            completedMeasureGroup.clearLayers();
        }
        resetCurrentDrawing();
    };

    window.stopAreaMeasurement = function () {
    isMeasuringArea = false;
    window.isMeasuringArea = false;
    resetCurrentDrawing();
    if (typeof map !== 'undefined') {
        map.getContainer().style.cursor = '';
        map.off('click', onMapClick);
        map.off('mousemove', onMapMouseMove);
        map.off('contextmenu', onMapRightClick);
    }
};
})();

