(function () {
    let isMeasuringLength = false;
    let points = [];
    let polyline = null;
    let tempSegmentLabel = null;
    let currentLengthGroup = null;

    // Nhóm Layer chứa tất cả các đường đo chiều dài đã hoàn thành
    let completedLengthGroup = null;

    document.addEventListener('DOMContentLoaded', () => {
        const btnLength = document.getElementById('btn-widget-measure-length');
        if (btnLength) {
            btnLength.addEventListener('click', toggleMeasureLength);
        }
    });

    function toggleMeasureLength() {
        if (typeof map === 'undefined') return;

        if (!isMeasuringLength && typeof window.stopAreaMeasurement === 'function') {
            window.stopAreaMeasurement();
        }

        if (!completedLengthGroup) {
            completedLengthGroup = L.layerGroup([], { pane: 'measurePane' }).addTo(map);
        }

        isMeasuringLength = !isMeasuringLength;
        Window.isMeasuringLength = isMeasuringLength;

        if (isMeasuringLength) {
            map.getContainer().style.cursor = 'crosshair';
            
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

    // 1. Khi nhấn 1 điểm: Lưu tọa độ
    function onMapClick(e) {
        if (!isMeasuringLength) return;

        const latlng = e.latlng;
        points.push(latlng);

        if (!currentLengthGroup) {
            currentLengthGroup = L.layerGroup().addTo(map);
        }

        // Tạo nhãn độ dài phân đoạn (chỉ là Tooltip/DivIcon)
        if (points.length > 1) {
            const p1 = points[points.length - 2];
            const p2 = points[points.length - 1];
            const segText = calculateDistanceText(p1, p2);
            const midLatLng = L.latLng((p1.lat + p2.lat) / 2, (p1.lng + p2.lng) / 2);

            const segLabel = createTooltipMarker(midLatLng, segText, 'measure-edge-label');
            currentLengthGroup.addLayer(segLabel);
        }

       
        updateDrawing([latlng]);
    }

    // 2. Di chuyển chuột: Cập nhật đường nét đứt & chiều dài phân đoạn theo thời gian thực
    function onMapMouseMove(e) {
        if (!isMeasuringLength || points.length === 0) return;

        const mouseLatLng = e.latlng;
        updateDrawing([mouseLatLng]);

        // Tính và hiển thị độ dài tức thời từ điểm cuối cùng đến chuột
        const lastPoint = points[points.length - 1];
        const currentSegText = calculateDistanceText(lastPoint, mouseLatLng);
        const midLatLng = L.latLng((lastPoint.lat + mouseLatLng.lat) / 2, (lastPoint.lng + mouseLatLng.lng) / 2);

        if (!tempSegmentLabel) {
            tempSegmentLabel = createTooltipMarker(midLatLng, currentSegText, 'measure-edge-label-live').addTo(map);
        } else {
            tempSegmentLabel.setLatLng(midLatLng);
            tempSegmentLabel.setIcon(createTooltipIcon(currentSegText, 'measure-edge-label-live'));
        }
    }

    function updateDrawing(extraPoints = []) {
        const currentPoints = points.concat(extraPoints);

        if (!polyline) {
            polyline = L.polyline(currentPoints, {
                color: '#ff4d4f', // Đường màu đỏ nổi bật cho đo chiều dài
                weight: 2,
                dashArray: '5, 5',
                pane: 'measurePane'
            }).addTo(map);
        } else {
            polyline.setLatLngs(currentPoints);
        }
    }

    // 3. Click chuột phải để kết thúc đường vẽ
    function onMapRightClick(e) {
        if (!isMeasuringLength || points.length < 2) return;

        // Xóa các phần tử tạm thời
        if (polyline) map.removeLayer(polyline);
        if (tempSegmentLabel) map.removeLayer(tempSegmentLabel);

        // Tạo Polyline cố định
        const finalPolyline = L.polyline(points, {
            color: '#ff4d4f',
            weight: 3,
            pane: 'measurePane'
        });

        // Tính tổng chiều dài toàn bộ đường gấp khúc
        const totalDistanceText = calculateTotalDistanceText(points);
        const lastPoint = points[points.length - 1];
        const totalLabel = createTooltipMarker(lastPoint, `Tổng: ${totalDistanceText}`, 'measure-total-length-label');

        if (currentLengthGroup) {
            currentLengthGroup.addLayer(finalPolyline);
            currentLengthGroup.addLayer(totalLabel);

            // Đưa vào Layer chung chứa các hình đã hoàn thành
            completedLengthGroup.addLayer(currentLengthGroup);
        }

        resetCurrentDrawing();
    }

    function resetCurrentDrawing() {
    points = [];
    if (polyline) map.removeLayer(polyline);
    if (tempSegmentLabel) map.removeLayer(tempSegmentLabel);
    polyline = null;
    tempSegmentLabel = null;
    currentLengthGroup = null;
}
    // --- TÍNH TOÁN & TẠO NHÃN ---

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

    function calculateTotalDistanceText(latlngs) {
        if (typeof turf === 'undefined') return '';
        let totalKm = 0;
        for (let i = 0; i < latlngs.length - 1; i++) {
            const from = turf.point([latlngs[i].lng, latlngs[i].lat]);
            const to = turf.point([latlngs[i + 1].lng, latlngs[i + 1].lat]);
            totalKm += turf.distance(from, to, { units: 'kilometers' });
        }

        if (totalKm < 1) {
            return Math.round(totalKm * 1000) + ' m';
        }
        return totalKm.toFixed(2) + ' km';
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

    // Xuất hàm xóa đo chiều dài cho Nút Bỏ Thao Tác sau này
    window.clearAllLengthMeasurements = function () {
        if (completedLengthGroup) {
            completedLengthGroup.clearLayers();
        }
        resetCurrentDrawing();
    };

    window.stopLengthMeasurement = function () {
    isMeasuringLength = false;
    window.isMeasuringLength = false;
    resetCurrentDrawing();
    if (typeof map !== 'undefined') {
        map.getContainer().style.cursor = '';
        map.off('click', onMapClick);
        map.off('mousemove', onMapMouseMove);
        map.off('contextmenu', onMapRightClick);
    }
};
})();

