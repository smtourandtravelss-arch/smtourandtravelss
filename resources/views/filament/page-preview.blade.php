<div
    x-data="{
        mode: 'desktop',
        scale: 1,
        fit() {
            const w = this.$refs.box.clientWidth - 24;
            this.scale = this.mode === 'desktop' ? Math.max(0.2, Math.min(1, w / 1280)) : 1;
        },
    }"
    x-init="$nextTick(() => fit()); new ResizeObserver(() => fit()).observe($refs.box)"
    x-effect="mode; $nextTick(() => fit())"
    style="display:flex;flex-direction:column;gap:12px"
>
    <style>
        .hcp-btn{border:0;cursor:pointer;border-radius:8px;padding:8px 16px;font-size:14px;font-weight:700;background:#e5e7eb;color:#374151}
        .hcp-btn.on{background:#1D74C9;color:#fff}
    </style>
    <div style="display:flex;flex-wrap:wrap;align-items:center;gap:8px">
        <button type="button" class="hcp-btn" x-bind:class="{ on: mode === 'desktop' }" x-on:click="mode = 'desktop'">🖥️ Desktop</button>
        <button type="button" class="hcp-btn" x-bind:class="{ on: mode === 'mobile' }" x-on:click="mode = 'mobile'">📱 Mobile</button>
        <button type="button" class="hcp-btn" wire:click="$refresh">🔄 Refresh preview</button>
        <span style="font-size:12px;color:#6b7280">Shows your unsaved changes. Newly uploaded photos appear after you save.</span>
    </div>

    <div x-ref="box" style="display:flex;justify-content:center;background:#d1d5db;border-radius:12px;padding:12px;min-height:75vh">
        <div x-bind:style="mode === 'desktop'
                ? 'width:' + (1280 * scale) + 'px;height:75vh;overflow:hidden;background:#fff;border-radius:8px'
                : 'width:410px;height:75vh;overflow:hidden;background:#fff;border:10px solid #111;border-radius:28px'">
            <iframe
                wire:key="preview-{{ md5($html) }}"
                srcdoc="{{ $html }}"
                sandbox="allow-scripts allow-same-origin allow-popups"
                x-bind:style="mode === 'desktop'
                    ? 'display:block;width:1280px;height:calc(75vh / ' + scale + ');border:0;transform:scale(' + scale + ');transform-origin:0 0'
                    : 'display:block;width:100%;height:100%;border:0'"
            ></iframe>
        </div>
    </div>
</div>
