<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>API Testing Dashboard — ReactBackend</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <style>
        pre code { font-family: ui-monospace, SFMono-Regular, Menlo, Monaco, Consolas, monospace; }
        .tab-btn.active {
            border-color: #3b82f6;
            color: #2563eb;
            background-color: #eff6ff;
        }
        .method-badge {
            font-size: 0.7rem;
            font-weight: 700;
            padding: 2px 8px;
            border-radius: 4px;
            letter-spacing: 0.05em;
        }
        .badge-post { background-color: #dcfce7; color: #15803d; border: 1px solid #86efac; }
        .badge-get  { background-color: #dbeafe; color: #1d4ed8; border: 1px solid #93c5fd; }
        .badge-put  { background-color: #fef3c7; color: #b45309; border: 1px solid #fcd34d; }
        .badge-del  { background-color: #fee2e2; color: #b91c1c; border: 1px solid #fca5a5; }
    </style>
</head>
<body class="bg-slate-50 text-slate-800 min-h-screen">

    <!-- Top Navigation Header -->
    <header class="bg-white border-b border-slate-200 sticky top-0 z-50">
        <div class="max-w-7xl mx-auto px-4 py-3 flex items-center justify-between">
            <div class="flex items-center space-x-3">
                <div class="w-10 h-10 rounded-lg bg-blue-600 text-white flex items-center justify-center font-bold text-lg shadow-sm">
                    <i class="fa-solid fa-bolt"></i>
                </div>
                <div>
                    <h1 class="text-lg font-bold text-slate-900 leading-tight">API Testing Dashboard</h1>
                    <p class="text-xs text-slate-500">Modules: Auth, Layouts, Navbar, Home, Services, Portfolio, SEO, Media, Settings</p>
                </div>
            </div>

            <div class="flex items-center space-x-3 text-xs">
                <div class="hidden sm:flex items-center space-x-2 bg-slate-100 px-3 py-1.5 rounded-lg border border-slate-200">
                    <span class="text-slate-500">Base URL:</span>
                    <input id="baseUrlInput" type="text" value="" class="bg-transparent font-mono text-slate-700 outline-none w-48 font-medium">
                </div>
                <div id="tokenBadge" class="flex items-center space-x-1.5 px-3 py-1.5 rounded-full bg-amber-50 text-amber-700 border border-amber-200 font-medium">
                    <span class="w-2 h-2 rounded-full bg-amber-500"></span>
                    <span id="tokenBadgeText">No Token</span>
                </div>
                <button onclick="clearStoredToken()" title="Clear Token" class="text-slate-400 hover:text-red-600 px-2 py-1 text-sm">
                    <i class="fa-solid fa-arrow-right-from-bracket"></i>
                </button>
            </div>
        </div>
    </header>

    <div class="max-w-7xl mx-auto px-4 py-6 grid grid-cols-1 lg:grid-cols-12 gap-6">

        <!-- Left Sidebar / Category Navigation -->
        <div class="lg:col-span-3">
            <div class="bg-white rounded-xl border border-slate-200 p-3 shadow-sm sticky top-20">
                <h3 class="text-xs font-semibold text-slate-400 uppercase tracking-wider px-3 mb-2">API Modules</h3>
                <nav class="space-y-1">
                    <button onclick="switchTab('auth')" id="tab-auth" class="tab-btn active w-full flex items-center justify-between px-3 py-2 text-sm font-medium rounded-lg text-left transition">
                        <span class="flex items-center space-x-2.5">
                            <i class="fa-solid fa-shield-halved text-blue-500"></i>
                            <span>Admin Auth</span>
                        </span>
                        <span class="text-xs bg-blue-100 text-blue-700 px-2 py-0.5 rounded-full font-bold">5</span>
                    </button>

                    <button onclick="switchTab('modules')" id="tab-modules" class="tab-btn w-full flex items-center justify-between px-3 py-2 text-sm font-medium rounded-lg text-left text-slate-600 hover:bg-slate-50 transition">
                        <span class="flex items-center space-x-2.5">
                            <i class="fa-solid fa-cubes text-purple-600"></i>
                            <span class="font-bold text-purple-900">Admin Modules (GET)</span>
                        </span>
                        <span class="text-xs bg-purple-100 text-purple-700 px-2 py-0.5 rounded-full font-bold">11</span>
                    </button>

                    <button onclick="switchTab('layouts')" id="tab-layouts" class="tab-btn w-full flex items-center justify-between px-3 py-2 text-sm font-medium rounded-lg text-left text-slate-600 hover:bg-slate-50 transition">
                        <span class="flex items-center space-x-2.5">
                            <i class="fa-solid fa-table-columns text-emerald-500"></i>
                            <span>Layouts (Mod 1-5)</span>
                        </span>
                        <span class="text-xs bg-emerald-100 text-emerald-700 px-2 py-0.5 rounded-full font-bold">4</span>
                    </button>

                    <button onclick="switchTab('navbar')" id="tab-navbar" class="tab-btn w-full flex items-center justify-between px-3 py-2 text-sm font-medium rounded-lg text-left text-slate-600 hover:bg-slate-50 transition">
                        <span class="flex items-center space-x-2.5">
                            <i class="fa-solid fa-bars text-indigo-500"></i>
                            <span>Module 2: Navbar</span>
                        </span>
                        <span class="text-xs bg-indigo-100 text-indigo-700 px-2 py-0.5 rounded-full font-bold">6</span>
                    </button>

                    <button onclick="switchTab('home')" id="tab-home" class="tab-btn w-full flex items-center justify-between px-3 py-2 text-sm font-medium rounded-lg text-left text-slate-600 hover:bg-slate-50 transition">
                        <span class="flex items-center space-x-2.5">
                            <i class="fa-solid fa-house text-orange-500"></i>
                            <span>Module 3: Home</span>
                        </span>
                        <span class="text-xs bg-orange-100 text-orange-700 px-2 py-0.5 rounded-full font-bold">4</span>
                    </button>

                    <button onclick="switchTab('services')" id="tab-services" class="tab-btn w-full flex items-center justify-between px-3 py-2 text-sm font-medium rounded-lg text-left text-slate-600 hover:bg-slate-50 transition">
                        <span class="flex items-center space-x-2.5">
                            <i class="fa-solid fa-briefcase text-teal-500"></i>
                            <span>Module 4: Services</span>
                        </span>
                        <span class="text-xs bg-teal-100 text-teal-700 px-2 py-0.5 rounded-full font-bold">7</span>
                    </button>

                    <button onclick="switchTab('portfolio')" id="tab-portfolio" class="tab-btn w-full flex items-center justify-between px-3 py-2 text-sm font-medium rounded-lg text-left text-slate-600 hover:bg-slate-50 transition">
                        <span class="flex items-center space-x-2.5">
                            <i class="fa-solid fa-folder-open text-cyan-500"></i>
                            <span>Module 5: Portfolio</span>
                        </span>
                        <span class="text-xs bg-cyan-100 text-cyan-700 px-2 py-0.5 rounded-full font-bold">7</span>
                    </button>

                    <button onclick="switchTab('blog')" id="tab-blog" class="tab-btn w-full flex items-center justify-between px-3 py-2 text-sm font-medium rounded-lg text-left text-slate-600 hover:bg-slate-50 transition">
                        <span class="flex items-center space-x-2.5">
                            <i class="fa-solid fa-newspaper text-rose-500"></i>
                            <span>Module 6: Blog</span>
                        </span>
                        <span class="text-xs bg-rose-100 text-rose-700 px-2 py-0.5 rounded-full font-bold">7</span>
                    </button>

                    <button onclick="switchTab('seo')" id="tab-seo" class="tab-btn w-full flex items-center justify-between px-3 py-2 text-sm font-medium rounded-lg text-left text-slate-600 hover:bg-slate-50 transition">
                        <span class="flex items-center space-x-2.5">
                            <i class="fa-solid fa-magnifying-glass-chart text-purple-500"></i>
                            <span>3.2 SEO & ALT</span>
                        </span>
                        <span class="text-xs bg-purple-100 text-purple-700 px-2 py-0.5 rounded-full font-bold">3</span>
                    </button>

                    <button onclick="switchTab('media')" id="tab-media" class="tab-btn w-full flex items-center justify-between px-3 py-2 text-sm font-medium rounded-lg text-left text-slate-600 hover:bg-slate-50 transition">
                        <span class="flex items-center space-x-2.5">
                            <i class="fa-solid fa-cloud-arrow-up text-pink-500"></i>
                            <span>3.3 Media Upload</span>
                        </span>
                        <span class="text-xs bg-pink-100 text-pink-700 px-2 py-0.5 rounded-full font-bold">2</span>
                    </button>

                    <button onclick="switchTab('settings')" id="tab-settings" class="tab-btn w-full flex items-center justify-between px-3 py-2 text-sm font-medium rounded-lg text-left text-slate-600 hover:bg-slate-50 transition">
                        <span class="flex items-center space-x-2.5">
                            <i class="fa-solid fa-sliders text-amber-500"></i>
                            <span>3.4 Site Settings</span>
                        </span>
                        <span class="text-xs bg-amber-100 text-amber-700 px-2 py-0.5 rounded-full font-bold">2</span>
                    </button>
                </nav>

                <div class="mt-6 pt-4 border-t border-slate-100">
                    <div class="flex items-center justify-between mb-1.5">
                        <label class="text-xs font-semibold text-slate-600">Bearer Token</label>
                        <button onclick="copyToken()" class="text-xs text-blue-600 hover:underline">Copy</button>
                    </div>
                    <textarea id="tokenDisplay" rows="3" readonly class="w-full text-[10px] font-mono bg-slate-50 border border-slate-200 rounded p-2 text-slate-600 resize-none outline-none" placeholder="Login to receive token automatically..."></textarea>
                </div>

                <div class="mt-3 p-3 bg-blue-50 rounded-lg border border-blue-100 text-xs text-blue-900">
                    <p class="font-semibold mb-1"><i class="fa-regular fa-lightbulb"></i> Test Admin Credentials</p>
                    <p class="text-[11px] font-mono">Email: admin@admin.com</p>
                    <p class="text-[11px] font-mono">Pass: Admin@1234</p>
                </div>
            </div>
        </div>

        <!-- Middle Column: API Forms -->
        <div class="lg:col-span-5 space-y-4">

            <!-- ══════════════════════════════════════════════════════════ -->
            <!-- 1. AUTH MODULE APIS -->
            <!-- ══════════════════════════════════════════════════════════ -->
            <div id="section-auth" class="space-y-4">
                <div class="bg-white rounded-xl border border-slate-200 shadow-sm p-4">
                    <div class="flex items-center justify-between mb-3">
                        <div class="flex items-center space-x-2">
                            <span class="method-badge badge-post">POST</span>
                            <span class="font-mono text-sm font-semibold text-slate-800">/api/admin/login</span>
                        </div>
                        <span class="text-xs text-slate-400">Public</span>
                    </div>
                    <p class="text-xs text-slate-600 mb-3">Log in as admin & receive a Passport Bearer token.</p>
                    <div class="space-y-2 mb-3">
                        <div>
                            <label class="text-xs font-medium text-slate-600">Email</label>
                            <input id="login-email" type="email" value="admin@admin.com" class="w-full text-xs border border-slate-200 rounded-lg p-2 mt-1 focus:ring-2 focus:ring-blue-500 outline-none">
                        </div>
                        <div>
                            <label class="text-xs font-medium text-slate-600">Password</label>
                            <input id="login-password" type="password" value="Admin@1234" class="w-full text-xs border border-slate-200 rounded-lg p-2 mt-1 focus:ring-2 focus:ring-blue-500 outline-none">
                        </div>
                        <div>
                            <label class="text-xs font-medium text-slate-600">Device Name (Multi-device support)</label>
                            <input id="login-device" type="text" value="Workstation Chrome" placeholder="e.g. iPhone, MacBook, Workstation" class="w-full text-xs border border-slate-200 rounded-lg p-2 mt-1 focus:ring-2 focus:ring-blue-500 outline-none">
                        </div>
                    </div>
                    <button onclick="testLogin()" class="w-full bg-blue-600 hover:bg-blue-700 text-white font-medium text-xs py-2.5 rounded-lg transition shadow-sm flex items-center justify-center space-x-2">
                        <i class="fa-solid fa-right-to-bracket"></i>
                        <span>Send Login Request & Auto-Save Token</span>
                    </button>
                </div>

                <div class="bg-white rounded-xl border border-slate-200 shadow-sm p-4">
                    <div class="flex items-center justify-between mb-3">
                        <div class="flex items-center space-x-2">
                            <span class="method-badge badge-get">GET</span>
                            <span class="font-mono text-sm font-semibold text-slate-800">/api/admin/me</span>
                        </div>
                        <span class="text-xs bg-slate-100 text-slate-600 px-2 py-0.5 rounded font-medium">Auth Required</span>
                    </div>
                    <button onclick="testMe()" class="w-full bg-slate-800 hover:bg-slate-900 text-white font-medium text-xs py-2 rounded-lg transition">
                        <i class="fa-solid fa-user-check"></i> Send GET /me
                    </button>
                </div>

                <div class="bg-white rounded-xl border border-slate-200 shadow-sm p-4">
                    <div class="flex items-center justify-between mb-3">
                        <div class="flex items-center space-x-2">
                            <span class="method-badge badge-put">PUT</span>
                            <span class="font-mono text-sm font-semibold text-slate-800">/api/admin/profile</span>
                        </div>
                        <span class="text-xs bg-slate-100 text-slate-600 px-2 py-0.5 rounded font-medium">Auth Required</span>
                    </div>
                    <p class="text-xs text-slate-600 mb-3">Update admin profile (name, phone, bio, and profile image file or URL).</p>
                    <div class="space-y-2 mb-3">
                        <div class="grid grid-cols-2 gap-2">
                            <div>
                                <label class="text-xs font-medium text-slate-600">Name</label>
                                <input id="profile-name" type="text" value="Super Admin" class="w-full text-xs border border-slate-200 rounded p-1.5 mt-0.5 outline-none">
                            </div>
                            <div>
                                <label class="text-xs font-medium text-slate-600">Phone</label>
                                <input id="profile-phone" type="text" value="+1 (555) 019-2834" class="w-full text-xs border border-slate-200 rounded p-1.5 mt-0.5 outline-none">
                            </div>
                        </div>
                        <div>
                            <label class="text-xs font-medium text-slate-600">Bio</label>
                            <input id="profile-bio" type="text" value="Chief Digital Officer & Lead Architect" class="w-full text-xs border border-slate-200 rounded p-1.5 mt-0.5 outline-none">
                        </div>
                        <div>
                            <label class="text-xs font-medium text-slate-600">Profile Image (Upload File)</label>
                            <input id="profile-image-file" type="file" accept="image/*" class="w-full text-xs border border-slate-200 rounded p-1.5 mt-0.5 outline-none bg-slate-50">
                        </div>
                        <div>
                            <label class="text-xs font-medium text-slate-600">Or Profile Image URL</label>
                            <input id="profile-image-url" type="text" placeholder="https://images.unsplash.com/photo-..." class="w-full text-xs border border-slate-200 rounded p-1.5 mt-0.5 outline-none">
                        </div>
                    </div>
                    <button onclick="testUpdateProfile()" class="w-full bg-indigo-600 hover:bg-indigo-700 text-white font-medium text-xs py-2 rounded-lg transition shadow-sm flex items-center justify-center space-x-2">
                        <i class="fa-solid fa-user-pen"></i>
                        <span>Update Admin Profile</span>
                    </button>
                </div>

                <div class="bg-white rounded-xl border border-slate-200 shadow-sm p-4">
                    <div class="flex items-center justify-between mb-3">
                        <div class="flex items-center space-x-2">
                            <span class="method-badge badge-put">PUT</span>
                            <span class="font-mono text-sm font-semibold text-slate-800">/api/admin/change-password</span>
                        </div>
                        <span class="text-xs bg-slate-100 text-slate-600 px-2 py-0.5 rounded font-medium">Auth Required</span>
                    </div>
                    <p class="text-xs text-slate-600 mb-3">Change admin password (requires current password verification).</p>
                    <div class="space-y-2 mb-3">
                        <div>
                            <label class="text-xs font-medium text-slate-600">Current Password</label>
                            <input id="pwd-current" type="password" value="Admin@1234" class="w-full text-xs border border-slate-200 rounded p-1.5 mt-0.5 outline-none">
                        </div>
                        <div class="grid grid-cols-2 gap-2">
                            <div>
                                <label class="text-xs font-medium text-slate-600">New Password</label>
                                <input id="pwd-new" type="password" value="Admin@1234" class="w-full text-xs border border-slate-200 rounded p-1.5 mt-0.5 outline-none">
                            </div>
                            <div>
                                <label class="text-xs font-medium text-slate-600">Confirm Password</label>
                                <input id="pwd-confirm" type="password" value="Admin@1234" class="w-full text-xs border border-slate-200 rounded p-1.5 mt-0.5 outline-none">
                            </div>
                        </div>
                    </div>
                    <button onclick="testChangePassword()" class="w-full bg-amber-600 hover:bg-amber-700 text-white font-medium text-xs py-2 rounded-lg transition shadow-sm flex items-center justify-center space-x-2">
                        <i class="fa-solid fa-key"></i>
                        <span>Change Password</span>
                    </button>
                </div>

                <div class="bg-white rounded-xl border border-slate-200 shadow-sm p-4">
                    <div class="flex items-center justify-between mb-3">
                        <div class="flex items-center space-x-2">
                            <span class="method-badge badge-post">POST</span>
                            <span class="font-mono text-sm font-semibold text-slate-800">/api/admin/logout</span>
                        </div>
                        <span class="text-xs bg-slate-100 text-slate-600 px-2 py-0.5 rounded font-medium">Auth Required</span>
                    </div>
                    <button onclick="testLogout()" class="w-full bg-red-600 hover:bg-red-700 text-white font-medium text-xs py-2 rounded-lg transition">
                        <i class="fa-solid fa-right-from-bracket"></i> Send POST /logout
                    </button>
                </div>
            </div>

            <!-- ══════════════════════════════════════════════════════════ -->
            <!-- ADMIN MODULES (GET) - DIRECT /api/admin/... ENDPOINTS -->
            <!-- ══════════════════════════════════════════════════════════ -->
            <div id="section-modules" class="space-y-4 hidden">
                <div class="bg-purple-50 border border-purple-200 rounded-xl p-4 text-xs text-purple-900 leading-relaxed">
                    <p class="font-bold mb-1"><i class="fa-solid fa-circle-info mr-1 text-purple-600"></i> Direct Admin Module GET APIs</p>
                    <p>Direct module endpoints under <code class="bg-purple-100 px-1 py-0.5 rounded font-mono font-bold">/api/admin/&lt;module&gt;</code> without any <code class="line-through text-red-500 font-mono">/modules/</code> prefix. All endpoints require an Admin Bearer token.</p>
                </div>

                <!-- 1. Top Bar -->
                <div class="bg-white rounded-xl border border-slate-200 shadow-sm p-4">
                    <div class="flex items-center justify-between mb-2">
                        <div class="flex items-center space-x-2">
                            <span class="method-badge badge-get">GET</span>
                            <span class="font-mono text-sm font-semibold text-slate-800">/api/admin/topbar</span>
                        </div>
                        <span class="text-xs bg-purple-100 text-purple-700 px-2 py-0.5 rounded font-bold">Admin Auth</span>
                    </div>
                    <p class="text-xs text-slate-600 mb-3">Module 1: Returns Top Bar layout configuration and site contact/socials content.</p>
                    <button onclick="sendApi('GET', '/api/admin/topbar', null, true)" class="w-full bg-slate-900 hover:bg-slate-800 text-white font-medium text-xs py-2 rounded-lg transition shadow-sm">
                        <i class="fa-solid fa-arrow-down mr-1"></i> Fetch /api/admin/topbar
                    </button>
                </div>

                <!-- 2. Navbar -->
                <div class="bg-white rounded-xl border border-slate-200 shadow-sm p-4">
                    <div class="flex items-center justify-between mb-2">
                        <div class="flex items-center space-x-2">
                            <span class="method-badge badge-get">GET</span>
                            <span class="font-mono text-sm font-semibold text-slate-800">/api/admin/navbar</span>
                        </div>
                        <span class="text-xs bg-purple-100 text-purple-700 px-2 py-0.5 rounded font-bold">Admin Auth</span>
                    </div>
                    <p class="text-xs text-slate-600 mb-3">Module 2: Returns Navbar layout, scroll behavior, CTA button, and full menu items array.</p>
                    <div class="grid grid-cols-2 gap-2">
                        <button onclick="sendApi('GET', '/api/admin/navbar', null, true)" class="bg-indigo-600 hover:bg-indigo-700 text-white font-medium text-xs py-2 rounded-lg transition shadow-sm">
                            <i class="fa-solid fa-bars mr-1"></i> /api/admin/navbar
                        </button>
                        <button onclick="sendApi('GET', '/api/admin/navigation/navbar', null, true)" class="bg-slate-700 hover:bg-slate-800 text-white font-medium text-xs py-2 rounded-lg transition shadow-sm">
                            <i class="fa-solid fa-bars mr-1"></i> /api/admin/navigation/navbar
                        </button>
                    </div>
                </div>

                <!-- 3. Home Sections -->
                <div class="bg-white rounded-xl border border-slate-200 shadow-sm p-4">
                    <div class="flex items-center justify-between mb-2">
                        <div class="flex items-center space-x-2">
                            <span class="method-badge badge-get">GET</span>
                            <span class="font-mono text-sm font-semibold text-slate-800">/api/admin/home</span>
                        </div>
                        <span class="text-xs bg-purple-100 text-purple-700 px-2 py-0.5 rounded font-bold">Admin Auth</span>
                    </div>
                    <p class="text-xs text-slate-600 mb-3">Module 3: Returns Home Page layout and section visibility map.</p>
                    <button onclick="sendApi('GET', '/api/admin/home', null, true)" class="w-full bg-orange-600 hover:bg-orange-700 text-white font-medium text-xs py-2 rounded-lg transition shadow-sm">
                        <i class="fa-solid fa-house mr-1"></i> Fetch /api/admin/home
                    </button>
                </div>

                <!-- 4. Services Catalog -->
                <div class="bg-white rounded-xl border border-slate-200 shadow-sm p-4">
                    <div class="flex items-center justify-between mb-2">
                        <div class="flex items-center space-x-2">
                            <span class="method-badge badge-get">GET</span>
                            <span class="font-mono text-sm font-semibold text-slate-800">/api/admin/services</span>
                        </div>
                        <span class="text-xs bg-purple-100 text-purple-700 px-2 py-0.5 rounded font-bold">Admin Auth</span>
                    </div>
                    <p class="text-xs text-slate-600 mb-3">Module 4: Returns all admin service items.</p>
                    <button onclick="sendApi('GET', '/api/admin/services', null, true)" class="w-full bg-teal-600 hover:bg-teal-700 text-white font-medium text-xs py-2 rounded-lg transition shadow-sm">
                        <i class="fa-solid fa-briefcase mr-1"></i> Fetch /api/admin/services
                    </button>
                </div>

                <!-- 4.1 Service Detail -->
                <div class="bg-white rounded-xl border border-slate-200 shadow-sm p-4">
                    <div class="flex items-center justify-between mb-2">
                        <div class="flex items-center space-x-2">
                            <span class="method-badge badge-get">GET</span>
                            <span class="font-mono text-sm font-semibold text-slate-800">/api/admin/service-detail</span>
                        </div>
                        <span class="text-xs bg-purple-100 text-purple-700 px-2 py-0.5 rounded font-bold">Admin Auth</span>
                    </div>
                    <p class="text-xs text-slate-600 mb-3">Module 4.1: Returns Service Detail layout and default service SEO configuration.</p>
                    <button onclick="sendApi('GET', '/api/admin/service-detail', null, true)" class="w-full bg-teal-700 hover:bg-teal-800 text-white font-medium text-xs py-2 rounded-lg transition shadow-sm">
                        <i class="fa-solid fa-file-lines mr-1"></i> Fetch /api/admin/service-detail
                    </button>
                </div>

                <!-- 5. Portfolio Catalog -->
                <div class="bg-white rounded-xl border border-slate-200 shadow-sm p-4">
                    <div class="flex items-center justify-between mb-2">
                        <div class="flex items-center space-x-2">
                            <span class="method-badge badge-get">GET</span>
                            <span class="font-mono text-sm font-semibold text-slate-800">/api/admin/portfolio</span>
                        </div>
                        <span class="text-xs bg-purple-100 text-purple-700 px-2 py-0.5 rounded font-bold">Admin Auth</span>
                    </div>
                    <p class="text-xs text-slate-600 mb-3">Module 5: Returns all portfolio projects.</p>
                    <button onclick="sendApi('GET', '/api/admin/portfolio', null, true)" class="w-full bg-cyan-600 hover:bg-cyan-700 text-white font-medium text-xs py-2 rounded-lg transition shadow-sm">
                        <i class="fa-solid fa-folder-open mr-1"></i> Fetch /api/admin/portfolio
                    </button>
                </div>

                <!-- 5.1 Portfolio Detail -->
                <div class="bg-white rounded-xl border border-slate-200 shadow-sm p-4">
                    <div class="flex items-center justify-between mb-2">
                        <div class="flex items-center space-x-2">
                            <span class="method-badge badge-get">GET</span>
                            <span class="font-mono text-sm font-semibold text-slate-800">/api/admin/portfolio-detail</span>
                        </div>
                        <span class="text-xs bg-purple-100 text-purple-700 px-2 py-0.5 rounded font-bold">Admin Auth</span>
                    </div>
                    <p class="text-xs text-slate-600 mb-3">Module 5.1: Returns Portfolio Detail layout and case study SEO metadata.</p>
                    <button onclick="sendApi('GET', '/api/admin/portfolio-detail', null, true)" class="w-full bg-cyan-700 hover:bg-cyan-800 text-white font-medium text-xs py-2 rounded-lg transition shadow-sm">
                        <i class="fa-solid fa-images mr-1"></i> Fetch /api/admin/portfolio-detail
                    </button>
                </div>

                <!-- 6. Blog Feed -->
                <div class="bg-white rounded-xl border border-slate-200 shadow-sm p-4">
                    <div class="flex items-center justify-between mb-2">
                        <div class="flex items-center space-x-2">
                            <span class="method-badge badge-get">GET</span>
                            <span class="font-mono text-sm font-semibold text-slate-800">/api/admin/blog</span>
                        </div>
                        <span class="text-xs bg-purple-100 text-purple-700 px-2 py-0.5 rounded font-bold">Admin Auth</span>
                    </div>
                    <p class="text-xs text-slate-600 mb-3">Module 6: Returns all blog articles.</p>
                    <button onclick="sendApi('GET', '/api/admin/blog', null, true)" class="w-full bg-rose-600 hover:bg-rose-700 text-white font-medium text-xs py-2 rounded-lg transition shadow-sm">
                        <i class="fa-solid fa-newspaper mr-1"></i> Fetch /api/admin/blog
                    </button>
                </div>

                <!-- 6.1 Blog Detail -->
                <div class="bg-white rounded-xl border border-slate-200 shadow-sm p-4">
                    <div class="flex items-center justify-between mb-2">
                        <div class="flex items-center space-x-2">
                            <span class="method-badge badge-get">GET</span>
                            <span class="font-mono text-sm font-semibold text-slate-800">/api/admin/blog-detail</span>
                        </div>
                        <span class="text-xs bg-purple-100 text-purple-700 px-2 py-0.5 rounded font-bold">Admin Auth</span>
                    </div>
                    <p class="text-xs text-slate-600 mb-3">Module 6.1: Returns Blog article layout and article SEO metadata.</p>
                    <button onclick="sendApi('GET', '/api/admin/blog-detail', null, true)" class="w-full bg-rose-700 hover:bg-rose-800 text-white font-medium text-xs py-2 rounded-lg transition shadow-sm">
                        <i class="fa-solid fa-newspaper mr-1"></i> Fetch /api/admin/blog-detail
                    </button>
                </div>

                <!-- Settings -->
                <div class="bg-white rounded-xl border border-slate-200 shadow-sm p-4">
                    <div class="flex items-center justify-between mb-2">
                        <div class="flex items-center space-x-2">
                            <span class="method-badge badge-get">GET</span>
                            <span class="font-mono text-sm font-semibold text-slate-800">/api/admin/settings</span>
                        </div>
                        <span class="text-xs bg-purple-100 text-purple-700 px-2 py-0.5 rounded font-bold">Admin Auth</span>
                    </div>
                    <p class="text-xs text-slate-600 mb-3">Site Settings: Returns full site settings payload.</p>
                    <button onclick="sendApi('GET', '/api/admin/settings', null, true)" class="w-full bg-amber-600 hover:bg-amber-700 text-white font-medium text-xs py-2 rounded-lg transition shadow-sm">
                        <i class="fa-solid fa-sliders mr-1"></i> Fetch /api/admin/settings
                    </button>
                </div>

                <!-- Layouts -->
                <div class="bg-white rounded-xl border border-slate-200 shadow-sm p-4">
                    <div class="flex items-center justify-between mb-2">
                        <div class="flex items-center space-x-2">
                            <span class="method-badge badge-get">GET</span>
                            <span class="font-mono text-sm font-semibold text-slate-800">/api/admin/layouts</span>
                        </div>
                        <span class="text-xs bg-purple-100 text-purple-700 px-2 py-0.5 rounded font-bold">Admin Auth</span>
                    </div>
                    <p class="text-xs text-slate-600 mb-3">Layouts: Returns all active template keys and allowed ranges.</p>
                    <button onclick="sendApi('GET', '/api/admin/layouts', null, true)" class="w-full bg-emerald-600 hover:bg-emerald-700 text-white font-medium text-xs py-2 rounded-lg transition shadow-sm">
                        <i class="fa-solid fa-table-columns mr-1"></i> Fetch /api/admin/layouts
                    </button>
                </div>
            </div>

            <!-- ══════════════════════════════════════════════════════════ -->
            <!-- 2. LAYOUTS MODULE APIS -->
            <!-- ══════════════════════════════════════════════════════════ -->
            <div id="section-layouts" class="space-y-4 hidden">
                <div class="bg-white rounded-xl border border-slate-200 shadow-sm p-4">
                    <div class="flex items-center justify-between mb-3">
                        <div class="flex items-center space-x-2">
                            <span class="method-badge badge-get">GET</span>
                            <span class="font-mono text-sm font-semibold text-slate-800">/api/public/layouts</span>
                        </div>
                        <span class="text-xs text-slate-400">Public (No Auth)</span>
                    </div>
                    <button onclick="testPublicLayouts()" class="w-full bg-emerald-600 hover:bg-emerald-700 text-white font-medium text-xs py-2.5 rounded-lg transition shadow-sm flex items-center justify-center space-x-2">
                        <i class="fa-solid fa-arrows-rotate"></i>
                        <span>Fetch Public Active Layouts</span>
                    </button>
                </div>

                <div class="bg-white rounded-xl border border-slate-200 shadow-sm p-4">
                    <div class="flex items-center justify-between mb-3">
                        <div class="flex items-center space-x-2">
                            <span class="method-badge badge-get">GET</span>
                            <span class="font-mono text-sm font-semibold text-slate-800">/api/admin/layouts</span>
                        </div>
                        <span class="text-xs bg-slate-100 text-slate-600 px-2 py-0.5 rounded font-medium">Auth Required</span>
                    </div>
                    <p class="text-xs text-slate-600 mb-3">Admin read all 20 module layouts with their allowed template options and metadata.</p>
                    <button onclick="testAdminLayouts()" class="w-full bg-slate-800 hover:bg-slate-900 text-white font-medium text-xs py-2 rounded-lg transition">
                        Fetch Admin Layouts & Allowed IDs
                    </button>
                </div>

                <div class="bg-white rounded-xl border border-slate-200 shadow-sm p-4">
                    <div class="flex items-center justify-between mb-3">
                        <div class="flex items-center space-x-2">
                            <span class="method-badge badge-put">PUT</span>
                            <span class="font-mono text-sm font-semibold text-slate-800">/api/admin/layouts/:key</span>
                        </div>
                        <span class="text-xs bg-slate-100 text-slate-600 px-2 py-0.5 rounded font-medium">Auth Required</span>
                    </div>
                    <div class="space-y-2 mb-3">
                        <div>
                            <label class="text-xs font-medium text-slate-600">Select Layout Key</label>
                            <select id="layout-key-select" onchange="onLayoutKeyChanged()" class="w-full text-xs border border-slate-200 rounded p-2 mt-1 outline-none bg-white"></select>
                        </div>
                        <div>
                            <div class="flex items-center justify-between">
                                <label class="text-xs font-medium text-slate-600">Select Template ID</label>
                                <span id="layout-allowed-hint" class="text-[11px] text-blue-600 font-medium"></span>
                            </div>
                            <select id="layout-template-select" class="w-full text-xs border border-slate-200 rounded p-2 mt-1 outline-none bg-white"></select>
                        </div>
                    </div>
                    <button onclick="testUpdateLayout()" class="w-full bg-slate-800 hover:bg-slate-900 text-white font-medium text-xs py-2.5 rounded-lg transition">
                        <i class="fa-solid fa-pen-to-square"></i> Send PUT /api/admin/layouts/:key
                    </button>
                </div>

                <div class="bg-white rounded-xl border border-slate-200 shadow-sm p-4">
                    <div class="flex items-center justify-between mb-3">
                        <div class="flex items-center space-x-2">
                            <span class="method-badge badge-post">POST</span>
                            <span class="font-mono text-sm font-semibold text-slate-800">/api/admin/layouts/save</span>
                        </div>
                        <span class="text-xs bg-slate-100 text-slate-600 px-2 py-0.5 rounded font-medium">Auth Required</span>
                    </div>
                    <p class="text-xs text-slate-600 mb-3">Save multiple layouts in a single batch request.</p>
                    <button onclick="testBatchSaveLayouts()" class="w-full bg-emerald-600 hover:bg-emerald-700 text-white font-medium text-xs py-2 rounded-lg transition">
                        Save Batch Sample Layouts
                    </button>
                </div>
            </div>

            <!-- ══════════════════════════════════════════════════════════ -->
            <!-- MODULE 2: NAVBAR NAVIGATION APIS -->
            <!-- ══════════════════════════════════════════════════════════ -->
            <div id="section-navbar" class="space-y-4 hidden">
                <div class="bg-white rounded-xl border border-slate-200 shadow-sm p-4">
                    <div class="flex items-center justify-between mb-3">
                        <div class="flex items-center space-x-2">
                            <span class="method-badge badge-get">GET</span>
                            <span class="font-mono text-sm font-semibold text-slate-800">/api/public/navigation/navbar</span>
                        </div>
                        <span class="text-xs text-slate-400">Public (No Auth)</span>
                    </div>
                    <button onclick="testPublicNavbar()" class="w-full bg-indigo-600 hover:bg-indigo-700 text-white font-medium text-xs py-2.5 rounded-lg transition shadow-sm flex items-center justify-center space-x-2">
                        <span>Fetch Public Navbar Data</span>
                    </button>
                </div>

                <div class="bg-white rounded-xl border border-slate-200 shadow-sm p-4">
                    <div class="flex items-center justify-between mb-3">
                        <div class="flex items-center space-x-2">
                            <span class="method-badge badge-get">GET</span>
                            <span class="font-mono text-sm font-semibold text-slate-800">/api/admin/navigation/navbar</span>
                        </div>
                        <span class="text-xs bg-slate-100 text-slate-600 px-2 py-0.5 rounded font-medium">Auth Required</span>
                    </div>
                    <button onclick="testAdminNavbar()" class="w-full bg-slate-800 hover:bg-slate-900 text-white font-medium text-xs py-2 rounded-lg transition">
                        Fetch Admin Navbar Config
                    </button>
                </div>
            </div>

            <!-- ══════════════════════════════════════════════════════════ -->
            <!-- MODULE 3: HERO & HOME SECTIONS APIS -->
            <!-- ══════════════════════════════════════════════════════════ -->
            <div id="section-home" class="space-y-4 hidden">
                <div class="bg-white rounded-xl border border-slate-200 shadow-sm p-4">
                    <div class="flex items-center justify-between mb-3">
                        <div class="flex items-center space-x-2">
                            <span class="method-badge badge-get">GET</span>
                            <span class="font-mono text-sm font-semibold text-slate-800">/api/public/home-sections</span>
                        </div>
                        <span class="text-xs text-slate-400">Public (No Auth)</span>
                    </div>
                    <button onclick="testPublicHomeSections()" class="w-full bg-orange-600 hover:bg-orange-700 text-white font-medium text-xs py-2.5 rounded-lg transition shadow-sm flex items-center justify-center space-x-2">
                        <span>Fetch Home Sections Map</span>
                    </button>
                </div>
            </div>

            <!-- ══════════════════════════════════════════════════════════ -->
            <!-- MODULE 4: SERVICES APIS -->
            <!-- ══════════════════════════════════════════════════════════ -->
            <div id="section-services" class="space-y-4 hidden">
                <div class="bg-white rounded-xl border border-slate-200 shadow-sm p-4">
                    <div class="flex items-center justify-between mb-3">
                        <div class="flex items-center space-x-2">
                            <span class="method-badge badge-get">GET</span>
                            <span class="font-mono text-sm font-semibold text-slate-800">/api/public/services</span>
                        </div>
                        <span class="text-xs text-slate-400">Public (No Auth)</span>
                    </div>
                    <button onclick="testPublicServices()" class="w-full bg-teal-600 hover:bg-teal-700 text-white font-medium text-xs py-2.5 rounded-lg transition shadow-sm flex items-center justify-center space-x-2">
                        <span>Fetch Public Services Catalog</span>
                    </button>
                </div>

                <div class="bg-white rounded-xl border border-slate-200 shadow-sm p-4">
                    <div class="flex items-center justify-between mb-3">
                        <div class="flex items-center space-x-2">
                            <span class="method-badge badge-get">GET</span>
                            <span class="font-mono text-sm font-semibold text-slate-800">/api/public/services/:slug</span>
                        </div>
                        <span class="text-xs text-slate-400">Public (No Auth)</span>
                    </div>
                    <div class="flex space-x-2 mb-3">
                        <input id="srv-pub-slug" type="text" value="web-development" class="flex-1 text-xs border border-slate-200 rounded p-2 outline-none font-mono">
                        <button onclick="testPublicServiceDetail()" class="bg-teal-600 hover:bg-teal-700 text-white px-4 py-2 rounded-lg text-xs font-medium">
                            Fetch Detail
                        </button>
                    </div>
                </div>
            </div>

            <!-- ══════════════════════════════════════════════════════════ -->
            <!-- MODULE 5: PORTFOLIO & CASE STUDIES APIS -->
            <!-- ══════════════════════════════════════════════════════════ -->
            <div id="section-portfolio" class="space-y-4 hidden">
                <!-- 1. GET /api/public/portfolio -->
                <div class="bg-white rounded-xl border border-slate-200 shadow-sm p-4">
                    <div class="flex items-center justify-between mb-3">
                        <div class="flex items-center space-x-2">
                            <span class="method-badge badge-get">GET</span>
                            <span class="font-mono text-sm font-semibold text-slate-800">/api/public/portfolio</span>
                        </div>
                        <span class="text-xs text-slate-400">Public (No Auth)</span>
                    </div>
                    <p class="text-xs text-slate-600 mb-3">Catalog list (supports ?category=&featured=&limit=&page=). Returns compact items + total.</p>
                    <div class="grid grid-cols-2 gap-2 mb-3">
                        <input id="port-pub-cat" type="text" placeholder="Filter category (optional)" class="text-xs border border-slate-200 rounded p-1.5 outline-none">
                        <input id="port-pub-limit" type="number" placeholder="Limit (default 20)" value="20" class="text-xs border border-slate-200 rounded p-1.5 outline-none">
                    </div>
                    <button onclick="testPublicPortfolio()" class="w-full bg-cyan-600 hover:bg-cyan-700 text-white font-medium text-xs py-2.5 rounded-lg transition shadow-sm flex items-center justify-center space-x-2">
                        <i class="fa-solid fa-folder-open"></i>
                        <span>Fetch Public Portfolio Catalog</span>
                    </button>
                </div>

                <!-- 2. GET /api/public/portfolio/:slug -->
                <div class="bg-white rounded-xl border border-slate-200 shadow-sm p-4">
                    <div class="flex items-center justify-between mb-3">
                        <div class="flex items-center space-x-2">
                            <span class="method-badge badge-get">GET</span>
                            <span class="font-mono text-sm font-semibold text-slate-800">/api/public/portfolio/:slug</span>
                        </div>
                        <span class="text-xs text-slate-400">Public (No Auth)</span>
                    </div>
                    <p class="text-xs text-slate-600 mb-3">Full case study detail (challenge, solution, results metrics, gallery, resolved SEO).</p>
                    <div class="flex space-x-2 mb-3">
                        <input id="port-pub-slug" type="text" value="ecommerce-platform" placeholder="e.g. ecommerce-platform" class="flex-1 text-xs border border-slate-200 rounded p-2 outline-none font-mono">
                        <button onclick="testPublicPortfolioDetail()" class="bg-cyan-600 hover:bg-cyan-700 text-white px-4 py-2 rounded-lg text-xs font-medium">
                            Fetch Case Study
                        </button>
                    </div>
                </div>

                <!-- 3. GET /api/admin/portfolio -->
                <div class="bg-white rounded-xl border border-slate-200 shadow-sm p-4">
                    <div class="flex items-center justify-between mb-3">
                        <div class="flex items-center space-x-2">
                            <span class="method-badge badge-get">GET</span>
                            <span class="font-mono text-sm font-semibold text-slate-800">/api/admin/portfolio</span>
                        </div>
                        <span class="text-xs bg-slate-100 text-slate-600 px-2 py-0.5 rounded font-medium">Auth Required</span>
                    </div>
                    <p class="text-xs text-slate-600 mb-3">Admin list of all portfolio projects including unpublished items.</p>
                    <button onclick="testAdminPortfolio()" class="w-full bg-slate-800 hover:bg-slate-900 text-white font-medium text-xs py-2 rounded-lg transition">
                        Fetch All Admin Projects
                    </button>
                </div>

                <!-- 4. POST /api/admin/portfolio (Create) -->
                <div class="bg-white rounded-xl border border-slate-200 shadow-sm p-4">
                    <div class="flex items-center justify-between mb-3">
                        <div class="flex items-center space-x-2">
                            <span class="method-badge badge-post">POST</span>
                            <span class="font-mono text-sm font-semibold text-slate-800">/api/admin/portfolio</span>
                        </div>
                        <span class="text-xs bg-slate-100 text-slate-600 px-2 py-0.5 rounded font-medium">Auth Required</span>
                    </div>
                    <p class="text-xs text-slate-600 mb-3">Create case study project (required: title).</p>
                    <div class="space-y-2 mb-3">
                        <div class="grid grid-cols-2 gap-2">
                            <div>
                                <label class="text-xs font-medium text-slate-600">Title *</label>
                                <input id="port-create-title" type="text" value="Enterprise AI Supply Chain" class="w-full text-xs border border-slate-200 rounded p-1.5 mt-0.5 outline-none">
                            </div>
                            <div>
                                <label class="text-xs font-medium text-slate-600">Client</label>
                                <input id="port-create-client" type="text" value="LogiGlobal Inc" class="w-full text-xs border border-slate-200 rounded p-1.5 mt-0.5 outline-none">
                            </div>
                        </div>
                        <div class="grid grid-cols-2 gap-2">
                            <div>
                                <label class="text-xs font-medium text-slate-600">Year</label>
                                <input id="port-create-year" type="text" value="2025" class="w-full text-xs border border-slate-200 rounded p-1.5 mt-0.5 outline-none">
                            </div>
                            <div>
                                <label class="text-xs font-medium text-slate-600">Category</label>
                                <input id="port-create-cat" type="text" value="AI & Logistics" class="w-full text-xs border border-slate-200 rounded p-1.5 mt-0.5 outline-none">
                            </div>
                        </div>
                        <div>
                            <label class="text-xs font-medium text-slate-600">Technologies (comma-separated)</label>
                            <input id="port-create-tech" type="text" value="Python, React, Fastify, Docker, PyTorch" class="w-full text-xs border border-slate-200 rounded p-1.5 mt-0.5 outline-none">
                        </div>
                        <div>
                            <label class="text-xs font-medium text-slate-600">Short Description</label>
                            <input id="port-create-short" type="text" value="Predictive delivery intelligence optimizing 50,000 daily routes." class="w-full text-xs border border-slate-200 rounded p-1.5 mt-0.5 outline-none">
                        </div>
                    </div>
                    <button onclick="testCreatePortfolio()" class="w-full bg-cyan-600 hover:bg-cyan-700 text-white font-medium text-xs py-2 rounded-lg transition">
                        Create Portfolio Project
                    </button>
                </div>

                <!-- 5. PUT /api/admin/portfolio/:id & DELETE -->
                <div class="bg-white rounded-xl border border-slate-200 shadow-sm p-4">
                    <div class="flex items-center justify-between mb-3">
                        <div class="flex items-center space-x-2">
                            <span class="method-badge badge-put">PUT</span>
                            <span class="method-badge badge-del">DELETE</span>
                            <span class="font-mono text-sm font-semibold text-slate-800">/api/admin/portfolio/:id</span>
                        </div>
                        <span class="text-xs bg-slate-100 text-slate-600 px-2 py-0.5 rounded font-medium">Auth Required</span>
                    </div>
                    <div class="grid grid-cols-2 gap-2 mb-3">
                        <div>
                            <label class="text-xs font-medium text-slate-600">Project ID</label>
                            <input id="port-manage-id" type="number" value="1" class="w-full text-xs border border-slate-200 rounded p-1.5 mt-0.5 outline-none font-mono">
                        </div>
                        <div>
                            <label class="text-xs font-medium text-slate-600">Updated Title</label>
                            <input id="port-manage-title" type="text" value="E-Commerce Omnichannel Platform" class="w-full text-xs border border-slate-200 rounded p-1.5 mt-0.5 outline-none">
                        </div>
                    </div>
                    <div class="grid grid-cols-2 gap-2">
                        <button onclick="testUpdatePortfolio()" class="bg-slate-800 hover:bg-slate-900 text-white font-medium text-xs py-2 rounded-lg transition">
                            Update Project
                        </button>
                        <button onclick="testDeletePortfolio()" class="bg-red-600 hover:bg-red-700 text-white font-medium text-xs py-2 rounded-lg transition">
                            Delete Project
                        </button>
                    </div>
                </div>
            </div>

            <!-- ══════════════════════════════════════════════════════════ -->
            <!-- 2.5 MODULE 6: BLOG APIS -->
            <!-- ══════════════════════════════════════════════════════════ -->
            <div id="section-blog" class="space-y-4 hidden">
                <!-- 1. GET /api/public/blog -->
                <div class="bg-white rounded-xl border border-slate-200 shadow-sm p-4">
                    <div class="flex items-center justify-between mb-3">
                        <div class="flex items-center space-x-2">
                            <span class="method-badge badge-get">GET</span>
                            <span class="font-mono text-sm font-semibold text-slate-800">/api/public/blog</span>
                        </div>
                        <span class="text-xs text-slate-400">Public (No Auth)</span>
                    </div>
                    <p class="text-xs text-slate-600 mb-3">Feed (supports ?category=, ?tag=, ?limit=). Sorts by date descending and hides future-dated posts.</p>
                    <div class="grid grid-cols-3 gap-2 mb-3">
                        <input id="blog-pub-cat" type="text" placeholder="Category (e.g. Design)" class="text-xs border border-slate-200 rounded p-1.5 outline-none">
                        <input id="blog-pub-tag" type="text" placeholder="Tag (e.g. UI/UX)" class="text-xs border border-slate-200 rounded p-1.5 outline-none">
                        <input id="blog-pub-limit" type="number" placeholder="Limit" value="10" class="text-xs border border-slate-200 rounded p-1.5 outline-none">
                    </div>
                    <button onclick="testPublicBlog()" class="w-full bg-rose-600 hover:bg-rose-700 text-white font-medium text-xs py-2.5 rounded-lg transition shadow-sm flex items-center justify-center space-x-2">
                        <i class="fa-solid fa-newspaper"></i>
                        <span>Fetch Public Blog Feed</span>
                    </button>
                </div>

                <!-- 2. GET /api/public/blog/:slug -->
                <div class="bg-white rounded-xl border border-slate-200 shadow-sm p-4">
                    <div class="flex items-center justify-between mb-3">
                        <div class="flex items-center space-x-2">
                            <span class="method-badge badge-get">GET</span>
                            <span class="font-mono text-sm font-semibold text-slate-800">/api/public/blog/:slug</span>
                        </div>
                        <span class="text-xs text-slate-400">Public (No Auth)</span>
                    </div>
                    <p class="text-xs text-slate-600 mb-3">Single article detail (adds full content, author designation, resolved SEO).</p>
                    <div class="flex space-x-2 mb-3">
                        <input id="blog-pub-slug" type="text" value="evolution-of-design-systems" placeholder="e.g. evolution-of-design-systems" class="flex-1 text-xs border border-slate-200 rounded p-2 outline-none font-mono">
                        <button onclick="testPublicBlogDetail()" class="bg-rose-600 hover:bg-rose-700 text-white px-4 py-2 rounded-lg text-xs font-medium">
                            Fetch Article
                        </button>
                    </div>
                </div>

                <!-- 3. GET /api/admin/blog -->
                <div class="bg-white rounded-xl border border-slate-200 shadow-sm p-4">
                    <div class="flex items-center justify-between mb-3">
                        <div class="flex items-center space-x-2">
                            <span class="method-badge badge-get">GET</span>
                            <span class="font-mono text-sm font-semibold text-slate-800">/api/admin/blog</span>
                        </div>
                        <span class="text-xs bg-slate-100 text-slate-600 px-2 py-0.5 rounded font-medium">Auth Required</span>
                    </div>
                    <p class="text-xs text-slate-600 mb-3">Admin list of all articles including drafts and future scheduled posts.</p>
                    <button onclick="testAdminBlog()" class="w-full bg-slate-800 hover:bg-slate-900 text-white font-medium text-xs py-2 rounded-lg transition">
                        Fetch All Admin Blog Posts
                    </button>
                </div>

                <!-- 4. POST /api/admin/blog (Create) -->
                <div class="bg-white rounded-xl border border-slate-200 shadow-sm p-4">
                    <div class="flex items-center justify-between mb-3">
                        <div class="flex items-center space-x-2">
                            <span class="method-badge badge-post">POST</span>
                            <span class="font-mono text-sm font-semibold text-slate-800">/api/admin/blog</span>
                        </div>
                        <span class="text-xs bg-slate-100 text-slate-600 px-2 py-0.5 rounded font-medium">Auth Required</span>
                    </div>
                    <p class="text-xs text-slate-600 mb-3">Create new post (auto-computes readTime if not provided, auto-generates slug).</p>
                    <div class="space-y-2 mb-3">
                        <div class="grid grid-cols-2 gap-2">
                            <div>
                                <label class="text-xs font-medium text-slate-600">Title *</label>
                                <input id="blog-create-title" type="text" value="Modern Micro-Frontends at Scale" class="w-full text-xs border border-slate-200 rounded p-1.5 mt-0.5 outline-none">
                            </div>
                            <div>
                                <label class="text-xs font-medium text-slate-600">Category</label>
                                <input id="blog-create-cat" type="text" value="Architecture" class="w-full text-xs border border-slate-200 rounded p-1.5 mt-0.5 outline-none">
                            </div>
                        </div>
                        <div class="grid grid-cols-2 gap-2">
                            <div>
                                <label class="text-xs font-medium text-slate-600">Author Name</label>
                                <input id="blog-create-author-name" type="text" value="Vikram Singh" class="w-full text-xs border border-slate-200 rounded p-1.5 mt-0.5 outline-none">
                            </div>
                            <div>
                                <label class="text-xs font-medium text-slate-600">Author Designation</label>
                                <input id="blog-create-author-desig" type="text" value="Founder & CEO" class="w-full text-xs border border-slate-200 rounded p-1.5 mt-0.5 outline-none">
                            </div>
                        </div>
                        <div>
                            <label class="text-xs font-medium text-slate-600">Tags (comma-separated)</label>
                            <input id="blog-create-tags" type="text" value="Micro-Frontends, Web, Scale" class="w-full text-xs border border-slate-200 rounded p-1.5 mt-0.5 outline-none">
                        </div>
                        <div>
                            <label class="text-xs font-medium text-slate-600">HTML Content</label>
                            <textarea id="blog-create-content" rows="3" class="w-full text-xs border border-slate-200 rounded p-1.5 mt-0.5 outline-none">&lt;h2&gt;Decoupling the Web&lt;/h2&gt;&lt;p&gt;Micro-frontends allow multi-team enterprise organizations to independently build and deploy modular web applications.&lt;/p&gt;</textarea>
                        </div>
                    </div>
                    <button onclick="testCreateBlog()" class="w-full bg-rose-600 hover:bg-rose-700 text-white font-medium text-xs py-2 rounded-lg transition shadow-sm">
                        Create Blog Post
                    </button>
                </div>

                <!-- 5. PUT & DELETE /api/admin/blog/:id -->
                <div class="bg-white rounded-xl border border-slate-200 shadow-sm p-4">
                    <div class="flex items-center justify-between mb-3">
                        <div class="flex items-center space-x-2">
                            <span class="method-badge badge-put">PUT</span>
                            <span class="method-badge badge-del">DELETE</span>
                            <span class="font-mono text-sm font-semibold text-slate-800">/api/admin/blog/:id</span>
                        </div>
                        <span class="text-xs bg-slate-100 text-slate-600 px-2 py-0.5 rounded font-medium">Auth Required</span>
                    </div>
                    <div class="grid grid-cols-3 gap-2 mb-3">
                        <div>
                            <label class="text-xs font-medium text-slate-600">Post ID</label>
                            <input id="blog-manage-id" type="number" value="1" class="w-full text-xs border border-slate-200 rounded p-1.5 mt-0.5 outline-none font-mono">
                        </div>
                        <div class="col-span-2">
                            <label class="text-xs font-medium text-slate-600">Updated Title</label>
                            <input id="blog-manage-title" type="text" value="The Evolution of Enterprise Design Systems (Updated)" class="w-full text-xs border border-slate-200 rounded p-1.5 mt-0.5 outline-none">
                        </div>
                    </div>
                    <div class="grid grid-cols-2 gap-2">
                        <button onclick="testUpdateBlog()" class="bg-slate-800 hover:bg-slate-900 text-white font-medium text-xs py-2 rounded-lg transition">
                            Update Post
                        </button>
                        <button onclick="testDeleteBlog()" class="bg-red-600 hover:bg-red-700 text-white font-medium text-xs py-2 rounded-lg transition">
                            Delete Post
                        </button>
                    </div>
                </div>
            </div>

            <!-- ══════════════════════════════════════════════════════════ -->
            <!-- 3. SEO MODULE APIS -->
            <!-- ══════════════════════════════════════════════════════════ -->
            <div id="section-seo" class="space-y-4 hidden">
                <div class="bg-white rounded-xl border border-slate-200 shadow-sm p-4">
                    <div class="flex items-center justify-between mb-3">
                        <div class="flex items-center space-x-2">
                            <span class="method-badge badge-get">GET</span>
                            <span class="font-mono text-sm font-semibold text-slate-800">/api/public/seo/:page</span>
                        </div>
                        <span class="text-xs text-slate-400">Public (No Auth)</span>
                    </div>
                    <div class="flex space-x-2 mb-3">
                        <select id="seo-public-page" class="flex-1 text-xs border border-slate-200 rounded p-2 bg-white outline-none">
                            <option value="portfolio">portfolio</option>
                            <option value="portfolio-detail">portfolio-detail</option>
                            <option value="services">services</option>
                            <option value="service-detail">service-detail</option>
                            <option value="blog">blog</option>
                            <option value="blog-detail">blog-detail</option>
                            <option value="home">home</option>
                        </select>
                        <button onclick="testPublicSeo()" class="bg-purple-600 hover:bg-purple-700 text-white px-4 py-2 rounded-lg text-xs font-medium">
                            Read Public SEO
                        </button>
                    </div>
                </div>
            </div>

            <!-- ══════════════════════════════════════════════════════════ -->
            <!-- 4. MEDIA UPLOAD APIS -->
            <!-- ══════════════════════════════════════════════════════════ -->
            <div id="section-media" class="space-y-4 hidden">
                <div class="bg-white rounded-xl border border-slate-200 shadow-sm p-4">
                    <div class="flex items-center justify-between mb-3">
                        <div class="flex items-center space-x-2">
                            <span class="method-badge badge-post">POST</span>
                            <span class="font-mono text-sm font-semibold text-slate-800">/api/admin/uploads</span>
                        </div>
                        <span class="text-xs bg-slate-100 text-slate-600 px-2 py-0.5 rounded font-medium">Auth Required</span>
                    </div>
                    <div class="mb-3">
                        <input id="media-file-input" type="file" accept="image/*" class="w-full text-xs border border-slate-200 rounded p-2 outline-none bg-slate-50">
                    </div>
                    <button onclick="testMediaUpload()" class="w-full bg-pink-600 hover:bg-pink-700 text-white font-medium text-xs py-2.5 rounded-lg transition shadow-sm flex items-center justify-center space-x-2">
                        <span>Upload Image File</span>
                    </button>
                </div>
            </div>

            <!-- ══════════════════════════════════════════════════════════ -->
            <!-- 5. SITE SETTINGS APIS -->
            <!-- ══════════════════════════════════════════════════════════ -->
            <div id="section-settings" class="space-y-4 hidden">
                <!-- 1. GET /api/public/settings -->
                <div class="bg-white rounded-xl border border-slate-200 shadow-sm p-4">
                    <div class="flex items-center justify-between mb-3">
                        <div class="flex items-center space-x-2">
                            <span class="method-badge badge-get">GET</span>
                            <span class="font-mono text-sm font-semibold text-slate-800">/api/public/settings</span>
                        </div>
                        <span class="text-xs text-slate-400">Public (No Auth)</span>
                    </div>
                    <p class="text-xs text-slate-600 mb-3">Fetch public site identity, contacts, address, and socials.</p>
                    <button onclick="testPublicSettings()" class="w-full bg-amber-600 hover:bg-amber-700 text-white font-medium text-xs py-2.5 rounded-lg transition shadow-sm flex items-center justify-center space-x-2">
                        <i class="fa-solid fa-sliders"></i>
                        <span>Fetch Public Site Settings</span>
                    </button>
                </div>

                <!-- 2. GET /api/admin/settings -->
                <div class="bg-white rounded-xl border border-slate-200 shadow-sm p-4">
                    <div class="flex items-center justify-between mb-3">
                        <div class="flex items-center space-x-2">
                            <span class="method-badge badge-get">GET</span>
                            <span class="font-mono text-sm font-semibold text-slate-800">/api/admin/settings</span>
                        </div>
                        <span class="text-xs bg-slate-100 text-slate-600 px-2 py-0.5 rounded font-medium">Auth Required</span>
                    </div>
                    <p class="text-xs text-slate-600 mb-3">Fetch complete site settings for admin configuration.</p>
                    <button onclick="testAdminSettings()" class="w-full bg-slate-800 hover:bg-slate-900 text-white font-medium text-xs py-2 rounded-lg transition">
                        Fetch Admin Site Settings
                    </button>
                </div>

                <!-- 3. PUT /api/admin/settings -->
                <div class="bg-white rounded-xl border border-slate-200 shadow-sm p-4">
                    <div class="flex items-center justify-between mb-3">
                        <div class="flex items-center space-x-2">
                            <span class="method-badge badge-put">PUT</span>
                            <span class="font-mono text-sm font-semibold text-slate-800">/api/admin/settings</span>
                        </div>
                        <span class="text-xs bg-slate-100 text-slate-600 px-2 py-0.5 rounded font-medium">Auth Required</span>
                    </div>
                    <p class="text-xs text-slate-600 mb-3">Update global site metadata, branding, contacts, and socials.</p>
                    <div class="space-y-2 mb-3">
                        <div class="grid grid-cols-2 gap-2">
                            <div>
                                <label class="text-xs font-medium text-slate-600">Site Name</label>
                                <input id="settings-sitename" type="text" value="NexGen Digital" class="w-full text-xs border border-slate-200 rounded p-1.5 mt-0.5 outline-none">
                            </div>
                            <div>
                                <label class="text-xs font-medium text-slate-600">Contact Email</label>
                                <input id="settings-email" type="email" value="contact@nexgen.io" class="w-full text-xs border border-slate-200 rounded p-1.5 mt-0.5 outline-none">
                            </div>
                        </div>
                        <div class="grid grid-cols-2 gap-2">
                            <div>
                                <label class="text-xs font-medium text-slate-600">Phone</label>
                                <input id="settings-phone" type="text" value="+1 (800) 555-0199" class="w-full text-xs border border-slate-200 rounded p-1.5 mt-0.5 outline-none">
                            </div>
                            <div>
                                <label class="text-xs font-medium text-slate-600">Address</label>
                                <input id="settings-address" type="text" value="100 Innovation Way, Suite 400, San Francisco, CA" class="w-full text-xs border border-slate-200 rounded p-1.5 mt-0.5 outline-none">
                            </div>
                        </div>
                    </div>
                    <button onclick="testUpdateSettings()" class="w-full bg-amber-600 hover:bg-amber-700 text-white font-medium text-xs py-2 rounded-lg transition shadow-sm flex items-center justify-center space-x-2">
                        <i class="fa-solid fa-floppy-disk"></i>
                        <span>Save Site Settings</span>
                    </button>
                </div>
            </div>

        </div>

        <!-- Right Column: Live Response Viewer -->
        <div class="lg:col-span-4">
            <div class="bg-white rounded-xl border border-slate-200 p-4 shadow-sm sticky top-20">
                <div class="flex items-center justify-between mb-3 pb-2 border-b border-slate-100">
                    <div class="flex items-center space-x-2">
                        <span class="font-semibold text-xs text-slate-700 uppercase tracking-wider">Live Response</span>
                        <span id="resStatusBadge" class="hidden px-2 py-0.5 text-xs font-bold rounded"></span>
                    </div>
                    <div class="flex items-center space-x-2">
                        <span id="resTimeBadge" class="text-[11px] text-slate-400"></span>
                        <button onclick="copyResponse()" title="Copy JSON" class="text-xs text-slate-400 hover:text-blue-600 px-1.5 py-0.5">
                            <i class="fa-regular fa-copy"></i>
                        </button>
                    </div>
                </div>

                <div id="reqPreview" class="text-[11px] font-mono text-slate-500 mb-2 truncate">
                    Ready to send request...
                </div>

                <div class="bg-slate-900 rounded-lg p-3 overflow-hidden">
                    <pre id="responseOutput" class="text-xs font-mono text-emerald-400 overflow-x-auto max-h-[520px] leading-relaxed whitespace-pre-wrap">{
  "hint": "Click any API button on the left to test live output here."
}</pre>
                </div>
            </div>
        </div>

    </div>

    <!-- JavaScript logic -->
    <script>
        const LAYOUTS_SPEC = {
            'blogPageLayout':            { name: 'Blog catalog',       min: 1, max: 4, hint: 'layout1 to layout4 (Bento Grid, Classic Editorial, Minimalist Card, Full-width Media Feed)' },
            'blogDetailPageLayout':      { name: 'Blog article',       min: 1, max: 3, hint: 'layout1 to layout3 (Clean Editorial, Modern Hero with Sticky TOC, Media Centric)' },
            'portfolioPageLayout':       { name: 'Portfolio catalog',  min: 1, max: 8, hint: 'layout1 to layout8 (Filterable Grid, Fullwidth Masonry, Interactive Carousel, Minimal Cards, Showcase, Bento, Split Stack)' },
            'portfolioDetailPageLayout': { name: 'Case study detail',  min: 1, max: 4, hint: 'layout1 to layout4 (Story Driven, Metrics & Impact, Visual Gallery, Sidebar Overview)' },
            'servicesPageLayout':        { name: 'Services catalog',   min: 1, max: 8, hint: 'layout1 to layout8' },
            'serviceDetailPageLayout':   { name: 'Service detail',     min: 1, max: 4, hint: 'layout1 to layout4' },
            'topbarLayout':              { name: 'Top bar',            min: 0, max: 4, hint: 'layout0 (hidden) to layout4' },
            'navbarLayout':              { name: 'Navbar',             min: 1, max: 5, hint: 'layout1 to layout5' },
            'homePageLayout':            { name: 'Home',               min: 1, max: 10, hint: 'layout1 to layout10' },
            'blogPageLayout':            { name: 'Blog catalog',       min: 1, max: 4, hint: 'layout1 to layout4' },
            'blogDetailPageLayout':      { name: 'Blog article',       min: 1, max: 3, hint: 'layout1 to layout3' },
            'eventsPageLayout':          { name: 'Events feed',        min: 1, max: 3, hint: 'layout1 to layout3' },
            'eventDetailPageLayout':     { name: 'Event detail',       min: 1, max: 4, hint: 'layout1 to layout4' },
            'teamPageLayout':            { name: 'Team',               min: 1, max: 5, hint: 'layout1 to layout5' },
            'testimonialsPageLayout':    { name: 'Testimonials',       min: 1, max: 5, hint: 'layout1 to layout5' },
            'galleryPageLayout':         { name: 'Gallery',            min: 1, max: 4, hint: 'layout1 to layout4' },
            'contactPageLayout':         { name: 'Contact',            min: 1, max: 4, hint: 'layout1 to layout4' },
            'footerLayout':              { name: 'Footer',             min: 1, max: 4, hint: 'layout1 to layout4' },
            'careerPageLayout':          { name: 'Career',             min: 1, max: 4, hint: 'layout1 to layout4' },
            'faqPageLayout':             { name: 'FAQ',                min: 1, max: 4, hint: 'layout1 to layout4' },
            'termsPageLayout':           { name: 'Terms',              min: 1, max: 3, hint: 'layout1 to layout3' },
            'privacyPageLayout':         { name: 'Privacy',            min: 1, max: 3, hint: 'layout1 to layout3' }
        };

        let currentToken = localStorage.getItem('api_admin_token') || '';
        document.getElementById('baseUrlInput').value = window.location.origin;

        function initLayoutSelects() {
            const keySelect = document.getElementById('layout-key-select');
            keySelect.innerHTML = '';
            for (const [key, spec] of Object.entries(LAYOUTS_SPEC)) {
                const opt = document.createElement('option');
                opt.value = key;
                opt.textContent = `${key} (${spec.name})`;
                keySelect.appendChild(opt);
            }
            onLayoutKeyChanged();
        }

        function onLayoutKeyChanged() {
            const key = document.getElementById('layout-key-select').value;
            const spec = LAYOUTS_SPEC[key];
            const tplSelect = document.getElementById('layout-template-select');
            const hint = document.getElementById('layout-allowed-hint');

            tplSelect.innerHTML = '';
            hint.textContent = spec.hint;

            for (let i = spec.min; i <= spec.max; i++) {
                const opt = document.createElement('option');
                opt.value = `layout${i}`;
                opt.textContent = `layout${i}${i === 0 ? ' (hidden)' : ''}`;
                tplSelect.appendChild(opt);
            }
        }

        function updateTokenDisplay() {
            const badge = document.getElementById('tokenBadge');
            const badgeText = document.getElementById('tokenBadgeText');
            const display = document.getElementById('tokenDisplay');

            display.value = currentToken;

            if (currentToken) {
                badge.className = 'flex items-center space-x-1.5 px-3 py-1.5 rounded-full bg-emerald-50 text-emerald-700 border border-emerald-200 font-medium';
                badgeText.innerHTML = '<span class="w-2 h-2 rounded-full bg-emerald-500 inline-block mr-1"></span> Logged In';
            } else {
                badge.className = 'flex items-center space-x-1.5 px-3 py-1.5 rounded-full bg-amber-50 text-amber-700 border border-amber-200 font-medium';
                badgeText.innerHTML = '<span class="w-2 h-2 rounded-full bg-amber-500 inline-block mr-1"></span> No Token';
            }
        }

        function clearStoredToken() {
            currentToken = '';
            localStorage.removeItem('api_admin_token');
            updateTokenDisplay();
        }

        function copyToken() {
            if (!currentToken) return alert('No token to copy. Log in first!');
            navigator.clipboard.writeText(currentToken);
            alert('Token copied to clipboard!');
        }

        function copyResponse() {
            const text = document.getElementById('responseOutput').textContent;
            navigator.clipboard.writeText(text);
            alert('Response JSON copied!');
        }

        function switchTab(name) {
            document.querySelectorAll('.tab-btn').forEach(btn => btn.classList.remove('active'));
            document.getElementById(`tab-${name}`).classList.add('active');

            ['auth', 'modules', 'layouts', 'navbar', 'home', 'services', 'portfolio', 'blog', 'seo', 'media', 'settings'].forEach(s => {
                const el = document.getElementById(`section-${s}`);
                if (el) el.classList.add('hidden');
            });
            document.getElementById(`section-${name}`).classList.remove('hidden');
        }

        async function sendApi(method, path, body = null, requireAuth = false, isFormData = false) {
            const baseUrl = document.getElementById('baseUrlInput').value.replace(/\/+$/, '');
            const url = `${baseUrl}${path}`;

            const headers = { 'Accept': 'application/json' };
            if (requireAuth && currentToken) {
                headers['Authorization'] = `Bearer ${currentToken}`;
            }

            const options = { method, headers };

            if (isFormData) {
                options.body = body;
            } else if (body) {
                headers['Content-Type'] = 'application/json';
                options.body = JSON.stringify(body);
            }

            document.getElementById('reqPreview').textContent = `${method} ${path}`;
            const startTime = performance.now();

            try {
                const response = await fetch(url, options);
                const duration = Math.round(performance.now() - startTime);

                const data = await response.json();
                renderResponse(response.status, response.statusText, duration, data);
                return { status: response.status, data: data };
            } catch (err) {
                const duration = Math.round(performance.now() - startTime);
                renderResponse(0, 'Network / Server Error', duration, { error: err.message });
                return null;
            }
        }

        function renderResponse(status, statusText, duration, data) {
            const badge = document.getElementById('resStatusBadge');
            const timeBadge = document.getElementById('resTimeBadge');
            const output = document.getElementById('responseOutput');

            badge.classList.remove('hidden', 'bg-emerald-100', 'text-emerald-800', 'bg-amber-100', 'text-amber-800', 'bg-red-100', 'text-red-800');

            if (status >= 200 && status < 300) {
                badge.className = 'px-2 py-0.5 text-xs font-bold rounded bg-emerald-100 text-emerald-800';
            } else if (status >= 400 && status < 500) {
                badge.className = 'px-2 py-0.5 text-xs font-bold rounded bg-amber-100 text-amber-800';
            } else {
                badge.className = 'px-2 py-0.5 text-xs font-bold rounded bg-red-100 text-red-800';
            }

            badge.textContent = `${status} ${statusText}`;
            timeBadge.textContent = `${duration}ms`;
            output.textContent = JSON.stringify(data, null, 2);
        }

        // Auth
        async function testLogin() {
            const email = document.getElementById('login-email').value;
            const password = document.getElementById('login-password').value;
            const device_name = document.getElementById('login-device').value;
            const res = await sendApi('POST', '/api/admin/login', { email, password, device_name });
            if (res && res.data && res.data.token) {
                currentToken = res.data.token;
                localStorage.setItem('api_admin_token', currentToken);
                updateTokenDisplay();
            }
        }
        async function testMe() { await sendApi('GET', '/api/admin/me', null, true); }
        async function testUpdateProfile() {
            const name = document.getElementById('profile-name').value;
            const phone = document.getElementById('profile-phone').value;
            const bio = document.getElementById('profile-bio').value;
            const imgFile = document.getElementById('profile-image-file').files[0];
            const imgUrl = document.getElementById('profile-image-url').value;

            if (imgFile) {
                const formData = new FormData();
                if (name) formData.append('name', name);
                if (phone) formData.append('phone', phone);
                if (bio) formData.append('bio', bio);
                formData.append('profile_image', imgFile);
                await sendApi('POST', '/api/admin/profile', formData, true, true);
            } else {
                const body = { name, phone, bio };
                if (imgUrl) {
                    body.profile_image = imgUrl;
                }
                await sendApi('PUT', '/api/admin/profile', body, true);
            }
        }
        async function testChangePassword() {
            const current_password = document.getElementById('pwd-current').value;
            const password = document.getElementById('pwd-new').value;
            const password_confirmation = document.getElementById('pwd-confirm').value;
            await sendApi('PUT', '/api/admin/change-password', {
                current_password,
                password,
                password_confirmation
            }, true);
        }
        async function testLogout() {
            await sendApi('POST', '/api/admin/logout', {}, true);
            clearStoredToken();
        }

        // Individual Module GET helpers
        async function testAdminModule(moduleName) {
            await sendApi('GET', `/api/admin/modules/${moduleName}`, null, true);
        }

        // Layouts
        async function testPublicLayouts() { await sendApi('GET', '/api/public/layouts'); }
        async function testAdminLayouts() { await sendApi('GET', '/api/admin/layouts', null, true); }
        async function testUpdateLayout() {
            const key = document.getElementById('layout-key-select').value;
            const templateId = document.getElementById('layout-template-select').value;
            await sendApi('PUT', `/api/admin/layouts/${key}`, { templateId }, true);
        }
        async function testBatchSaveLayouts() {
            const body = {
                layouts: {
                    topbarLayout: "layout1",
                    navbarLayout: "layout1",
                    homePageLayout: "layout1",
                    footerLayout: "layout1"
                }
            };
            await sendApi('POST', '/api/admin/layouts/save', body, true);
        }

        // Navbar
        async function testPublicNavbar() { await sendApi('GET', '/api/public/navigation/navbar'); }
        async function testAdminNavbar() { await sendApi('GET', '/api/admin/navigation/navbar', null, true); }

        // Home
        async function testPublicHomeSections() { await sendApi('GET', '/api/public/home-sections'); }

        // Services
        async function testPublicServices() { await sendApi('GET', '/api/public/services'); }
        async function testPublicServiceDetail() {
            const slug = document.getElementById('srv-pub-slug').value.trim();
            await sendApi('GET', `/api/public/services/${slug}`);
        }

        // Module 5: Portfolio
        async function testPublicPortfolio() {
            const cat = document.getElementById('port-pub-cat').value;
            const limit = document.getElementById('port-pub-limit').value || 20;
            let path = `/api/public/portfolio?limit=${limit}`;
            if (cat) path += `&category=${encodeURIComponent(cat)}`;
            await sendApi('GET', path);
        }
        async function testPublicPortfolioDetail() {
            const slug = document.getElementById('port-pub-slug').value.trim();
            if (!slug) return alert('Enter project slug.');
            await sendApi('GET', `/api/public/portfolio/${slug}`);
        }
        async function testAdminPortfolio() {
            await sendApi('GET', '/api/admin/portfolio', null, true);
        }
        async function testCreatePortfolio() {
            const tech = document.getElementById('port-create-tech').value.split(',').map(s => s.trim()).filter(Boolean);
            const body = {
                title: document.getElementById('port-create-title').value,
                client: document.getElementById('port-create-client').value,
                year: document.getElementById('port-create-year').value,
                category: document.getElementById('port-create-cat').value,
                technologies: tech,
                shortDescription: document.getElementById('port-create-short').value,
                results: [
                    { metric: "+180%", label: "Operational Speedup" }
                ],
                isFeatured: true,
                isPublished: true
            };
            await sendApi('POST', '/api/admin/portfolio', body, true);
        }
        async function testUpdatePortfolio() {
            const id = document.getElementById('port-manage-id').value;
            const title = document.getElementById('port-manage-title').value;
            await sendApi('PUT', `/api/admin/portfolio/${id}`, { title }, true);
        }
        async function testDeletePortfolio() {
            const id = document.getElementById('port-manage-id').value;
            if (!confirm(`Delete project ID ${id}?`)) return;
            await sendApi('DELETE', `/api/admin/portfolio/${id}`, null, true);
        }

        // Blog Module JS
        async function testPublicBlog() {
            const cat = document.getElementById('blog-pub-cat').value;
            const tag = document.getElementById('blog-pub-tag').value;
            const limit = document.getElementById('blog-pub-limit').value || 10;
            let path = `/api/public/blog?limit=${limit}`;
            if (cat) path += `&category=${encodeURIComponent(cat)}`;
            if (tag) path += `&tag=${encodeURIComponent(tag)}`;
            await sendApi('GET', path);
        }
        async function testPublicBlogDetail() {
            const slug = document.getElementById('blog-pub-slug').value.trim();
            if (!slug) return alert('Enter blog article slug.');
            await sendApi('GET', `/api/public/blog/${slug}`);
        }
        async function testAdminBlog() {
            await sendApi('GET', '/api/admin/blog', null, true);
        }
        async function testCreateBlog() {
            const tags = document.getElementById('blog-create-tags').value.split(',').map(s => s.trim()).filter(Boolean);
            const body = {
                title: document.getElementById('blog-create-title').value,
                category: document.getElementById('blog-create-cat').value,
                author: {
                    name: document.getElementById('blog-create-author-name').value,
                    designation: document.getElementById('blog-create-author-desig').value,
                },
                tags: tags,
                content: document.getElementById('blog-create-content').value,
                status: 'published'
            };
            await sendApi('POST', '/api/admin/blog', body, true);
        }
        async function testUpdateBlog() {
            const id = document.getElementById('blog-manage-id').value;
            const title = document.getElementById('blog-manage-title').value;
            await sendApi('PUT', `/api/admin/blog/${id}`, { title }, true);
        }
        async function testDeleteBlog() {
            const id = document.getElementById('blog-manage-id').value;
            if (!confirm(`Delete blog post ID ${id}?`)) return;
            await sendApi('DELETE', `/api/admin/blog/${id}`, null, true);
        }

        // SEO
        async function testPublicSeo() {
            const page = document.getElementById('seo-public-page').value;
            await sendApi('GET', `/api/public/seo/${page}`);
        }

        // Media
        async function testMediaUpload() {
            const fileInput = document.getElementById('media-file-input');
            if (!fileInput.files.length) return alert('Please select an image file first.');
            const formData = new FormData();
            formData.append('file', fileInput.files[0]);
            await sendApi('POST', '/api/admin/uploads', formData, true, true);
        }

        // Settings
        async function testPublicSettings() { await sendApi('GET', '/api/public/settings'); }
        async function testAdminSettings() { await sendApi('GET', '/api/admin/settings', null, true); }
        async function testUpdateSettings() {
            const siteName = document.getElementById('settings-sitename').value;
            const email = document.getElementById('settings-email').value;
            const phone = document.getElementById('settings-phone').value;
            const address = document.getElementById('settings-address').value;
            await sendApi('PUT', '/api/admin/settings', { siteName, email, phone, address }, true);
        }

        initLayoutSelects();
        updateTokenDisplay();
    </script>
</body>
</html>
