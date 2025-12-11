<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>出席管理システム</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://unpkg.com/lucide@latest"></script>
    <link href="https://fonts.googleapis.com/css2?family=Noto+Sans+JP:wght@400;500;700&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Noto Sans JP', sans-serif; }
        .fade-in { animation: fadeIn 0.3s ease-in-out; }
        @keyframes fadeIn { from { opacity: 0; } to { opacity: 1; } }
    </style>
</head>
<body class="bg-gray-50 text-gray-900 min-h-screen">

    <div id="toast-container" class="fixed top-4 right-4 z-50 flex flex-col gap-2"></div>

    <header class="sticky top-0 z-10 bg-white/80 backdrop-blur-md shadow-sm border-b">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 h-16 flex items-center justify-between">
            <div class="flex items-center gap-2">
                <i data-lucide="graduation-cap" class="h-6 w-6 text-indigo-600"></i>
                <span class="font-bold text-lg bg-gradient-to-r from-indigo-600 to-purple-600 bg-clip-text text-transparent">
                    出席管理システム
                </span>
            </div>
            <div id="header-user-info" class="flex items-center gap-4 hidden">
                <span id="header-username" class="text-sm font-medium"></span>
                <button onclick="handleLogout()" class="text-muted-foreground flex items-center hover:text-gray-900">
                    <i data-lucide="log-out" class="h-4 w-4 mr-2"></i>
                    ログアウト
                </button>
            </div>
        </div>
    </header>

    <main id="app" class="max-w-5xl mx-auto p-4 sm:p-6 lg:p-8">
        </main>

    <script>
        // --- データ定義 ---
        const users = [
            { id: 'teacher', password: 'password', role: 'teacher', name: '教員 太郎' },
            { id: 'K023C0063', password: 'password', role: 'student', name: '源内 琉月', studentId: 'K023C0063' },
            { id: 'K023C0082', password: 'password', role: 'student', name: '清水屋 雄紀', studentId: 'K023C0082' },
            { id: 'K023C0020', password: 'password', role: 'student', name: '筌野 来海', studentId: 'K023C0020' },
            { id: 'K023C0022', password: 'password', role: 'student', name: '呉 荻' , studentId: 'K023C0022'},
            { id: 'K023C0048', password: 'password', role: 'student', name: '岡田 空' , studentId: 'K023C0048'},
        ];

        // --- 状態管理 (State) ---
        let state = {
            currentUser: null,
            currentView: 'login',
            attendanceForms: [],
            attendanceRecords: [],
            activeForm: null,
            editingRecord: null,
            
            // 入力値の一時保存
            loginId: '',
            loginPassword: '',
            
            // 作成フォーム用
            inputClassName: '情報システム基礎',
            inputClassDate: new Date().toISOString().split('T')[0],
            generatedCode: '',

            // 学生入力用
            inputStudentCode: '',
            
            // 編集用
            editForm: {},

            // 検索用
            searchDate: '',
            searchName: '',
            searchClassName: ''
        };

        // --- ユーティリティ ---
        const showToast = (message, type = 'success') => {
            const container = document.getElementById('toast-container');
            const toast = document.createElement('div');
            const colors = type === 'error' ? 'bg-red-500' : type === 'info' ? 'bg-blue-500' : 'bg-green-600';
            toast.className = `${colors} text-white px-4 py-3 rounded shadow-lg text-sm flex items-center gap-2 fade-in`;
            toast.innerHTML = `<span>${message}</span>`;
            
            container.appendChild(toast);
            setTimeout(() => {
                toast.remove();
            }, 3000);
        };

        const renderIcons = () => {
            if (window.lucide) {
                lucide.createIcons();
            }
        };

        // --- ロジック関数 ---

        const handleLogin = (e) => {
            e.preventDefault();
            const user = users.find(u => u.id === state.loginId && u.password === state.loginPassword);
            if (user) {
                state.currentUser = user;
                state.currentView = user.role === 'teacher' ? 'teacher-dashboard' : 'student-dashboard';
                state.loginId = '';
                state.loginPassword = '';
                showToast(`${user.name}としてログインしました`);
                render();
            } else {
                showToast('IDまたはパスワードが違います', 'error');
            }
        };

        const handleLogout = () => {
            state.currentUser = null;
            state.currentView = 'login';
            state.activeForm = null;
            showToast('ログアウトしました');
            render();
        };

        const generateCode = () => {
            const chars = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789';
            let code = '';
            for (let i = 0; i < 6; i++) code += chars.charAt(Math.floor(Math.random() * chars.length));
            state.generatedCode = code;
            render();
        };

        const handleCreateForm = (e) => {
            e.preventDefault();
            const newForm = {
                id: `form-${Date.now()}`,
                className: state.inputClassName,
                date: state.inputClassDate,
                code: state.generatedCode,
                attendanceList: [],
                createdAt: Date.now()
            };
            state.attendanceForms.push(newForm);
            state.activeForm = newForm;
            state.currentView = 'share-form';
            showToast('出席フォームが作成されました');
            render();
        };

        const handleStudentSubmit = (e) => {
            e.preventDefault();
            const form = state.attendanceForms.find(f => f.code === state.inputStudentCode);
            
            if (!form) {
                showToast('出席コードが正しくありません', 'error');
                return;
            }
            if (!state.currentUser.studentId) return;

            const alreadySubmitted = form.attendanceList.some(sub => sub.studentId === state.currentUser.studentId);
            if (alreadySubmitted) {
                showToast('すでに出席済みです', 'error');
                return;
            }

            // 締め切り判定
            const now = Date.now();
            const diff = now - form.createdAt;
            const LATE_THRESHOLD = 10 * 60 * 1000;
            const DEADLINE_THRESHOLD = 15 * 60 * 1000;

            if (diff > DEADLINE_THRESHOLD) {
                showToast('提出期限（15分）を過ぎているため送信できません', 'error');
                return;
            }

            const status = diff <= LATE_THRESHOLD ? '出席' : '遅刻';

            // データ登録
            const submission = {
                studentId: state.currentUser.studentId,
                studentName: state.currentUser.name,
                timestamp: now,
                status: status,
                comment: ''
            };
            form.attendanceList.push(submission);

            const record = {
                id: `rec-${now}`,
                studentId: state.currentUser.studentId,
                studentName: state.currentUser.name,
                className: form.className,
                date: form.date,
                status: status,
                comment: ''
            };
            state.attendanceRecords.push(record);
            
            state.inputStudentCode = '';
            state.currentView = 'student-complete';
            showToast(`出席が完了しました（ステータス: ${status}）`);
            render();
        };

        // 新規追加ボタン
        const handleAddRecord = () => {
            state.editingRecord = {
                id: `manual-${Date.now()}`,
                studentId: '',
                studentName: '',
                className: '',
                date: new Date().toISOString().split('T')[0],
                status: '出席',
                comment: ''
            };
            state.editForm = { ...state.editingRecord };
            state.currentView = 'edit-record';
            render();
        };

        // 編集開始
        const startEditRecord = (id) => {
            const record = state.attendanceRecords.find(r => r.id === id);
            if (record) {
                state.editingRecord = record;
                state.editForm = { ...record };
                state.currentView = 'edit-record';
                render();
            }
        };

        // 編集保存
        const saveEditRecord = (e) => {
            e.preventDefault();
            const { id } = state.editForm;
            const existingIndex = state.attendanceRecords.findIndex(r => r.id === id);

            if (existingIndex >= 0) {
                state.attendanceRecords[existingIndex] = { ...state.editForm };
                showToast('記録を更新しました');
            } else {
                state.attendanceRecords.push({ ...state.editForm });
                showToast('新規記録を追加しました');
            }
            state.currentView = 'attendance-list';
            render();
        };

        // 削除
        const handleDeleteRecord = (id) => {
            if (confirm('この記録を削除しますか？')) {
                const record = state.attendanceRecords.find(r => r.id === id);
                state.attendanceRecords = state.attendanceRecords.filter(r => r.id !== id);
                
                // フォーム側との整合性
                if (record) {
                    state.attendanceForms.forEach(form => {
                        if (form.className === record.className && form.date === record.date) {
                            form.attendanceList = form.attendanceList.filter(s => s.studentId !== record.studentId);
                        }
                    });
                }
                showToast('削除しました');
                render();
            }
        };

        // --- Render Functions (HTML生成) ---

        const renderLogin = () => `
            <div class="flex items-center justify-center min-h-[80vh]">
                <div class="w-full max-w-md bg-white rounded-xl shadow-xl p-8 fade-in">
                    <div class="text-center mb-8">
                        <div class="inline-block p-4 bg-gradient-to-br from-indigo-500 to-purple-600 rounded-2xl mb-4">
                            <i data-lucide="graduation-cap" class="h-12 w-12 text-white"></i>
                        </div>
                        <h1 class="text-2xl font-bold bg-gradient-to-r from-indigo-600 to-purple-600 bg-clip-text text-transparent">出席管理システム</h1>
                        <p class="text-gray-500 mt-2">ログインして始めましょう</p>
                    </div>
                    <form onsubmit="handleLogin(event)" class="space-y-4">
                        <div>
                            <label class="block text-sm font-medium mb-1">ユーザーID</label>
                            <input type="text" value="${state.loginId}" oninput="state.loginId=this.value" class="w-full p-2 border rounded-md" placeholder="ユーザーIDを入力" required>
                        </div>
                        <div>
                            <label class="block text-sm font-medium mb-1">パスワード</label>
                            <input type="password" value="${state.loginPassword}" oninput="state.loginPassword=this.value" class="w-full p-2 border rounded-md" placeholder="パスワードを入力" required>
                        </div>
                        <button type="submit" class="w-full py-2 bg-gradient-to-r from-indigo-600 to-purple-600 text-white rounded-md hover:opacity-90">ログイン</button>
                    </form>
                    <div class="mt-4 text-center text-xs text-gray-400">
                        デモ用: teacher / password または K023C0063 / password
                    </div>
                </div>
            </div>
        `;

        const renderTeacherDashboard = () => `
            <div class="fade-in">
                <div class="bg-white p-6 rounded-xl shadow-lg border border-gray-100 mb-6">
                    <h2 class="text-xl font-bold flex items-center gap-2 mb-4 text-indigo-700">
                        <i data-lucide="sparkles"></i> 教員ダッシュボード
                    </h2>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <button onclick="state.currentView='create-form'; generateCode(); render()" class="p-6 bg-blue-500 text-white rounded-xl shadow hover:bg-blue-600 transition flex flex-col items-center gap-2">
                            <i data-lucide="plus-circle" class="h-8 w-8"></i>
                            <span class="font-bold text-lg">出席フォーム作成</span>
                            <span class="text-sm opacity-90">新しい授業を開始</span>
                        </button>
                        <button onclick="checkAndGoLive()" class="p-6 bg-emerald-500 text-white rounded-xl shadow hover:bg-emerald-600 transition flex flex-col items-center gap-2">
                            <i data-lucide="list-check" class="h-8 w-8"></i>
                            <span class="font-bold text-lg">リアルタイム状況</span>
                            <span class="text-sm opacity-90">現在の出席を確認</span>
                        </button>
                        <button onclick="state.currentView='attendance-list'; render()" class="md:col-span-2 p-6 bg-purple-500 text-white rounded-xl shadow hover:bg-purple-600 transition flex flex-col items-center gap-2">
                            <i data-lucide="file-text" class="h-8 w-8"></i>
                            <span class="font-bold text-lg">出席リスト管理</span>
                            <span class="text-sm opacity-90">過去の記録を編集・検索</span>
                        </button>
                    </div>
                </div>
            </div>
        `;

        const renderCreateForm = () => `
            <div class="max-w-2xl mx-auto bg-white rounded-xl shadow-lg p-6 fade-in">
                <h2 class="text-xl font-bold mb-6 flex items-center gap-2 text-blue-600">
                    <i data-lucide="plus-circle"></i> 出席フォーム作成
                </h2>
                <form onsubmit="handleCreateForm(event)" class="space-y-6">
                    <div>
                        <label class="block text-sm font-medium mb-1">授業名</label>
                        <input type="text" value="${state.inputClassName}" oninput="state.inputClassName=this.value" class="w-full p-2 border rounded" required>
                    </div>
                    <div>
                        <label class="block text-sm font-medium mb-1">日付</label>
                        <input type="date" value="${state.inputClassDate}" oninput="state.inputClassDate=this.value" class="w-full p-2 border rounded" required>
                    </div>
                    <div>
                        <label class="block text-sm font-medium mb-1">出席コード</label>
                        <div class="flex gap-2">
                            <input type="text" value="${state.generatedCode}" readonly class="w-full p-2 border rounded bg-gray-50 font-mono tracking-widest">
                            <button type="button" onclick="generateCode()" class="px-4 py-2 border rounded hover:bg-gray-50"><i data-lucide="sparkles" class="h-4 w-4"></i></button>
                        </div>
                    </div>
                    <div class="flex justify-between pt-4 border-t">
                        <button type="button" onclick="state.currentView='teacher-dashboard'; render()" class="px-4 py-2 border rounded text-gray-600 hover:bg-gray-50">戻る</button>
                        <button type="submit" class="px-6 py-2 bg-blue-600 text-white rounded hover:bg-blue-700">作成する</button>
                    </div>
                </form>
            </div>
        `;

        const renderShareForm = () => `
            <div class="max-w-md mx-auto bg-white rounded-xl shadow-xl p-8 text-center fade-in">
                <div class="inline-block p-3 bg-green-100 rounded-full mb-4">
                    <i data-lucide="check-circle" class="h-8 w-8 text-green-600"></i>
                </div>
                <h2 class="text-xl font-bold text-green-700 mb-2">作成完了</h2>
                
                <div class="space-y-6 text-left">
                    <div>
                        <p class="text-sm font-bold text-gray-500 mb-2 flex items-center gap-2"><span class="w-5 h-5 rounded-full bg-indigo-600 text-white flex items-center justify-center text-xs">1</span> 出席コード</p>
                        <div class="bg-indigo-50 p-4 rounded-xl border-2 border-indigo-200 text-center">
                            <span class="text-4xl font-mono font-bold text-indigo-700 tracking-widest">${state.activeForm.code}</span>
                        </div>
                    </div>

                    <div>
                        <p class="text-sm font-bold text-gray-500 mb-2 flex items-center gap-2"><span class="w-5 h-5 rounded-full bg-indigo-600 text-white flex items-center justify-center text-xs">2</span> ログインURL</p>
                        <div class="flex gap-2">
                            <input type="text" value="${window.location.href}" readonly class="w-full p-2 border rounded bg-gray-50 text-xs text-gray-500 font-mono">
                            <button onclick="navigator.clipboard.writeText(window.location.href); showToast('URLをコピーしました')" class="px-3 border rounded hover:bg-gray-50 flex items-center justify-center" title="コピー">
                                <i data-lucide="copy" class="h-4 w-4"></i>
                            </button>
                        </div>
                        <p class="text-xs text-gray-400 mt-1">※ 学生はこのURLからアクセスしてコードを入力します</p>
                    </div>
                </div>

                <button onclick="state.currentView='teacher-dashboard'; render()" class="w-full mt-8 py-3 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700">ダッシュボードへ戻る</button>
            </div>
        `;

        const renderLiveStatus = () => {
            if (!state.activeForm) return '<div>データがありません</div>';
            
            // リストの生成
            const listHtml = state.activeForm.attendanceList.length === 0 
                ? `<tr><td colspan="5" class="py-12 text-center text-gray-400">待機中...</td></tr>`
                : state.activeForm.attendanceList.map((sub, i) => `
                    <tr class="border-b">
                        <td class="p-3 text-center">${i + 1}</td>
                        <td class="p-3 font-mono">${sub.studentId}</td>
                        <td class="p-3">${sub.studentName}</td>
                        <td class="p-3 text-sm text-gray-500">${new Date(sub.timestamp).toLocaleTimeString()}</td>
                        <td class="p-3"><span class="px-2 py-1 rounded text-xs ${sub.status === '出席' ? 'bg-green-100 text-green-800' : sub.status === '遅刻' ? 'bg-yellow-100 text-yellow-800' : 'bg-red-100 text-red-800'}">${sub.status}</span></td>
                    </tr>
                `).join('');

            return `
            <div class="bg-white rounded-xl shadow-lg overflow-hidden fade-in">
                <div class="p-6 bg-gradient-to-r from-emerald-50 to-teal-50 border-b flex justify-between items-center">
                    <div>
                        <h2 class="text-xl font-bold flex items-center gap-2 text-emerald-800">
                            <span class="relative flex h-3 w-3"><span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-red-400 opacity-75"></span><span class="relative inline-flex rounded-full h-3 w-3 bg-red-500"></span></span>
                            リアルタイム出席状況
                        </h2>
                        <p class="text-sm text-gray-500 mt-1">5秒ごとに更新中 | ${state.activeForm.className}</p>
                    </div>
                    <div class="flex items-center gap-4">
                        <div class="text-right">
                            <div class="text-2xl font-bold text-gray-800">${state.activeForm.attendanceList.length}名</div>
                            <div class="text-xs text-gray-500">出席済み</div>
                        </div>
                    </div>
                </div>
                
                <div class="overflow-x-auto">
                    <table class="w-full text-left">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="p-3 text-center w-16">No</th>
                                <th class="p-3">学籍番号</th>
                                <th class="p-3">氏名</th>
                                <th class="p-3">時刻</th>
                                <th class="p-3">状況</th>
                            </tr>
                        </thead>
                        <tbody>${listHtml}</tbody>
                    </table>
                </div>
                <div class="p-4 bg-gray-50 border-t flex justify-between">
                    <button onclick="state.currentView='teacher-dashboard'; render()" class="px-4 py-2 border bg-white rounded hover:bg-gray-50">戻る</button>
                    </div>
            </div>
            `;
        };

        const renderAttendanceList = () => {
            // フィルタリング
            const filtered = state.attendanceRecords.filter(r => {
                const matchDate = !state.searchDate || r.date.includes(state.searchDate);
                const matchName = !state.searchName || r.studentName.includes(state.searchName);
                const matchClass = !state.searchClassName || r.className.includes(state.searchClassName);
                return matchDate && matchName && matchClass;
            }).sort((a, b) => new Date(b.date) - new Date(a.date));

            const listHtml = filtered.length === 0 
                ? `<tr><td colspan="7" class="py-12 text-center text-gray-400">データがありません</td></tr>`
                : filtered.map(rec => `
                    <tr class="border-b hover:bg-gray-50">
                        <td class="p-3 text-sm">${rec.date}</td>
                        <td class="p-3">${rec.className}</td>
                        <td class="p-3 font-mono text-sm">${rec.studentId}</td>
                        <td class="p-3">${rec.studentName}</td>
                        <td class="p-3"><span class="px-2 py-1 rounded text-xs ${rec.status === '出席' ? 'bg-gray-100' : 'bg-red-100'}">${rec.status}</span></td>
                        <td class="p-3 text-sm text-gray-500">${rec.comment}</td>
                        <td class="p-3 flex gap-2">
                            <button onclick="startEditRecord('${rec.id}')" class="px-2 py-1 bg-blue-500 text-white text-xs rounded">編集</button>
                            <button onclick="handleDeleteRecord('${rec.id}')" class="px-2 py-1 bg-red-500 text-white text-xs rounded">削除</button>
                        </td>
                    </tr>
                `).join('');

            return `
            <div class="bg-white rounded-xl shadow-lg border border-gray-100 fade-in">
                <div class="p-6 border-b flex justify-between items-center bg-purple-50">
                    <h2 class="text-xl font-bold text-purple-700 flex items-center gap-2"><i data-lucide="file-text"></i> 出席リスト管理</h2>
                    <button onclick="handleAddRecord()" class="px-4 py-2 bg-purple-600 text-white rounded hover:bg-purple-700 text-sm flex items-center gap-2"><i data-lucide="plus-circle" class="h-4 w-4"></i> 新規追加</button>
                </div>
                <div class="p-4 grid grid-cols-1 md:grid-cols-3 gap-4 bg-gray-50 border-b">
                    <input type="date" class="p-2 border rounded" onchange="state.searchDate=this.value; render()" value="${state.searchDate}">
                    <input type="text" placeholder="授業名" class="p-2 border rounded" oninput="state.searchClassName=this.value; render()" value="${state.searchClassName}">
                    <input type="text" placeholder="名前" class="p-2 border rounded" oninput="state.searchName=this.value; render()" value="${state.searchName}">
                </div>
                <div class="overflow-x-auto">
                    <table class="w-full text-left">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="p-3">日付</th><th class="p-3">授業名</th><th class="p-3">ID</th><th class="p-3">名前</th><th class="p-3">状況</th><th class="p-3">コメント</th><th class="p-3">操作</th>
                            </tr>
                        </thead>
                        <tbody>${listHtml}</tbody>
                    </table>
                </div>
                <div class="p-4 border-t">
                    <button onclick="state.currentView='teacher-dashboard'; render()" class="px-4 py-2 border rounded hover:bg-gray-50">戻る</button>
                </div>
            </div>
            `;
        };

        const renderEditRecord = () => {
            const r = state.editForm;
            return `
            <div class="max-w-2xl mx-auto bg-white rounded-xl shadow-lg p-6 fade-in">
                <h2 class="text-xl font-bold mb-6 text-blue-600">編集 / 新規登録</h2>
                <form onsubmit="saveEditRecord(event)" class="space-y-4">
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm mb-1">学籍番号</label>
                            <input type="text" value="${r.studentId}" 
                                oninput="state.editForm.studentId=this.value; const u = users.find(u=>u.studentId===this.value); if(u) { document.getElementById('editName').value = u.name; state.editForm.studentName = u.name; }" 
                                class="w-full p-2 border rounded" required>
                            <p class="text-xs text-gray-400 mt-1">※番号入力で名前自動反映</p>
                        </div>
                        <div>
                            <label class="block text-sm mb-1">名前</label>
                            <input id="editName" type="text" value="${r.studentName}" oninput="state.editForm.studentName=this.value" class="w-full p-2 border rounded" required>
                        </div>
                    </div>
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm mb-1">授業名</label>
                            <input type="text" value="${r.className}" oninput="state.editForm.className=this.value" class="w-full p-2 border rounded" required>
                        </div>
                        <div>
                            <label class="block text-sm mb-1">日付</label>
                            <input type="date" value="${r.date}" oninput="state.editForm.date=this.value" class="w-full p-2 border rounded" required>
                        </div>
                    </div>
                    <div>
                        <label class="block text-sm mb-1">状況</label>
                        <select onchange="state.editForm.status=this.value" class="w-full p-2 border rounded">
                            <option value="出席" ${r.status==='出席'?'selected':''}>出席</option>
                            <option value="遅刻" ${r.status==='遅刻'?'selected':''}>遅刻</option>
                            <option value="欠席" ${r.status==='欠席'?'selected':''}>欠席</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm mb-1">コメント</label>
                        <textarea oninput="state.editForm.comment=this.value" class="w-full p-2 border rounded">${r.comment}</textarea>
                    </div>
                    <div class="flex justify-between pt-4 border-t">
                        <button type="button" onclick="state.currentView='attendance-list'; render()" class="px-4 py-2 border rounded">キャンセル</button>
                        <button type="submit" class="px-6 py-2 bg-blue-600 text-white rounded hover:bg-blue-700">保存</button>
                    </div>
                </form>
            </div>
            `;
        };

        const renderStudentDashboard = () => `
            <div class="max-w-md mx-auto bg-white rounded-xl shadow-lg overflow-hidden fade-in">
                <div class="p-6 bg-gradient-to-r from-emerald-50 to-teal-50 border-b">
                    <h2 class="text-xl font-bold text-emerald-800 flex items-center gap-2"><i data-lucide="graduation-cap"></i> 学生ダッシュボード</h2>
                    <div class="mt-4">
                        <div class="text-lg font-bold">${state.currentUser.name} さん</div>
                        <div class="text-sm text-gray-500">学籍番号: ${state.currentUser.studentId}</div>
                    </div>
                    <div class="mt-2 inline-block px-2 py-1 bg-amber-50 text-amber-700 text-xs rounded border border-amber-200">
                        <i data-lucide="alert-circle" class="inline w-3 h-3"></i> 提出期限: 作成から15分以内
                    </div>
                </div>
                <div class="p-6">
                    <form onsubmit="handleStudentSubmit(event)" class="space-y-6">
                        <div>
                            <label class="block font-bold mb-2">出席コード</label>
                            <input type="text" value="${state.inputStudentCode}" oninput="state.inputStudentCode=this.value.toUpperCase()" class="w-full p-4 text-center text-2xl font-mono border-2 border-gray-200 rounded-lg tracking-widest uppercase focus:border-emerald-500 outline-none" placeholder="ABC123" maxlength="6" required>
                        </div>
                        <button type="submit" class="w-full py-4 bg-emerald-600 text-white font-bold rounded-lg hover:bg-emerald-700 shadow-lg flex items-center justify-center gap-2">
                            <i data-lucide="check-circle"></i> 出席を送信
                        </button>
                    </form>
                    <button onclick="state.currentView='student-history'; render()" class="w-full mt-4 py-2 text-gray-500 hover:text-gray-700 text-sm">過去の履歴を見る</button>
                </div>
            </div>
        `;

        const renderStudentHistory = () => {
            const myRecords = state.attendanceRecords.filter(r => r.studentId === state.currentUser.studentId);
            const list = myRecords.length === 0 
                ? `<tr><td colspan="4" class="py-8 text-center text-gray-400">履歴がありません</td></tr>`
                : myRecords.map(r => `
                    <tr class="border-b">
                        <td class="p-3 text-sm">${r.date}</td>
                        <td class="p-3 text-sm">${r.className}</td>
                        <td class="p-3"><span class="px-2 py-1 rounded text-xs bg-gray-100">${r.status}</span></td>
                    </tr>
                `).join('');

            return `
            <div class="max-w-md mx-auto bg-white rounded-xl shadow-lg fade-in">
                <div class="p-4 border-b bg-gray-50 flex items-center gap-2">
                    <i data-lucide="history"></i> <span class="font-bold">出席履歴</span>
                </div>
                <div class="p-4">
                    <table class="w-full text-left">
                        <thead><tr class="text-sm text-gray-500"><th class="p-2">日付</th><th class="p-2">授業</th><th class="p-2">状況</th></tr></thead>
                        <tbody>${list}</tbody>
                    </table>
                    <button onclick="state.currentView='student-dashboard'; render()" class="w-full mt-6 py-2 border rounded hover:bg-gray-50">戻る</button>
                </div>
            </div>
            `;
        };

        const renderStudentComplete = () => `
            <div class="max-w-md mx-auto text-center py-12 fade-in">
                <div class="inline-block p-4 bg-green-100 rounded-full mb-6">
                    <i data-lucide="check-circle" class="h-12 w-12 text-green-600"></i>
                </div>
                <h2 class="text-2xl font-bold text-gray-800 mb-2">送信完了</h2>
                <p class="text-gray-500 mb-8">出席データが記録されました</p>
                <button onclick="state.currentView='student-dashboard'; render()" class="px-8 py-3 bg-gray-800 text-white rounded-lg hover:bg-gray-700">トップに戻る</button>
            </div>
        `;

        // --- メイン描画関数 ---
        const render = () => {
            const app = document.getElementById('app');
            const headerInfo = document.getElementById('header-user-info');
            const headerName = document.getElementById('header-username');

            // ヘッダー更新
            if (state.currentUser) {
                headerInfo.classList.remove('hidden');
                headerName.textContent = state.currentUser.name;
            } else {
                headerInfo.classList.add('hidden');
            }

            // ビュー切り替え
            switch (state.currentView) {
                case 'login': app.innerHTML = renderLogin(); break;
                case 'teacher-dashboard': app.innerHTML = renderTeacherDashboard(); break;
                case 'create-form': app.innerHTML = renderCreateForm(); break;
                case 'share-form': app.innerHTML = renderShareForm(); break;
                case 'live-status': app.innerHTML = renderLiveStatus(); break;
                case 'attendance-list': app.innerHTML = renderAttendanceList(); break;
                case 'edit-record': app.innerHTML = renderEditRecord(); break;
                case 'student-dashboard': app.innerHTML = renderStudentDashboard(); break;
                case 'student-history': app.innerHTML = renderStudentHistory(); break;
                case 'student-complete': app.innerHTML = renderStudentComplete(); break;
                default: app.innerHTML = renderLogin();
            }
            
            // アイコン再描画
            renderIcons();
        };

        // --- 初期化 ---
        const checkAndGoLive = () => {
            let active = state.activeForm;
            if (!active && state.attendanceForms.length > 0) {
                active = state.attendanceForms[state.attendanceForms.length - 1];
                state.activeForm = active;
                showToast(`最新のフォームを表示します`, 'info');
            }
            
            if (active) {
                state.currentView = 'live-status';
                render();
            } else {
                showToast('フォームがありません', 'error');
            }
        };

        // 5秒ごとの更新 (ReactのuseEffect相当)
        setInterval(() => {
            if (state.currentView === 'live-status' && state.activeForm) {
                // 本来はサーバーfetchだが、ここでは再描画のみ
                render(); 
            }
        }, 5000);

        // 初回描画
        render();

    </script>
</body>
</html>