@php
    $map = [];
    foreach (\App\Filament\Support\SiteFields::blocks() as $block) {
        $map[(string) $block->getLabel()] = $block->getName();
    }
@endphp
<style>
    #hc-block-hover {
        position: fixed; z-index: 99999; display: none; background: #fff;
        border: 1px solid rgba(0, 0, 0, .15); border-radius: 12px; overflow: hidden;
        box-shadow: 0 20px 50px rgba(0, 0, 0, .3); pointer-events: none;
    }
    #hc-block-hover .hc-bh-title { height: 34px; box-sizing: border-box; padding: 8px 14px; font: 700 13px system-ui, sans-serif; background: #1D74C9; color: #fff; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; }
    #hc-block-hover .hc-bh-view { overflow: hidden; position: relative; background: #f3f4f6; }
    #hc-block-hover iframe { position: absolute; left: 0; top: 0; width: 1240px; border: 0; transform-origin: 0 0; background: #fff; }
</style>
<div id="hc-block-hover">
    <div class="hc-bh-title"></div>
    <div class="hc-bh-view"><iframe tabindex="-1"></iframe></div>
</div>
<script>
(function () {
    var map = @json($map);
    var base = @json(url('/admin/block-preview'));
    var box = document.getElementById('hc-block-hover');
    var view = box.querySelector('.hc-bh-view');
    var frame = box.querySelector('iframe');
    var title = box.querySelector('.hc-bh-title');
    var current = null;
    var GAP = 14, MAXW = 560, MINW = 240, TITLE = 34, RATIO = 0.68; // view height = width * RATIO

    function hide() { box.style.display = 'none'; current = null; }

    // Size + place the preview so it NEVER covers the section list.
    function place(btn) {
        var panel = btn.closest('.fi-dropdown-panel') || btn;
        var r = panel.getBoundingClientRect();
        var b = btn.getBoundingClientRect();
        var vw = window.innerWidth, vh = window.innerHeight, M = 10;

        var opts = [];
        // right / left of the list
        [['right', vw - r.right - GAP - M], ['left', r.left - GAP - M]].forEach(function (s) {
            var w = Math.min(MAXW, s[1]);
            if (w >= MINW) { opts.push({ side: s[0], w: w, h: Math.min(w * RATIO, vh - TITLE - 2 * M) }); }
        });
        // above / below the list
        [['above', r.top - GAP - M], ['below', vh - r.bottom - GAP - M]].forEach(function (s) {
            var h = Math.min(MAXW * RATIO, s[1] - TITLE);
            if (h >= MINW * RATIO) { opts.push({ side: s[0], w: Math.min(MAXW, h / RATIO), h: h }); }
        });
        if (!opts.length) { hide(); return false; }
        opts.sort(function (a, c) { return c.w * c.h - a.w * a.h; });
        var o = opts[0], left, top, total = o.h + TITLE + 2;

        if (o.side === 'right') { left = r.right + GAP; }
        else if (o.side === 'left') { left = r.left - GAP - o.w; }
        else { left = Math.max(M, Math.min(b.left + b.width / 2 - o.w / 2, vw - o.w - M)); }

        if (o.side === 'above') { top = r.top - GAP - total; }
        else if (o.side === 'below') { top = r.bottom + GAP; }
        else { top = Math.max(M, Math.min(b.top + b.height / 2 - total / 2, vh - total - M)); }

        var scale = o.w / 1240;
        box.style.width = o.w + 'px';
        box.style.left = left + 'px';
        box.style.top = top + 'px';
        view.style.height = o.h + 'px';
        frame.style.transform = 'scale(' + scale + ')';
        frame.style.height = (o.h / scale) + 'px';
        return true;
    }

    document.addEventListener('mouseover', function (e) {
        var btn = e.target.closest ? e.target.closest('.fi-fo-builder-block-picker .fi-dropdown-list-item') : null;
        if (!btn) { if (current && !(e.target.closest && e.target.closest('.fi-dropdown-panel'))) { hide(); } return; }
        var label = btn.textContent.replace(/\s+/g, ' ').trim();
        var type = map[label];
        if (!type) { hide(); return; }
        if (!place(btn)) { return; }
        box.style.display = 'block';
        if (current !== type) {
            current = type;
            title.textContent = label + ' — how it looks (sample)';
            frame.src = base + '/' + type;
        }
    });
    document.addEventListener('click', hide, true);
    document.addEventListener('keydown', function (e) { if (e.key === 'Escape') { hide(); } });
})();
</script>
