<?php
// footer.php - scripts & closing tags
?>
<script>
    // トースト表示: サーバからのメッセージ（URLパラメータ）を表示
    (function(){
        const params = new URLSearchParams(window.location.search);
        const msg = params.get('msg');
        const type = params.get('msg_type') || 'success';
        if (msg) {
            const container = document.getElementById('toast-container');
            const toast = document.createElement('div');
            const colors = type === 'error' ? 'bg-red-500' : type === 'info' ? 'bg-blue-500' : 'bg-green-600';
            toast.className = `${colors} text-white px-4 py-3 rounded shadow-lg text-sm flex items-center gap-2 fade-in`;
            toast.innerText = msg;
            container.appendChild(toast);
            setTimeout(()=>toast.remove(), 4000);
            // パラメータをURLから取り除く（履歴の書き換え）
            if (history.replaceState) {
                const cleanUrl = location.pathname + location.search.replace(/([?&])msg=[^&]*/,'').replace(/([?&])msg_type=[^&]*/,'');
                history.replaceState({}, '', cleanUrl);
            }
        }
        if (window.lucide) lucide.createIcons();
    })();
</script>
</body>
</html>