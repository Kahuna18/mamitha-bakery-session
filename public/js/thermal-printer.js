/**
 * Mamitha Bakery — Bluetooth Thermal Printer (ESC/POS)
 * Supports 58mm (32 chars/line) and 80mm (48 chars/line) printers.
 *
 * Usage:
 *   await ThermalPrinter.connect();          // pair & connect
 *   await ThermalPrinter.printReceipt(data); // print receipt object
 *   ThermalPrinter.disconnect();             // disconnect
 */
const ThermalPrinter = (() => {
    // ── state ──────────────────────────────────────────────────────────
    let device = null;
    let server = null;
    let characteristic = null;
    let connected = false;
    let paperWidth = 48; // default 80mm (48 chars), 58mm = 32 chars

    // Common BLE serial / printer service UUIDs
    const SERVICE_UUIDS = [
        '000018f0-0000-1000-8000-00805f9b34fb', // common thermal printer
        '49535343-fe7d-4ae5-8fa9-9fafd205e455', // ISSC / many Chinese printers
        '0000ff00-0000-1000-8000-00805f9b34fb', // alt common
        'e7810a71-73ae-499d-8c15-faa9aef0c3f2', // Nordic UART
    ];

    const CHAR_UUIDS = [
        '00002af1-0000-1000-8000-00805f9b34fb',
        '49535343-8841-43f4-a8d4-ecbe34729bb3',
        '0000ff02-0000-1000-8000-00805f9b34fb',
        'bef8d6c9-9c21-4c9e-b632-bd58c1009f9f',
    ];

    // ── ESC/POS helpers ────────────────────────────────────────────────
    const ESC = 0x1B;
    const GS  = 0x1D;
    const LF  = 0x0A;

    const cmd = {
        init:          [ESC, 0x40],                 // initialise printer
        alignLeft:     [ESC, 0x61, 0x00],
        alignCenter:   [ESC, 0x61, 0x01],
        alignRight:    [ESC, 0x61, 0x02],
        boldOn:        [ESC, 0x45, 0x01],
        boldOff:       [ESC, 0x45, 0x00],
        doubleHeight:  [ESC, 0x21, 0x10],
        normalSize:    [ESC, 0x21, 0x00],
        doubleWidth:   [GS,  0x21, 0x10],
        cut:           [GS,  0x56, 0x00],           // full cut
        partialCut:    [GS,  0x56, 0x01],
        feedLines: (n) => [ESC, 0x64, n],
    };

    function textToBytes(text) {
        const encoder = new TextEncoder();
        return encoder.encode(text);
    }

    function line(char = '-') {
        return char.repeat(paperWidth);
    }

    // ── low-level write (chunked for BLE MTU) ──────────────────────────
    async function writeRaw(bytes) {
        if (!characteristic) throw new Error('Printer belum terhubung');
        const chunk = 100; // safe BLE chunk
        const data = bytes instanceof Uint8Array ? bytes : new Uint8Array(bytes);
        for (let i = 0; i < data.length; i += chunk) {
            const slice = data.slice(i, i + chunk);
            await characteristic.writeValueWithoutResponse(slice);
            await sleep(30);
        }
    }

    async function writeLine(text = '') {
        await writeRaw([...textToBytes(text), LF]);
    }

    function sleep(ms) {
        return new Promise(r => setTimeout(r, ms));
    }

    // pad / truncate helpers for columns
    function padRight(str, len) {
        str = String(str);
        return str.length >= len ? str.substring(0, len) : str + ' '.repeat(len - str.length);
    }
    function padLeft(str, len) {
        str = String(str);
        return str.length >= len ? str.substring(0, len) : ' '.repeat(len - str.length) + str;
    }

    function formatCurrency(num) {
        return 'Rp' + Number(num).toLocaleString('id-ID');
    }

    // ── Connect ────────────────────────────────────────────────────────
    async function connect(options = {}) {
        if (connected) return true;

        if (!navigator.bluetooth) {
            showToast('Browser tidak mendukung Bluetooth. Gunakan Chrome/Edge.', 'error');
            throw new Error('Web Bluetooth API not available');
        }

        try {
            showToast('Mencari printer Bluetooth…', 'info');

            // Build filter list — accept any of the known service UUIDs
            const filters = SERVICE_UUIDS.map(u => ({ services: [u] }));

            device = await navigator.bluetooth.requestDevice({
                filters,
                optionalServices: SERVICE_UUIDS,
            });

            device.addEventListener('gattserverdisconnected', onDisconnected);

            showToast('Menghubungkan ke ' + device.name + '…', 'info');
            server = await device.gatt.connect();

            // Find a writable characteristic across known services
            characteristic = null;
            for (const sUuid of SERVICE_UUIDS) {
                try {
                    const svc = await server.getPrimaryService(sUuid);
                    const chars = await svc.getCharacteristics();
                    for (const ch of chars) {
                        if (ch.properties.writeWithoutResponse || ch.properties.write) {
                            characteristic = ch;
                            break;
                        }
                    }
                    if (characteristic) break;
                } catch (_) { /* try next */ }
            }

            if (!characteristic) {
                throw new Error('Tidak ditemukan karakteristik yang bisa ditulis');
            }

            connected = true;

            // Detect paper width from options or saved preference
            if (options.paperWidth) {
                paperWidth = options.paperWidth === '58mm' ? 32 : 48;
            } else {
                const saved = localStorage.getItem('mamitha_printer_width');
                if (saved) paperWidth = parseInt(saved, 10);
            }

            localStorage.setItem('mamitha_printer_device', device.name || 'Unknown');
            updateStatusUI();
            showToast('Printer ' + (device.name || '') + ' terhubung ✓', 'success');
            return true;
        } catch (err) {
            connected = false;
            if (err.name === 'NotFoundError') {
                showToast('Tidak ada printer yang dipilih.', 'warning');
            } else {
                showToast('Gagal menghubungkan: ' + err.message, 'error');
            }
            updateStatusUI();
            throw err;
        }
    }

    function disconnect() {
        if (device && device.gatt.connected) {
            device.gatt.disconnect();
        }
        connected = false;
        characteristic = null;
        server = null;
        updateStatusUI();
        showToast('Printer terputus', 'warning');
    }

    function onDisconnected() {
        connected = false;
        characteristic = null;
        server = null;
        updateStatusUI();
        showToast('Printer Bluetooth terputus', 'warning');
    }

    function isConnected() {
        return connected;
    }

    function setPaperWidth(width) {
        paperWidth = width === '58mm' ? 32 : 48;
        localStorage.setItem('mamitha_printer_width', paperWidth);
    }

    // ── Print Receipt ──────────────────────────────────────────────────
    /**
     * @param {Object} data
     * @param {string} data.storeName
     * @param {string} [data.storeAddress]
     * @param {string} [data.storePhone]
     * @param {string} data.type          — 'invoice' | 'kitchen'
     * @param {string} data.orderNumber
     * @param {string} data.date
     * @param {string} data.pickupDate
     * @param {string} data.customerName
     * @param {string} data.customerPhone
     * @param {string} [data.address]
     * @param {string} [data.orderType]   — 'pickup' | 'delivery'
     * @param {Array}  data.items         — [{name, quantity, subtotal}]
     * @param {number} [data.total]
     * @param {string} [data.status]
     * @param {string} [data.notes]
     */
    async function printReceipt(data) {
        if (!connected) {
            await connect();
        }

        try {
            // ── Init ──
            await writeRaw(cmd.init);
            await sleep(50);

            // ── Header ──
            await writeRaw(cmd.alignCenter);
            await writeRaw(cmd.boldOn);
            await writeRaw(cmd.doubleHeight);
            await writeLine(data.storeName || 'Mamitha Bakery');
            await writeRaw(cmd.normalSize);
            await writeRaw(cmd.boldOff);

            if (data.storeAddress) await writeLine(data.storeAddress);
            if (data.storePhone)   await writeLine(data.storePhone);

            await writeLine('');
            await writeRaw(cmd.boldOn);
            await writeLine(data.type === 'kitchen' ? '** ORDER DAPUR **' : '** INVOICE **');
            await writeRaw(cmd.boldOff);
            await writeLine(line('='));

            // ── Order info ──
            await writeRaw(cmd.alignLeft);
            await writeLine('No    : ' + data.orderNumber);
            if (data.date)       await writeLine('Tgl   : ' + data.date);
            if (data.pickupDate) await writeLine('Ambil : ' + data.pickupDate);
            await writeLine('Nama  : ' + data.customerName);
            if (data.customerPhone) await writeLine('WA    : ' + data.customerPhone);
            if (data.orderType) {
                await writeLine('Tipe  : ' + (data.orderType === 'pickup' ? 'Ambil di Toko' : 'Diantar'));
            }
            if (data.address) await writeLine('Alamat: ' + data.address);

            await writeLine(line('-'));

            // ── Items ──
            if (data.items && data.items.length > 0 && typeof data.items[0].price !== 'undefined') {
                // Premium layout: prints Item Name on line 1, and Qty x Price ... Subtotal on line 2
                for (const item of data.items) {
                    // Line 1: Item name
                    await writeLine(item.name);
                    
                    // Line 2: Qty x Price and Subtotal
                    const qtyPrice = '  ' + item.quantity + ' x ' + formatCurrency(item.price);
                    const subtotal = formatCurrency(item.subtotal);
                    const spacesNeeded = paperWidth - qtyPrice.length - subtotal.length;
                    const spaces = ' '.repeat(Math.max(1, spacesNeeded));
                    await writeLine(qtyPrice + spaces + subtotal);
                }

                await writeLine(line('-'));
                if (typeof data.total !== 'undefined') {
                    await writeRaw(cmd.boldOn);
                    await writeRaw(cmd.alignRight);
                    await writeLine('TOTAL: ' + formatCurrency(data.total));
                    await writeRaw(cmd.boldOff);
                    await writeRaw(cmd.alignLeft);
                }

                if (data.status) {
                    await writeLine('Status: ' + data.status);
                }
            } else {
                // Fallback/Kitchen layout: name + qty only
                for (const item of data.items) {
                    const name = padRight(item.name, paperWidth - 6);
                    const qty  = padLeft('x' + item.quantity, 5);
                    await writeLine(name + qty);
                }
            }

            // ── Notes ──
            if (data.notes) {
                await writeLine(line('-'));
                await writeRaw(cmd.boldOn);
                await writeLine('Catatan:');
                await writeRaw(cmd.boldOff);
                await writeLine(data.notes);
            }

            // ── Footer ──
            await writeLine(line('='));
            await writeRaw(cmd.alignCenter);
            if (data.type !== 'kitchen') {
                await writeLine('Terima kasih telah berbelanja!');
                await writeLine('Barang yang sudah dibeli');
                await writeLine('tidak dapat ditukar');
            } else {
                await writeLine('--- SELESAI ---');
            }
            await writeLine('');

            // Feed & cut
            await writeRaw(cmd.feedLines(4));
            await writeRaw(cmd.partialCut);
            await sleep(100);

            showToast('Struk berhasil dicetak ✓', 'success');
        } catch (err) {
            showToast('Gagal mencetak: ' + err.message, 'error');
            throw err;
        }
    }

    // ── Test Print ─────────────────────────────────────────────────────
    async function testPrint() {
        if (!connected) await connect();

        await writeRaw(cmd.init);
        await writeRaw(cmd.alignCenter);
        await writeRaw(cmd.boldOn);
        await writeRaw(cmd.doubleHeight);
        await writeLine('Mamitha Bakery');
        await writeRaw(cmd.normalSize);
        await writeRaw(cmd.boldOff);
        await writeLine('');
        await writeLine('=== TEST PRINT ===');
        await writeLine('Printer ' + (paperWidth === 32 ? '58mm' : '80mm'));
        await writeLine('Koneksi Bluetooth OK');
        await writeLine('');
        await writeLine(new Date().toLocaleString('id-ID'));
        await writeLine('');
        await writeRaw(cmd.feedLines(3));
        await writeRaw(cmd.partialCut);

        showToast('Test print berhasil ✓', 'success');
    }

    // ── UI Helpers ─────────────────────────────────────────────────────
    function updateStatusUI() {
        const el = document.getElementById('printer-status');
        if (!el) return;
        if (connected) {
            el.innerHTML = `
                <span class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-full text-xs font-medium bg-green-100 text-green-700">
                    <span class="w-2 h-2 rounded-full bg-green-500 animate-pulse"></span>
                    🖨️ ${device?.name || 'Printer'}
                </span>`;
        } else {
            el.innerHTML = `
                <span class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-full text-xs font-medium bg-gray-100 text-gray-500">
                    <span class="w-2 h-2 rounded-full bg-gray-400"></span>
                    Printer tidak terhubung
                </span>`;
        }
    }

    let toastTimeout = null;
    function showToast(message, type = 'info') {
        let container = document.getElementById('printer-toast');
        if (!container) {
            container = document.createElement('div');
            container.id = 'printer-toast';
            container.style.cssText = 'position:fixed;top:20px;right:20px;z-index:99999;display:flex;flex-direction:column;gap:8px;';
            document.body.appendChild(container);
        }

        const colors = {
            success: 'background:#065f46;color:#d1fae5;',
            error:   'background:#991b1b;color:#fecaca;',
            warning: 'background:#92400e;color:#fef3c7;',
            info:    'background:#1e40af;color:#dbeafe;',
        };

        const toast = document.createElement('div');
        toast.style.cssText = `${colors[type] || colors.info}padding:12px 20px;border-radius:12px;font-size:14px;font-family:Inter,sans-serif;box-shadow:0 8px 24px rgba(0,0,0,.2);max-width:340px;animation:printerToastIn .3s ease;`;
        toast.textContent = message;
        container.appendChild(toast);

        // inject keyframes once
        if (!document.getElementById('printer-toast-style')) {
            const style = document.createElement('style');
            style.id = 'printer-toast-style';
            style.textContent = `
                @keyframes printerToastIn { from { opacity:0; transform:translateX(40px); } to { opacity:1; transform:translateX(0); } }
                @keyframes printerToastOut { from { opacity:1; transform:translateX(0); } to { opacity:0; transform:translateX(40px); } }
            `;
            document.head.appendChild(style);
        }

        setTimeout(() => {
            toast.style.animation = 'printerToastOut .3s ease forwards';
            setTimeout(() => toast.remove(), 350);
        }, 3500);
    }

    // ── Printer Settings Modal ─────────────────────────────────────────
    function openSettings() {
        const existing = document.getElementById('printer-settings-modal');
        if (existing) existing.remove();

        const savedWidth = localStorage.getItem('mamitha_printer_width') || '48';
        const savedDevice = localStorage.getItem('mamitha_printer_device') || '-';
        const is58 = savedWidth === '32';

        const modal = document.createElement('div');
        modal.id = 'printer-settings-modal';
        modal.innerHTML = `
        <div style="position:fixed;inset:0;background:rgba(0,0,0,.5);z-index:99990;display:flex;align-items:center;justify-content:center;" onclick="if(event.target===this)this.parentElement.remove()">
            <div style="background:#fff;border-radius:16px;padding:28px;width:400px;max-width:90vw;box-shadow:0 20px 60px rgba(0,0,0,.3);font-family:Inter,sans-serif;">
                <h3 style="margin:0 0 4px;font-size:18px;font-weight:700;color:#1f2937;">🖨️ Pengaturan Printer</h3>
                <p style="margin:0 0 20px;font-size:13px;color:#6b7280;">Hubungkan printer thermal Bluetooth</p>

                <div style="background:#f9fafb;border-radius:12px;padding:16px;margin-bottom:16px;">
                    <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:8px;">
                        <span style="font-size:13px;color:#6b7280;">Status</span>
                        <span style="font-size:13px;font-weight:600;color:${connected ? '#059669' : '#9ca3af'}">
                            ${connected ? '● Terhubung' : '○ Tidak terhubung'}
                        </span>
                    </div>
                    <div style="display:flex;justify-content:space-between;align-items:center;">
                        <span style="font-size:13px;color:#6b7280;">Perangkat</span>
                        <span style="font-size:13px;font-weight:500;color:#374151">${connected ? (device?.name || 'Unknown') : savedDevice}</span>
                    </div>
                </div>

                <label style="display:block;font-size:13px;font-weight:600;color:#374151;margin-bottom:6px;">Ukuran Kertas</label>
                <div style="display:flex;gap:8px;margin-bottom:20px;">
                    <button onclick="ThermalPrinter.setPaperWidth('58mm');this.style.background='#d97706';this.style.color='#fff';this.nextElementSibling.style.background='#f3f4f6';this.nextElementSibling.style.color='#374151';"
                        style="flex:1;padding:10px;border-radius:10px;border:none;cursor:pointer;font-weight:600;font-size:13px;transition:all .2s;
                        ${is58 ? 'background:#d97706;color:#fff;' : 'background:#f3f4f6;color:#374151;'}">
                        58mm <span style="font-weight:400;opacity:.7;">(32 char)</span>
                    </button>
                    <button onclick="ThermalPrinter.setPaperWidth('80mm');this.style.background='#d97706';this.style.color='#fff';this.previousElementSibling.style.background='#f3f4f6';this.previousElementSibling.style.color='#374151';"
                        style="flex:1;padding:10px;border-radius:10px;border:none;cursor:pointer;font-weight:600;font-size:13px;transition:all .2s;
                        ${!is58 ? 'background:#d97706;color:#fff;' : 'background:#f3f4f6;color:#374151;'}">
                        80mm <span style="font-weight:400;opacity:.7;">(48 char)</span>
                    </button>
                </div>

                <div style="display:flex;gap:8px;">
                    ${connected
                        ? `<button onclick="ThermalPrinter.disconnect();document.getElementById('printer-settings-modal').remove();"
                                style="flex:1;padding:12px;border-radius:10px;border:none;background:#fef2f2;color:#dc2626;font-weight:600;cursor:pointer;font-size:13px;">
                                Putuskan
                           </button>
                           <button onclick="ThermalPrinter.testPrint();"
                                style="flex:1;padding:12px;border-radius:10px;border:none;background:#f0fdf4;color:#16a34a;font-weight:600;cursor:pointer;font-size:13px;">
                                Test Print
                           </button>`
                        : `<button onclick="ThermalPrinter.connect().then(()=>{document.getElementById('printer-settings-modal')?.remove();ThermalPrinter.openSettings();});"
                                style="flex:1;padding:12px;border-radius:10px;border:none;background:#d97706;color:#fff;font-weight:600;cursor:pointer;font-size:13px;">
                                🔗 Hubungkan Printer
                           </button>`
                    }
                </div>

                <button onclick="document.getElementById('printer-settings-modal').remove();"
                    style="display:block;width:100%;margin-top:10px;padding:10px;border-radius:10px;border:1px solid #e5e7eb;background:#fff;color:#6b7280;font-weight:500;cursor:pointer;font-size:13px;">
                    Tutup
                </button>
            </div>
        </div>`;
        document.body.appendChild(modal);
    }

    // ── Public API ─────────────────────────────────────────────────────
    return {
        connect,
        disconnect,
        isConnected,
        setPaperWidth,
        printReceipt,
        testPrint,
        openSettings,
        updateStatusUI,
        showToast,
    };
})();

// Auto-init status badge on DOMContentLoaded
document.addEventListener('DOMContentLoaded', () => ThermalPrinter.updateStatusUI());
