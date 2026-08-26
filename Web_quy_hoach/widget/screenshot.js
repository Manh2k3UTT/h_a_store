document.addEventListener('click', function (e) {
    const btnScreenshot = e.target.closest('#btn-widget-screenshot');
    if (btnScreenshot) {
        takeMapScreenshot(btnScreenshot);
    }
});

async function takeMapScreenshot(btnEl) {
    if (typeof map === 'undefined') {
        alert('Bản đồ chưa tải xong!');
        return;
    }

    const originalOpacity = btnEl.style.opacity;
    btnEl.style.opacity = '0.3';
    btnEl.style.pointerEvents = 'none';

    try {
        const mapContainer = map.getContainer();
        const width = mapContainer.clientWidth;
        const height = mapContainer.clientHeight;

        // 1. Tạo Canvas tổng
        const finalCanvas = document.createElement('canvas');
        finalCanvas.width = width;
        finalCanvas.height = height;
        const ctx = finalCanvas.getContext('2d');

        // Màu nền trắng cho Canvas
        ctx.fillStyle = '#ffffff';
        ctx.fillRect(0, 0, width, height);

        // 2. Tải & Vẽ tất cả các mảnh bản đồ (Tile Img) lên Canvas
        const tileImages = mapContainer.querySelectorAll('.leaflet-tile-container img');
        const loadPromises = Array.from(tileImages).map(img => {
            return new Promise((resolve) => {
                if (!img.src) return resolve();
                
                const tempImg = new Image();
                tempImg.crossOrigin = 'anonymous';
                
                tempImg.onload = () => {
                    const rect = img.getBoundingClientRect();
                    const containerRect = mapContainer.getBoundingClientRect();
                    const x = rect.left - containerRect.left;
                    const y = rect.top - containerRect.top;
                    
                    try {
                        ctx.drawImage(tempImg, x, y, rect.width, rect.height);
                    } catch (e) {
                        console.warn('Không thể vẽ mảnh tile:', e);
                    }
                    resolve();
                };
                
                tempImg.onerror = () => resolve();
                tempImg.src = img.src;
            });
        });

        await Promise.all(loadPromises);

        // 3. SỬA LỖI: Quét tất cả thẻ Canvas (Polyline, Polygon) và ép đúng tỉ lệ resolution gốc
        const vectorCanvases = mapContainer.querySelectorAll('canvas');
        vectorCanvases.forEach(vCanvas => {
            const rect = vCanvas.getBoundingClientRect();
            const containerRect = mapContainer.getBoundingClientRect();
            
            const x = rect.left - containerRect.left;
            const y = rect.top - containerRect.top;
            const w = rect.width;
            const h = rect.height;

            if (w > 0 && h > 0) {
                // Dùng hàm drawImage 9 tham số để map đúng Pixel gốc (vCanvas.width/height) sang tọa độ màn hình (x, y, w, h)
                ctx.drawImage(
                    vCanvas, 
                    0, 0, vCanvas.width, vCanvas.height, 
                    x, y, w, h
                );
            }
        });

        // 4. Vẽ các Nhãn đo đạc (Text/Label) lên Canvas
        const tooltips = mapContainer.querySelectorAll('.leaflet-marker-icon, .leaflet-tooltip');
        tooltips.forEach(marker => {
            const rect = marker.getBoundingClientRect();
            const containerRect = mapContainer.getBoundingClientRect();
            const x = rect.left - containerRect.left;
            const y = rect.top - containerRect.top;

            const text = marker.innerText || marker.textContent;
            if (text && text.trim() !== '') {
                ctx.fillStyle = 'rgba(255, 255, 255, 0.9)';
                ctx.strokeStyle = '#1677ff';
                ctx.lineWidth = 1;
                
                ctx.font = 'bold 12px sans-serif';
                const textWidth = ctx.measureText(text.trim()).width + 16;
                const textHeight = 22;
                
                ctx.beginPath();
                if (ctx.roundRect) {
                    ctx.roundRect(x, y, textWidth, textHeight, 4);
                } else {
                    ctx.rect(x, y, textWidth, textHeight);
                }
                ctx.fill();
                ctx.stroke();

                ctx.fillStyle = '#000000';
                ctx.textAlign = 'center';
                ctx.textBaseline = 'middle';
                ctx.fillText(text.trim(), x + textWidth / 2, y + textHeight / 2);
            }
        });

        // 5. Tải ảnh về
        const downloadLink = document.createElement('a');
        downloadLink.href = finalCanvas.toDataURL('image/png');
        downloadLink.download = `ban-do-quy-hoach-${Date.now()}.png`;
        document.body.appendChild(downloadLink);
        downloadLink.click();
        document.body.removeChild(downloadLink);

    } catch (err) {
        console.error('Lỗi khi chụp bản đồ:', err);
        alert('Có lỗi xảy ra khi xuất ảnh bản đồ!');
    } finally {
        btnEl.style.opacity = originalOpacity || '1';
        btnEl.style.pointerEvents = 'auto';
    }
}