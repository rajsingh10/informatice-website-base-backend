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
                        <span class="text-xs bg-teal-100 text-teal-700 px-2 py-0.5 rounded-full font-bold">11</span>
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

                    <button onclick="switchTab('events')" id="tab-events" class="tab-btn w-full flex items-center justify-between px-3 py-2 text-sm font-medium rounded-lg text-left text-slate-600 hover:bg-slate-50 transition">
                        <span class="flex items-center space-x-2.5">
                            <i class="fa-solid fa-calendar-days text-amber-500"></i>
                            <span>Module 7: Events</span>
                        </span>
                        <span class="text-xs bg-amber-100 text-amber-700 px-2 py-0.5 rounded-full font-bold">7</span>
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

                <!-- 7. Events Feed -->
                <div class="bg-white rounded-xl border border-slate-200 shadow-sm p-4">
                    <div class="flex items-center justify-between mb-2">
                        <div class="flex items-center space-x-2">
                            <span class="method-badge badge-get">GET</span>
                            <span class="font-mono text-sm font-semibold text-slate-800">/api/admin/modules/events</span>
                        </div>
                        <span class="text-xs bg-purple-100 text-purple-700 px-2 py-0.5 rounded font-bold">Admin Auth</span>
                    </div>
                    <p class="text-xs text-slate-600 mb-3">Module 7: Returns events feed layout (eventsPageLayout) and all events list.</p>
                    <button onclick="sendApi('GET', '/api/admin/modules/events', null, true)" class="w-full bg-amber-600 hover:bg-amber-700 text-white font-medium text-xs py-2 rounded-lg transition shadow-sm">
                        <i class="fa-solid fa-calendar-days mr-1"></i> Fetch /api/admin/modules/events
                    </button>
                </div>

                <!-- 7.1 Event Detail -->
                <div class="bg-white rounded-xl border border-slate-200 shadow-sm p-4">
                    <div class="flex items-center justify-between mb-2">
                        <div class="flex items-center space-x-2">
                            <span class="method-badge badge-get">GET</span>
                            <span class="font-mono text-sm font-semibold text-slate-800">/api/admin/modules/event-detail</span>
                        </div>
                        <span class="text-xs bg-purple-100 text-purple-700 px-2 py-0.5 rounded font-bold">Admin Auth</span>
                    </div>
                    <p class="text-xs text-slate-600 mb-3">Module 7.1: Returns event detail layout (eventDetailPageLayout) and default SEO metadata.</p>
                    <button onclick="sendApi('GET', '/api/admin/modules/event-detail', null, true)" class="w-full bg-amber-700 hover:bg-amber-800 text-white font-medium text-xs py-2 rounded-lg transition shadow-sm">
                        <i class="fa-solid fa-calendar-days mr-1"></i> Fetch /api/admin/modules/event-detail
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
                <!-- 1. GET Public Navbar -->
                <div class="bg-white rounded-xl border border-slate-200 shadow-sm p-4">
                    <div class="flex items-center justify-between mb-3">
                        <div class="flex items-center space-x-2">
                            <span class="method-badge badge-get">GET</span>
                            <span class="font-mono text-sm font-semibold text-slate-800">/api/public/navigation/navbar</span>
                        </div>
                        <span class="text-xs text-slate-400">Public (No Auth)</span>
                    </div>
                    <p class="text-xs text-slate-500 mb-3">Returns enabled items, CTA button, and navbar scroll behavior.</p>
                    <button onclick="testPublicNavbar()" class="w-full bg-indigo-600 hover:bg-indigo-700 text-white font-medium text-xs py-2.5 rounded-lg transition shadow-sm flex items-center justify-center space-x-2">
                        <span>Fetch Public Navbar Data</span>
                    </button>
                </div>

                <!-- 2. PUT / POST Update Navbar -->
                <div class="bg-white rounded-xl border border-indigo-200 shadow-sm p-4">
                    <div class="flex items-center justify-between mb-3">
                        <div class="flex items-center space-x-2">
                            <span class="method-badge badge-put">PUT</span>
                            <span class="font-mono text-sm font-semibold text-indigo-900">/api/public/navigation/navbar</span>
                        </div>
                        <span class="text-xs bg-indigo-100 text-indigo-700 px-2 py-0.5 rounded font-bold">Public / Admin Update</span>
                    </div>
                    <p class="text-xs text-slate-600 mb-3">Update navbar scroll behavior, CTA button, and menu items in one call.</p>
                    
                    <div class="space-y-3 mb-4">
                        <div>
                            <label class="block text-xs font-medium text-slate-700 mb-1">Scroll Behavior</label>
                            <select id="navbar-update-behavior" class="w-full text-xs border border-slate-300 rounded-lg p-2 focus:ring-2 focus:ring-indigo-500 font-mono">
                                <option value="scroll">scroll (Default)</option>
                                <option value="fixed">fixed</option>
                                <option value="colored">colored</option>
                                <option value="transparent">transparent</option>
                            </select>
                        </div>
                        <div class="grid grid-cols-2 gap-2">
                            <div>
                                <label class="block text-xs font-medium text-slate-700 mb-1">CTA Label</label>
                                <input type="text" id="navbar-update-cta-label" value="Get a Quote" class="w-full text-xs border border-slate-300 rounded-lg p-2">
                            </div>
                            <div>
                                <label class="block text-xs font-medium text-slate-700 mb-1">CTA URL</label>
                                <input type="text" id="navbar-update-cta-url" value="/contact" class="w-full text-xs border border-slate-300 rounded-lg p-2">
                            </div>
                        </div>
                    </div>

                    <button onclick="testUpdateNavbarQuick()" class="w-full bg-indigo-600 hover:bg-indigo-700 text-white font-medium text-xs py-2.5 rounded-lg transition shadow-sm flex items-center justify-center space-x-2">
                        <i class="fa-solid fa-floppy-disk mr-1"></i>
                        <span>Send PUT /api/public/navigation/navbar</span>
                    </button>
                </div>

                <!-- 3. GET Admin Navbar -->
                <div class="bg-white rounded-xl border border-slate-200 shadow-sm p-4">
                    <div class="flex items-center justify-between mb-3">
                        <div class="flex items-center space-x-2">
                            <span class="method-badge badge-get">GET</span>
                            <span class="font-mono text-sm font-semibold text-slate-800">/api/admin/navigation/navbar</span>
                        </div>
                        <span class="text-xs bg-slate-100 text-slate-600 px-2 py-0.5 rounded font-medium">Auth Required</span>
                    </div>
                    <p class="text-xs text-slate-500 mb-3">Returns all items including disabled ones for administrative management.</p>
                    <button onclick="testAdminNavbar()" class="w-full bg-slate-800 hover:bg-slate-900 text-white font-medium text-xs py-2 rounded-lg transition">
                        Fetch Admin Navbar Config
                    </button>
                </div>

                <!-- 4. Navbar Menu Links Manager (List, Add, Edit, Toggle, Reorder, Delete) -->
                <div class="bg-white rounded-xl border border-indigo-200 shadow-sm p-4">
                    <div class="flex items-center justify-between mb-3">
                        <div class="flex items-center space-x-2">
                            <span class="method-badge badge-post">POST</span>
                            <span class="method-badge badge-put">PUT</span>
                            <span class="method-badge badge-del">DELETE</span>
                            <span class="font-mono text-sm font-semibold text-indigo-900">/api/admin/navigation/navbar/items</span>
                        </div>
                        <span class="text-xs bg-indigo-100 text-indigo-700 px-2 py-0.5 rounded font-bold">Menu Links Manager</span>
                    </div>
                    <p class="text-xs text-slate-600 mb-3">Add new links, rename existing links, change destination URLs, show/hide, reorder, or delete links from header navigation.</p>

                    <!-- Fetch items button -->
                    <div class="mb-4">
                        <button onclick="testFetchNavbarItems()" class="w-full bg-slate-800 hover:bg-slate-900 text-white font-medium text-xs py-2 rounded-lg transition flex items-center justify-center space-x-1">
                            <i class="fa-solid fa-list mr-1"></i>
                            <span>Fetch All Menu Links (GET /api/admin/navigation/navbar/items)</span>
                        </button>
                    </div>

                    <!-- Add New Link Form -->
                    <div class="p-3 bg-slate-50 rounded-lg border border-slate-200 mb-4">
                        <span class="text-xs font-bold text-slate-700 uppercase tracking-wide block mb-2">
                            <i class="fa-solid fa-plus-circle text-indigo-600 mr-1"></i> Add New Navigation Link (POST)
                        </span>
                        <div class="grid grid-cols-2 gap-2 mb-2">
                            <div>
                                <label class="text-[11px] font-medium text-slate-600 block mb-0.5">Link Name *</label>
                                <input type="text" id="nav-item-add-label" placeholder="e.g. Pricing, Docs, Career" value="Pricing" class="w-full text-xs border border-slate-300 rounded p-1.5 outline-none">
                            </div>
                            <div>
                                <label class="text-[11px] font-medium text-slate-600 block mb-0.5">URL Path *</label>
                                <input type="text" id="nav-item-add-url" placeholder="e.g. /pricing, /docs" value="/pricing" class="w-full text-xs border border-slate-300 rounded p-1.5 outline-none font-mono">
                            </div>
                        </div>
                        <button onclick="testAddNavbarItem()" class="w-full bg-indigo-600 hover:bg-indigo-700 text-white font-medium text-xs py-2 rounded-lg transition flex items-center justify-center space-x-1">
                            <i class="fa-solid fa-plus mr-1"></i>
                            <span>+ Add Link (POST /api/admin/navigation/navbar/items)</span>
                        </button>
                    </div>

                    <!-- Edit / Toggle / Move / Delete Form -->
                    <div class="p-3 bg-slate-50 rounded-lg border border-slate-200">
                        <span class="text-xs font-bold text-slate-700 uppercase tracking-wide block mb-2">
                            <i class="fa-solid fa-pen-to-square text-indigo-600 mr-1"></i> Edit, Show/Hide, Reorder &amp; Delete Link
                        </span>
                        <div class="grid grid-cols-3 gap-2 mb-2">
                            <div>
                                <label class="text-[11px] font-medium text-slate-600 block mb-0.5">Item ID *</label>
                                <input type="text" id="nav-item-edit-id" value="nav-1" placeholder="e.g. nav-1" class="w-full text-xs border border-slate-300 rounded p-1.5 outline-none font-mono">
                            </div>
                            <div>
                                <label class="text-[11px] font-medium text-slate-600 block mb-0.5">Updated Name</label>
                                <input type="text" id="nav-item-edit-label" value="Home" placeholder="New label" class="w-full text-xs border border-slate-300 rounded p-1.5 outline-none">
                            </div>
                            <div>
                                <label class="text-[11px] font-medium text-slate-600 block mb-0.5">Updated URL</label>
                                <input type="text" id="nav-item-edit-url" value="/" placeholder="New URL" class="w-full text-xs border border-slate-300 rounded p-1.5 outline-none font-mono">
                            </div>
                        </div>
                        <div class="grid grid-cols-4 gap-2">
                            <button onclick="testUpdateNavbarItem()" class="bg-indigo-600 hover:bg-indigo-700 text-white font-medium text-xs py-2 rounded-lg transition">
                                <i class="fa-solid fa-check mr-1"></i> Edit / Save
                            </button>
                            <button onclick="testToggleNavbarItem()" class="bg-amber-600 hover:bg-amber-700 text-white font-medium text-xs py-2 rounded-lg transition">
                                <i class="fa-solid fa-eye mr-1"></i> Show/Hide
                            </button>
                            <div class="flex space-x-1">
                                <button onclick="testMoveNavbarItem('up')" class="flex-1 bg-slate-700 hover:bg-slate-800 text-white text-xs py-2 rounded-lg transition" title="Move Up">
                                    <i class="fa-solid fa-arrow-up"></i> Up
                                </button>
                                <button onclick="testMoveNavbarItem('down')" class="flex-1 bg-slate-700 hover:bg-slate-800 text-white text-xs py-2 rounded-lg transition" title="Move Down">
                                    <i class="fa-solid fa-arrow-down"></i> Down
                                </button>
                            </div>
                            <button onclick="testDeleteNavbarItem()" class="bg-red-600 hover:bg-red-700 text-white font-medium text-xs py-2 rounded-lg transition">
                                <i class="fa-solid fa-trash mr-1"></i> Delete
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            <!-- ══════════════════════════════════════════════════════════ -->
            <!-- MODULE 3: HERO & HOME SECTIONS APIS -->
            <!-- ══════════════════════════════════════════════════════════ -->
            <div id="section-home" class="space-y-4 hidden">
                <!-- 1. GET Public Home Sections -->
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

                <!-- 2. GET Admin Home Module & Sections -->
                <div class="bg-white rounded-xl border border-slate-200 shadow-sm p-4">
                    <div class="flex items-center justify-between mb-3">
                        <div class="flex items-center space-x-2">
                            <span class="method-badge badge-get">GET</span>
                            <span class="font-mono text-sm font-semibold text-slate-800">/api/admin/home-sections</span>
                        </div>
                        <span class="text-xs bg-slate-100 text-slate-600 px-2 py-0.5 rounded font-medium">Auth Required</span>
                    </div>
                    <p class="text-xs text-slate-500 mb-3">Returns visibility status, 10 detailed section cards, and active count.</p>
                    <div class="grid grid-cols-2 gap-2">
                        <button onclick="sendApi('GET', '/api/admin/home-sections', null, true)" class="bg-slate-800 hover:bg-slate-900 text-white font-medium text-xs py-2 rounded-lg transition">
                            /api/admin/home-sections
                        </button>
                        <button onclick="sendApi('GET', '/api/admin/home', null, true)" class="bg-orange-700 hover:bg-orange-800 text-white font-medium text-xs py-2 rounded-lg transition">
                            /api/admin/home
                        </button>
                    </div>
                </div>

                <!-- 3. POST Toggle Section ON / OFF -->
                <div class="bg-white rounded-xl border border-orange-200 shadow-sm p-4">
                    <div class="flex items-center justify-between mb-3">
                        <div class="flex items-center space-x-2">
                            <span class="method-badge badge-post">POST</span>
                            <span class="font-mono text-sm font-semibold text-orange-900">/api/admin/home-sections/:sectionId</span>
                        </div>
                        <span class="text-xs bg-orange-100 text-orange-700 px-2 py-0.5 rounded font-bold">Toggle On / Off</span>
                    </div>
                    <p class="text-xs text-slate-600 mb-3">Toggle or set visibility for any of the 10 homepage sections.</p>

                    <div class="grid grid-cols-2 gap-2 mb-3">
                        <div>
                            <label class="block text-xs font-medium text-slate-700 mb-1">Select Section</label>
                            <select id="home-sec-select" class="w-full text-xs border border-slate-300 rounded-lg p-2 font-mono">
                                <option value="hero">hero (Hero Section / Slider)</option>
                                <option value="about">about (About & Brand Story)</option>
                                <option value="services">services (Services Showcase)</option>
                                <option value="portfolio">portfolio (Portfolio Stack)</option>
                                <option value="stats">stats (Key Metrics & Achievements)</option>
                                <option value="clients">clients (Client Logos & Marquee)</option>
                                <option value="testimonials">testimonials (Reviews Wall)</option>
                                <option value="blog">blog (Latest Insights & Bento)</option>
                                <option value="faq">faq (FAQ & Accordion Questions)</option>
                                <option value="cta">cta (Call-to-Action & Contact Banner)</option>
                            </select>
                        </div>
                        <div>
                            <label class="block text-xs font-medium text-slate-700 mb-1">Visibility State</label>
                            <select id="home-sec-state" class="w-full text-xs border border-slate-300 rounded-lg p-2 font-mono">
                                <option value="false">Disable / Off (Hidden)</option>
                                <option value="true">Enable / On (Visible)</option>
                            </select>
                        </div>
                    </div>

                    <div class="grid grid-cols-2 gap-2">
                        <button onclick="testToggleHomeSection()" class="bg-orange-600 hover:bg-orange-700 text-white font-medium text-xs py-2.5 rounded-lg transition shadow-sm flex items-center justify-center space-x-1">
                            <i class="fa-solid fa-power-off mr-1"></i> Set Section State
                        </button>
                        <button onclick="testResetHomeSections()" class="bg-emerald-600 hover:bg-emerald-700 text-white font-medium text-xs py-2.5 rounded-lg transition shadow-sm flex items-center justify-center space-x-1">
                            <i class="fa-solid fa-rotate-left mr-1"></i> Enable All (Reset 10/10)
                        </button>
                    </div>
                </div>
            </div>

            <!-- ══════════════════════════════════════════════════════════ -->
            <!-- MODULE 4: SERVICES APIS -->
            <!-- ══════════════════════════════════════════════════════════ -->
            <div id="section-services" class="space-y-4 hidden">

                <!-- 1. Layout Switching: Catalog & Detail -->
                <div class="bg-white rounded-xl border border-slate-200 shadow-sm p-4">
                    <div class="flex items-center justify-between mb-3">
                        <div class="flex items-center space-x-2">
                            <span class="method-badge badge-put">PUT</span>
                            <span class="font-mono text-sm font-semibold text-slate-800">/api/admin/layouts/{key}</span>
                        </div>
                        <span class="text-xs bg-slate-100 text-slate-600 px-2 py-0.5 rounded font-medium">Auth Required</span>
                    </div>
                    <p class="text-xs text-slate-600 mb-3">Switch active template layout for Services catalog (layout1 to layout8) and Service detail (layout1 to layout4).</p>
                    <div class="grid grid-cols-2 gap-3 mb-3">
                        <div class="p-2.5 bg-slate-50 rounded-lg border border-slate-200">
                            <label class="text-xs font-semibold text-slate-700 block mb-1">Catalog Layout (1 - 8)</label>
                            <div class="flex space-x-2">
                                <select id="srv-layout-catalog" class="flex-1 text-xs border border-slate-300 rounded p-1.5 font-mono">
                                    <option value="layout1">layout1</option>
                                    <option value="layout2">layout2</option>
                                    <option value="layout3">layout3</option>
                                    <option value="layout4">layout4</option>
                                    <option value="layout5">layout5</option>
                                    <option value="layout6" selected>layout6</option>
                                    <option value="layout7">layout7</option>
                                    <option value="layout8">layout8</option>
                                </select>
                                <button onclick="switchServicesCatalogLayout()" class="bg-teal-600 hover:bg-teal-700 text-white px-3 py-1.5 rounded text-xs font-medium transition">
                                    Apply
                                </button>
                            </div>
                        </div>
                        <div class="p-2.5 bg-slate-50 rounded-lg border border-slate-200">
                            <label class="text-xs font-semibold text-slate-700 block mb-1">Detail Layout (1 - 4)</label>
                            <div class="flex space-x-2">
                                <select id="srv-layout-detail" class="flex-1 text-xs border border-slate-300 rounded p-1.5 font-mono">
                                    <option value="layout1" selected>layout1</option>
                                    <option value="layout2">layout2</option>
                                    <option value="layout3">layout3</option>
                                    <option value="layout4">layout4</option>
                                </select>
                                <button onclick="switchServiceDetailLayout()" class="bg-teal-600 hover:bg-teal-700 text-white px-3 py-1.5 rounded text-xs font-medium transition">
                                    Apply
                                </button>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- 2. GET /api/public/services (Catalog list) -->
                <div class="bg-white rounded-xl border border-slate-200 shadow-sm p-4">
                    <div class="flex items-center justify-between mb-3">
                        <div class="flex items-center space-x-2">
                            <span class="method-badge badge-get">GET</span>
                            <span class="font-mono text-sm font-semibold text-slate-800">/api/public/services</span>
                        </div>
                        <span class="text-xs text-slate-400">Public (No Auth)</span>
                    </div>
                    <p class="text-xs text-slate-600 mb-3">Public services catalog. Supports query filtering by <code class="font-mono text-teal-700">?category=</code>, <code class="font-mono text-teal-700">?limit=</code>, and <code class="font-mono text-teal-700">?page=</code>. Returns id, title, slug, category, shortDescription, icon, image, features plus total.</p>
                    <div class="grid grid-cols-3 gap-2 mb-3">
                        <input id="srv-pub-cat" type="text" placeholder="Category (optional)" class="text-xs border border-slate-200 rounded p-1.5 outline-none">
                        <input id="srv-pub-limit" type="number" placeholder="Limit (e.g. 20)" value="20" class="text-xs border border-slate-200 rounded p-1.5 outline-none">
                        <input id="srv-pub-page" type="number" placeholder="Page (e.g. 1)" value="1" class="text-xs border border-slate-200 rounded p-1.5 outline-none">
                    </div>
                    <button onclick="testPublicServices()" class="w-full bg-teal-600 hover:bg-teal-700 text-white font-medium text-xs py-2.5 rounded-lg transition shadow-sm flex items-center justify-center space-x-2">
                        <i class="fa-solid fa-layer-group"></i>
                        <span>Fetch Public Services Catalog</span>
                    </button>
                </div>

                <!-- 3. GET /api/public/services/:slug -->
                <div class="bg-white rounded-xl border border-slate-200 shadow-sm p-4">
                    <div class="flex items-center justify-between mb-3">
                        <div class="flex items-center space-x-2">
                            <span class="method-badge badge-get">GET</span>
                            <span class="font-mono text-sm font-semibold text-slate-800">/api/public/services/:slug</span>
                        </div>
                        <span class="text-xs text-slate-400">Public (No Auth)</span>
                    </div>
                    <p class="text-xs text-slate-600 mb-3">Returns the full service model: title, slug, category, shortDescription, fullDescription, icon, image, features, deliverables, order, isPublished, and resolved SEO.</p>
                    <div class="flex space-x-2 mb-3">
                        <input id="srv-pub-slug" type="text" value="web-development" placeholder="Enter service slug..." class="flex-1 text-xs border border-slate-200 rounded p-2 outline-none font-mono">
                        <button onclick="testPublicServiceDetail()" class="bg-teal-600 hover:bg-teal-700 text-white px-4 py-2 rounded-lg text-xs font-medium">
                            Fetch Detail
                        </button>
                    </div>
                </div>

                <!-- 4. GET /api/admin/services (NEW) -->
                <div class="bg-white rounded-xl border border-slate-200 shadow-sm p-4">
                    <div class="flex items-center justify-between mb-3">
                        <div class="flex items-center space-x-2">
                            <span class="method-badge badge-get">GET</span>
                            <span class="font-mono text-sm font-semibold text-slate-800">/api/admin/services</span>
                            <span class="text-[10px] bg-teal-100 text-teal-700 font-bold px-1.5 py-0.5 rounded">NEW</span>
                        </div>
                        <span class="text-xs bg-slate-100 text-slate-600 px-2 py-0.5 rounded font-medium">Auth Required</span>
                    </div>
                    <p class="text-xs text-slate-600 mb-3">Admin full list of services including unpublished records, sorting order, and admin edit metadata.</p>
                    <button onclick="testAdminServices()" class="w-full bg-slate-800 hover:bg-slate-900 text-white font-medium text-xs py-2 rounded-lg transition flex items-center justify-center space-x-2">
                        <i class="fa-solid fa-briefcase"></i>
                        <span>Fetch All Admin Services</span>
                    </button>
                </div>

                <!-- 5. GET /api/admin/services/:id (NEW) -->
                <div class="bg-white rounded-xl border border-slate-200 shadow-sm p-4">
                    <div class="flex items-center justify-between mb-3">
                        <div class="flex items-center space-x-2">
                            <span class="method-badge badge-get">GET</span>
                            <span class="font-mono text-sm font-semibold text-slate-800">/api/admin/services/:id</span>
                            <span class="text-[10px] bg-teal-100 text-teal-700 font-bold px-1.5 py-0.5 rounded">NEW</span>
                        </div>
                        <span class="text-xs bg-slate-100 text-slate-600 px-2 py-0.5 rounded font-medium">Auth Required</span>
                    </div>
                    <p class="text-xs text-slate-600 mb-3">Fetch single service record by primary ID for admin edit form.</p>
                    <div class="flex space-x-2">
                        <input id="srv-admin-id" type="number" value="1" placeholder="Service ID" class="w-32 text-xs border border-slate-200 rounded p-2 outline-none font-mono">
                        <button onclick="testAdminServiceById()" class="bg-slate-800 hover:bg-slate-900 text-white px-4 py-2 rounded-lg text-xs font-medium transition flex items-center space-x-1">
                            <i class="fa-solid fa-magnifying-glass"></i>
                            <span>Fetch Service by ID</span>
                        </button>
                    </div>
                </div>

                <!-- 6. POST /api/admin/services (Create) -->
                <div class="bg-white rounded-xl border border-slate-200 shadow-sm p-4">
                    <div class="flex items-center justify-between mb-3">
                        <div class="flex items-center space-x-2">
                            <span class="method-badge badge-post">POST</span>
                            <span class="font-mono text-sm font-semibold text-slate-800">/api/admin/services</span>
                        </div>
                        <span class="text-xs bg-slate-100 text-slate-600 px-2 py-0.5 rounded font-medium">Auth Required</span>
                    </div>
                    <p class="text-xs text-slate-600 mb-3">Create a new service. Required: <code class="font-mono text-teal-700 font-semibold">title</code>. Icon can be an icon name (e.g. Globe, Sparkles, Cpu, Layers) or image URL.</p>
                    <div class="space-y-2 mb-3">
                        <div class="grid grid-cols-2 gap-2">
                            <div>
                                <label class="text-xs font-medium text-slate-600">Title *</label>
                                <input id="srv-create-title" type="text" value="Cloud Architecture & DevOps" class="w-full text-xs border border-slate-200 rounded p-1.5 mt-0.5 outline-none">
                            </div>
                            <div>
                                <label class="text-xs font-medium text-slate-600">Category</label>
                                <input id="srv-create-cat" type="text" value="Cloud & Infrastructure" class="w-full text-xs border border-slate-200 rounded p-1.5 mt-0.5 outline-none">
                            </div>
                        </div>
                        <div class="grid grid-cols-3 gap-2">
                            <div>
                                <label class="text-xs font-medium text-slate-600">Icon</label>
                                <input id="srv-create-icon" type="text" value="Globe" class="w-full text-xs border border-slate-200 rounded p-1.5 mt-0.5 outline-none font-mono">
                            </div>
                            <div>
                                <label class="text-xs font-medium text-slate-600">Order</label>
                                <input id="srv-create-order" type="number" value="3" class="w-full text-xs border border-slate-200 rounded p-1.5 mt-0.5 outline-none">
                            </div>
                            <div>
                                <label class="text-xs font-medium text-slate-600">Published?</label>
                                <select id="srv-create-pub" class="w-full text-xs border border-slate-200 rounded p-1.5 mt-0.5 outline-none">
                                    <option value="true" selected>True (Published)</option>
                                    <option value="false">False (Draft)</option>
                                </select>
                            </div>
                        </div>
                        <div>
                            <label class="text-xs font-medium text-slate-600">Featured Image URL</label>
                            <input id="srv-create-img" type="text" value="https://images.unsplash.com/photo-1451187580459-43490279c0fa?auto=format&fit=crop&w=1200&q=80" class="w-full text-xs border border-slate-200 rounded p-1.5 mt-0.5 outline-none font-mono">
                        </div>
                        <div>
                            <label class="text-xs font-medium text-slate-600">Short Description</label>
                            <input id="srv-create-short" type="text" value="Enterprise cloud migration, serverless microservices, and automated CI/CD pipelines." class="w-full text-xs border border-slate-200 rounded p-1.5 mt-0.5 outline-none">
                        </div>
                        <div>
                            <label class="text-xs font-medium text-slate-600">Full Description (HTML)</label>
                            <textarea id="srv-create-full" rows="2" class="w-full text-xs border border-slate-200 rounded p-1.5 mt-0.5 outline-none font-mono">&lt;p&gt;We design and manage scalable, secure multi-cloud architectures built for high resilience and zero-downtime deployments.&lt;/p&gt;</textarea>
                        </div>
                        <div>
                            <label class="text-xs font-medium text-slate-600">Features (comma-separated)</label>
                            <input id="srv-create-features" type="text" value="AWS & GCP Migration, Kubernetes Orchestration, 99.99% Uptime SLA" class="w-full text-xs border border-slate-200 rounded p-1.5 mt-0.5 outline-none">
                        </div>
                        <div>
                            <label class="text-xs font-medium text-slate-600">Deliverables (comma-separated)</label>
                            <input id="srv-create-deliverables" type="text" value="Infrastructure as Code, Auto-scaling Clusters, 24/7 Monitoring Alerting" class="w-full text-xs border border-slate-200 rounded p-1.5 mt-0.5 outline-none">
                        </div>
                    </div>
                    <button onclick="testCreateService()" class="w-full bg-teal-600 hover:bg-teal-700 text-white font-medium text-xs py-2 rounded-lg transition shadow-sm flex items-center justify-center space-x-1">
                        <i class="fa-solid fa-plus"></i>
                        <span>Create Service</span>
                    </button>
                </div>

                <!-- 7. PUT /api/admin/services/:id & DELETE /api/admin/services/:id -->
                <div class="bg-white rounded-xl border border-slate-200 shadow-sm p-4">
                    <div class="flex items-center justify-between mb-3">
                        <div class="flex items-center space-x-2">
                            <span class="method-badge badge-put">PUT</span>
                            <span class="method-badge badge-del">DELETE</span>
                            <span class="font-mono text-sm font-semibold text-slate-800">/api/admin/services/:id</span>
                        </div>
                        <span class="text-xs bg-slate-100 text-slate-600 px-2 py-0.5 rounded font-medium">Auth Required</span>
                    </div>
                    <p class="text-xs text-slate-600 mb-3">Update or delete an existing service record.</p>
                    <div class="space-y-2 mb-3">
                        <div class="grid grid-cols-2 gap-2">
                            <div>
                                <label class="text-xs font-medium text-slate-600">Service ID *</label>
                                <input id="srv-manage-id" type="number" value="1" class="w-full text-xs border border-slate-200 rounded p-1.5 mt-0.5 outline-none font-mono">
                            </div>
                            <div>
                                <label class="text-xs font-medium text-slate-600">Updated Title</label>
                                <input id="srv-manage-title" type="text" value="Web Development & Full-Stack Apps" class="w-full text-xs border border-slate-200 rounded p-1.5 mt-0.5 outline-none">
                            </div>
                        </div>
                        <div class="grid grid-cols-2 gap-2">
                            <div>
                                <label class="text-xs font-medium text-slate-600">Updated Category</label>
                                <input id="srv-manage-cat" type="text" value="Web & Cloud Development" class="w-full text-xs border border-slate-200 rounded p-1.5 mt-0.5 outline-none">
                            </div>
                            <div>
                                <label class="text-xs font-medium text-slate-600">Published Status</label>
                                <select id="srv-manage-pub" class="w-full text-xs border border-slate-200 rounded p-1.5 mt-0.5 outline-none">
                                    <option value="true" selected>Published</option>
                                    <option value="false">Unpublished (Hidden)</option>
                                </select>
                            </div>
                        </div>
                        <div>
                            <label class="text-xs font-medium text-slate-600">Updated Short Description</label>
                            <input id="srv-manage-short" type="text" value="Custom websites and robust scalable backend APIs built with modern technologies." class="w-full text-xs border border-slate-200 rounded p-1.5 mt-0.5 outline-none">
                        </div>
                    </div>
                    <div class="grid grid-cols-2 gap-2">
                        <button onclick="testUpdateService()" class="bg-slate-800 hover:bg-slate-900 text-white font-medium text-xs py-2 rounded-lg transition flex items-center justify-center space-x-1">
                            <i class="fa-solid fa-pen-to-square"></i>
                            <span>Update Service</span>
                        </button>
                        <button onclick="testDeleteService()" class="bg-red-600 hover:bg-red-700 text-white font-medium text-xs py-2 rounded-lg transition flex items-center justify-center space-x-1">
                            <i class="fa-solid fa-trash"></i>
                            <span>Delete Service</span>
                        </button>
                    </div>
                </div>

                <!-- 8. Services SEO Endpoints -->
                <div class="bg-white rounded-xl border border-slate-200 shadow-sm p-4">
                    <div class="flex items-center justify-between mb-3">
                        <div class="flex items-center space-x-2">
                            <span class="method-badge badge-get">GET</span>
                            <span class="method-badge badge-put">PUT</span>
                            <span class="font-mono text-sm font-semibold text-slate-800">/api/public/seo/... &amp; /api/admin/seo/...</span>
                        </div>
                        <span class="text-xs bg-slate-100 text-slate-600 px-2 py-0.5 rounded font-medium">Catalog &amp; Detail SEO</span>
                    </div>
                    <p class="text-xs text-slate-600 mb-3">Read or update dedicated SEO meta tags for Services Catalog and Service Detail pages.</p>
                    <div class="grid grid-cols-2 gap-2 mb-2">
                        <button onclick="sendApi('GET', '/api/public/seo/services')" class="bg-slate-100 hover:bg-slate-200 text-slate-700 text-xs py-2 rounded-lg font-medium transition text-left px-3">
                            <span class="font-bold text-teal-600">GET</span> /public/seo/services
                        </button>
                        <button onclick="testUpdateServicesSeo()" class="bg-slate-100 hover:bg-slate-200 text-slate-700 text-xs py-2 rounded-lg font-medium transition text-left px-3">
                            <span class="font-bold text-blue-600">PUT</span> /admin/seo/services
                        </button>
                    </div>
                    <div class="grid grid-cols-2 gap-2">
                        <button onclick="sendApi('GET', '/api/public/seo/service-detail')" class="bg-slate-100 hover:bg-slate-200 text-slate-700 text-xs py-2 rounded-lg font-medium transition text-left px-3">
                            <span class="font-bold text-teal-600">GET</span> /public/seo/service-detail
                        </button>
                        <button onclick="testUpdateServiceDetailSeo()" class="bg-slate-100 hover:bg-slate-200 text-slate-700 text-xs py-2 rounded-lg font-medium transition text-left px-3">
                            <span class="font-bold text-blue-600">PUT</span> /admin/seo/service-detail
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
            <!-- 2.5 EVENTS MODULE APIS -->
            <!-- ══════════════════════════════════════════════════════════ -->
            <div id="section-events" class="space-y-4 hidden">
                <!-- 1. GET /api/public/events -->
                <div class="bg-white rounded-xl border border-slate-200 shadow-sm p-4">
                    <div class="flex items-center justify-between mb-3">
                        <div class="flex items-center space-x-2">
                            <span class="method-badge badge-get">GET</span>
                            <span class="font-mono text-sm font-semibold text-slate-800">/api/public/events</span>
                        </div>
                        <span class="text-xs text-slate-400">Public (No Auth)</span>
                    </div>
                    <p class="text-xs text-slate-600 mb-3">Public feed (defaults to upcoming events; supports ?when=upcoming|past|all, ?type=, ?online=1|0, ?limit=). Returns compact cards list.</p>
                    <div class="grid grid-cols-4 gap-2 mb-3">
                        <div>
                            <label class="text-[10px] text-slate-400 font-medium">When</label>
                            <select id="event-pub-when" class="w-full text-xs border border-slate-200 rounded p-1.5 outline-none bg-white">
                                <option value="upcoming" selected>Upcoming</option>
                                <option value="past">Past</option>
                                <option value="all">All</option>
                            </select>
                        </div>
                        <div>
                            <label class="text-[10px] text-slate-400 font-medium">Type</label>
                            <input id="event-pub-type" type="text" placeholder="e.g. conference" class="w-full text-xs border border-slate-200 rounded p-1.5 outline-none">
                        </div>
                        <div>
                            <label class="text-[10px] text-slate-400 font-medium">Online</label>
                            <select id="event-pub-online" class="w-full text-xs border border-slate-200 rounded p-1.5 outline-none bg-white">
                                <option value="">Any</option>
                                <option value="1">Online only</option>
                                <option value="0">In-person only</option>
                            </select>
                        </div>
                        <div>
                            <label class="text-[10px] text-slate-400 font-medium">Limit</label>
                            <input id="event-pub-limit" type="number" placeholder="Limit" value="10" class="w-full text-xs border border-slate-200 rounded p-1.5 outline-none">
                        </div>
                    </div>
                    <button onclick="testPublicEvents()" class="w-full bg-amber-600 hover:bg-amber-700 text-white font-medium text-xs py-2.5 rounded-lg transition shadow-sm flex items-center justify-center space-x-2">
                        <i class="fa-solid fa-calendar-days"></i>
                        <span>Fetch Public Events Feed</span>
                    </button>
                </div>

                <!-- 2. GET /api/public/events/:slug -->
                <div class="bg-white rounded-xl border border-slate-200 shadow-sm p-4">
                    <div class="flex items-center justify-between mb-3">
                        <div class="flex items-center space-x-2">
                            <span class="method-badge badge-get">GET</span>
                            <span class="font-mono text-sm font-semibold text-slate-800">/api/public/events/:slug</span>
                        </div>
                        <span class="text-xs text-slate-400">Public (No Auth)</span>
                    </div>
                    <p class="text-xs text-slate-600 mb-3">Single event detail by slug (adds longDescription, agenda, totalSeats, availableSeats, onlineUrl, resolved SEO).</p>
                    <div class="flex space-x-2 mb-3">
                        <input id="event-pub-slug" type="text" value="cloud-native-summit-2027" placeholder="e.g. cloud-native-summit-2027" class="flex-1 text-xs border border-slate-200 rounded p-2 outline-none font-mono">
                        <button onclick="testPublicEventDetail()" class="bg-amber-600 hover:bg-amber-700 text-white px-4 py-2 rounded-lg text-xs font-medium">
                            Fetch Event
                        </button>
                    </div>
                </div>

                <!-- 3. GET /api/admin/events -->
                <div class="bg-white rounded-xl border border-slate-200 shadow-sm p-4">
                    <div class="flex items-center justify-between mb-3">
                        <div class="flex items-center space-x-2">
                            <span class="method-badge badge-get">GET</span>
                            <span class="font-mono text-sm font-semibold text-slate-800">/api/admin/events</span>
                        </div>
                        <span class="text-xs bg-slate-100 text-slate-600 px-2 py-0.5 rounded font-medium">Auth Required</span>
                    </div>
                    <p class="text-xs text-slate-600 mb-3">Admin list of all events (draft, published, cancelled, completed), sorted by event date ascending.</p>
                    <button onclick="testAdminEvents()" class="w-full bg-slate-800 hover:bg-slate-900 text-white font-medium text-xs py-2 rounded-lg transition">
                        Fetch All Admin Events
                    </button>
                </div>

                <!-- 4. POST /api/admin/events (Create) -->
                <div class="bg-white rounded-xl border border-slate-200 shadow-sm p-4">
                    <div class="flex items-center justify-between mb-3">
                        <div class="flex items-center space-x-2">
                            <span class="method-badge badge-post">POST</span>
                            <span class="font-mono text-sm font-semibold text-slate-800">/api/admin/events</span>
                        </div>
                        <span class="text-xs bg-slate-100 text-slate-600 px-2 py-0.5 rounded font-medium">Auth Required</span>
                    </div>
                    <p class="text-xs text-slate-600 mb-3">Create new event (validates dates, venue, onlineUrl, seats, and price >= 0; auto-generates slug).</p>
                    <div class="space-y-2 mb-3">
                        <div class="grid grid-cols-3 gap-2">
                            <div class="col-span-2">
                                <label class="text-xs font-medium text-slate-600">Title *</label>
                                <input id="event-create-title" type="text" value="AI Agent Hackathon 2027" class="w-full text-xs border border-slate-200 rounded p-1.5 mt-0.5 outline-none">
                            </div>
                            <div>
                                <label class="text-xs font-medium text-slate-600">Type *</label>
                                <select id="event-create-type" class="w-full text-xs border border-slate-200 rounded p-1.5 mt-0.5 outline-none bg-white">
                                    <option value="hackathon" selected>hackathon</option>
                                    <option value="conference">conference</option>
                                    <option value="workshop">workshop</option>
                                    <option value="webinar">webinar</option>
                                    <option value="meetup">meetup</option>
                                    <option value="seminar">seminar</option>
                                </select>
                            </div>
                        </div>
                        <div class="grid grid-cols-4 gap-2">
                            <div>
                                <label class="text-xs font-medium text-slate-600">Start Date *</label>
                                <input id="event-create-date" type="date" value="2027-05-10" class="w-full text-xs border border-slate-200 rounded p-1.5 mt-0.5 outline-none">
                            </div>
                            <div>
                                <label class="text-xs font-medium text-slate-600">End Date</label>
                                <input id="event-create-enddate" type="date" value="2027-05-11" class="w-full text-xs border border-slate-200 rounded p-1.5 mt-0.5 outline-none">
                            </div>
                            <div>
                                <label class="text-xs font-medium text-slate-600">Start Time</label>
                                <input id="event-create-starttime" type="text" value="09:00 AM" class="w-full text-xs border border-slate-200 rounded p-1.5 mt-0.5 outline-none">
                            </div>
                            <div>
                                <label class="text-xs font-medium text-slate-600">End Time</label>
                                <input id="event-create-endtime" type="text" value="06:00 PM" class="w-full text-xs border border-slate-200 rounded p-1.5 mt-0.5 outline-none">
                            </div>
                        </div>
                        <div class="grid grid-cols-3 gap-2">
                            <div>
                                <label class="text-xs font-medium text-slate-600">Is Online?</label>
                                <select id="event-create-online" class="w-full text-xs border border-slate-200 rounded p-1.5 mt-0.5 outline-none bg-white">
                                    <option value="0">In-Person</option>
                                    <option value="1">Online</option>
                                </select>
                            </div>
                            <div>
                                <label class="text-xs font-medium text-slate-600">Venue *</label>
                                <input id="event-create-venue" type="text" value="Tech Hub Innovation Center, Austin, TX" class="w-full text-xs border border-slate-200 rounded p-1.5 mt-0.5 outline-none">
                            </div>
                            <div>
                                <label class="text-xs font-medium text-slate-600">Online URL (if online)</label>
                                <input id="event-create-onlineurl" type="url" placeholder="https://..." class="w-full text-xs border border-slate-200 rounded p-1.5 mt-0.5 outline-none font-mono">
                            </div>
                        </div>
                        <div class="grid grid-cols-3 gap-2">
                            <div>
                                <label class="text-xs font-medium text-slate-600">Price (0 = Free) *</label>
                                <input id="event-create-price" type="number" step="0.01" value="0" class="w-full text-xs border border-slate-200 rounded p-1.5 mt-0.5 outline-none">
                            </div>
                            <div>
                                <label class="text-xs font-medium text-slate-600">Total Seats</label>
                                <input id="event-create-totalseats" type="number" value="120" class="w-full text-xs border border-slate-200 rounded p-1.5 mt-0.5 outline-none">
                            </div>
                            <div>
                                <label class="text-xs font-medium text-slate-600">Available Seats</label>
                                <input id="event-create-availseats" type="number" value="120" class="w-full text-xs border border-slate-200 rounded p-1.5 mt-0.5 outline-none">
                            </div>
                        </div>
                        <div>
                            <label class="text-xs font-medium text-slate-600">Short Description *</label>
                            <input id="event-create-short" type="text" value="48-hour buildathon for developers creating next-gen autonomous agent workflows." class="w-full text-xs border border-slate-200 rounded p-1.5 mt-0.5 outline-none">
                        </div>
                        <div>
                            <label class="text-xs font-medium text-slate-600">Long Description (HTML or markdown)</label>
                            <textarea id="event-create-long" rows="3" class="w-full text-xs border border-slate-200 rounded p-1.5 mt-0.5 outline-none">&lt;h2&gt;About the Hackathon&lt;/h2&gt;&lt;p&gt;Join top builders, engineers, and researchers to design, train, and deploy autonomous LLM agents.&lt;/p&gt;</textarea>
                        </div>
                    </div>
                    <button onclick="testCreateEvent()" class="w-full bg-amber-600 hover:bg-amber-700 text-white font-medium text-xs py-2 rounded-lg transition shadow-sm">
                        Create Event
                    </button>
                </div>

                <!-- 5. PUT & DELETE /api/admin/events/:id -->
                <div class="bg-white rounded-xl border border-slate-200 shadow-sm p-4">
                    <div class="flex items-center justify-between mb-3">
                        <div class="flex items-center space-x-2">
                            <span class="method-badge badge-put">PUT</span>
                            <span class="method-badge badge-del">DELETE</span>
                            <span class="font-mono text-sm font-semibold text-slate-800">/api/admin/events/:id</span>
                        </div>
                        <span class="text-xs bg-slate-100 text-slate-600 px-2 py-0.5 rounded font-medium">Auth Required</span>
                    </div>
                    <div class="grid grid-cols-3 gap-2 mb-3">
                        <div>
                            <label class="text-xs font-medium text-slate-600">Event ID</label>
                            <input id="event-manage-id" type="number" value="1" class="w-full text-xs border border-slate-200 rounded p-1.5 mt-0.5 outline-none font-mono">
                        </div>
                        <div class="col-span-2">
                            <label class="text-xs font-medium text-slate-600">Updated Title</label>
                            <input id="event-manage-title" type="text" value="Cloud Native Architecture Summit 2027 (Updated)" class="w-full text-xs border border-slate-200 rounded p-1.5 mt-0.5 outline-none">
                        </div>
                    </div>
                    <div class="grid grid-cols-2 gap-2">
                        <button onclick="testUpdateEvent()" class="bg-slate-800 hover:bg-slate-900 text-white font-medium text-xs py-2 rounded-lg transition">
                            Update Event
                        </button>
                        <button onclick="testDeleteEvent()" class="bg-red-600 hover:bg-red-700 text-white font-medium text-xs py-2 rounded-lg transition">
                            Delete Event
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
                            <option value="events">events</option>
                            <option value="event-detail">event-detail</option>
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

            ['auth', 'modules', 'layouts', 'navbar', 'home', 'services', 'portfolio', 'blog', 'events', 'seo', 'media', 'settings'].forEach(s => {
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
        async function testUpdateNavbarQuick() {
            const behavior = document.getElementById('navbar-update-behavior').value;
            const ctaLabel = document.getElementById('navbar-update-cta-label').value;
            const ctaUrl = document.getElementById('navbar-update-cta-url').value;
            const body = {
                behavior: behavior,
                cta: {
                    enabled: true,
                    label: ctaLabel,
                    url: ctaUrl
                }
            };
            await sendApi('PUT', '/api/public/navigation/navbar', body);
        }
        async function testAdminNavbar() { await sendApi('GET', '/api/admin/navigation/navbar', null, true); }

        // Navbar Menu Links Manager
        async function testFetchNavbarItems() {
            await sendApi('GET', '/api/admin/navigation/navbar/items', null, true);
        }
        async function testAddNavbarItem() {
            const label = document.getElementById('nav-item-add-label').value.trim();
            const url = document.getElementById('nav-item-add-url').value.trim();
            if (!label || !url) return alert('Link name and URL are required.');
            await sendApi('POST', '/api/admin/navigation/navbar/items', { label, url, enabled: true }, true);
        }
        async function testUpdateNavbarItem() {
            const id = document.getElementById('nav-item-edit-id').value.trim();
            const label = document.getElementById('nav-item-edit-label').value.trim();
            const url = document.getElementById('nav-item-edit-url').value.trim();
            if (!id) return alert('Enter Item ID (e.g. nav-1)');
            await sendApi('PUT', `/api/admin/navigation/navbar/items/${id}`, { label, url }, true);
        }
        async function testToggleNavbarItem() {
            const id = document.getElementById('nav-item-edit-id').value.trim();
            if (!id) return alert('Enter Item ID');
            await sendApi('POST', `/api/admin/navigation/navbar/items/${id}/toggle`, {}, true);
        }
        async function testMoveNavbarItem(direction) {
            const id = document.getElementById('nav-item-edit-id').value.trim();
            if (!id) return alert('Enter Item ID');
            await sendApi('POST', `/api/admin/navigation/navbar/items/${id}/move-${direction}`, {}, true);
        }
        async function testDeleteNavbarItem() {
            const id = document.getElementById('nav-item-edit-id').value.trim();
            if (!id) return alert('Enter Item ID to delete');
            if (!confirm(`Delete navbar link #${id}?`)) return;
            await sendApi('DELETE', `/api/admin/navigation/navbar/items/${id}`, null, true);
        }

        // Home
        async function testPublicHomeSections() { await sendApi('GET', '/api/public/home-sections'); }
        async function testToggleHomeSection() {
            const sec = document.getElementById('home-sec-select').value;
            const state = document.getElementById('home-sec-state').value === 'true';
            await sendApi('POST', `/api/admin/home-sections/${sec}`, { enabled: state }, true);
        }
        async function testResetHomeSections() {
            await sendApi('POST', '/api/admin/home-sections/reset', {}, true);
        }

        // Module 4: Services
        async function switchServicesCatalogLayout() {
            const layout = document.getElementById('srv-layout-catalog').value;
            await sendApi('PUT', '/api/admin/layouts/servicesPageLayout', { layout }, true);
        }
        async function switchServiceDetailLayout() {
            const layout = document.getElementById('srv-layout-detail').value;
            await sendApi('PUT', '/api/admin/layouts/serviceDetailPageLayout', { layout }, true);
        }
        async function testPublicServices() {
            const cat = document.getElementById('srv-pub-cat').value.trim();
            const limit = document.getElementById('srv-pub-limit').value.trim();
            const page = document.getElementById('srv-pub-page').value.trim();
            let path = '/api/public/services';
            const params = [];
            if (cat) params.push(`category=${encodeURIComponent(cat)}`);
            if (limit) params.push(`limit=${encodeURIComponent(limit)}`);
            if (page) params.push(`page=${encodeURIComponent(page)}`);
            if (params.length) path += '?' + params.join('&');
            await sendApi('GET', path);
        }
        async function testPublicServiceDetail() {
            const slug = document.getElementById('srv-pub-slug').value.trim();
            if (!slug) return alert('Enter service slug');
            await sendApi('GET', `/api/public/services/${slug}`);
        }
        async function testAdminServices() {
            await sendApi('GET', '/api/admin/services', null, true);
        }
        async function testAdminServiceById() {
            const id = document.getElementById('srv-admin-id').value.trim();
            if (!id) return alert('Enter service ID');
            await sendApi('GET', `/api/admin/services/${id}`, null, true);
        }
        async function testCreateService() {
            const title = document.getElementById('srv-create-title').value.trim();
            if (!title) return alert('Title is required');
            const features = document.getElementById('srv-create-features').value.split(',').map(s => s.trim()).filter(Boolean);
            const deliverables = document.getElementById('srv-create-deliverables').value.split(',').map(s => s.trim()).filter(Boolean);
            const body = {
                title: title,
                category: document.getElementById('srv-create-cat').value.trim(),
                icon: document.getElementById('srv-create-icon').value.trim(),
                image: document.getElementById('srv-create-img').value.trim(),
                order: parseInt(document.getElementById('srv-create-order').value || '0', 10),
                isPublished: document.getElementById('srv-create-pub').value === 'true',
                shortDescription: document.getElementById('srv-create-short').value.trim(),
                fullDescription: document.getElementById('srv-create-full').value.trim(),
                features: features,
                deliverables: deliverables,
            };
            await sendApi('POST', '/api/admin/services', body, true);
        }
        async function testUpdateService() {
            const id = document.getElementById('srv-manage-id').value.trim();
            if (!id) return alert('Enter service ID to update');
            const body = {
                title: document.getElementById('srv-manage-title').value.trim(),
                category: document.getElementById('srv-manage-cat').value.trim(),
                isPublished: document.getElementById('srv-manage-pub').value === 'true',
                shortDescription: document.getElementById('srv-manage-short').value.trim(),
            };
            await sendApi('PUT', `/api/admin/services/${id}`, body, true);
        }
        async function testDeleteService() {
            const id = document.getElementById('srv-manage-id').value.trim();
            if (!id) return alert('Enter service ID to delete');
            if (!confirm(`Are you sure you want to delete service #${id}?`)) return;
            await sendApi('DELETE', `/api/admin/services/${id}`, null, true);
        }
        async function testUpdateServicesSeo() {
            const body = {
                title: 'Our Services - Enterprise Technology & Cloud',
                description: 'Explore full suite of engineering, cloud transformation, and AI software services.',
                keywords: 'services, enterprise engineering, cloud transformation',
            };
            await sendApi('PUT', '/api/admin/seo/services', body, true);
        }
        async function testUpdateServiceDetailSeo() {
            const body = {
                title: 'Service Detail - Enterprise Scalability',
                description: 'In-depth breakdown of deliverables, architecture scope, and technical roadmap.',
                keywords: 'service detail, deliverables, architecture',
            };
            await sendApi('PUT', '/api/admin/seo/service-detail', body, true);
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

        // Events Module JS
        async function testPublicEvents() {
            const when = document.getElementById('event-pub-when').value;
            const type = document.getElementById('event-pub-type').value.trim();
            const online = document.getElementById('event-pub-online').value;
            const limit = document.getElementById('event-pub-limit').value || 10;
            let path = `/api/public/events?limit=${limit}`;
            if (when) path += `&when=${encodeURIComponent(when)}`;
            if (type) path += `&type=${encodeURIComponent(type)}`;
            if (online !== '') path += `&online=${encodeURIComponent(online)}`;
            await sendApi('GET', path);
        }
        async function testPublicEventDetail() {
            const slug = document.getElementById('event-pub-slug').value.trim();
            if (!slug) return alert('Enter event slug.');
            await sendApi('GET', `/api/public/events/${slug}`);
        }
        async function testAdminEvents() {
            await sendApi('GET', '/api/admin/events', null, true);
        }
        async function testCreateEvent() {
            const isOnline = document.getElementById('event-create-online').value === '1';
            const body = {
                title: document.getElementById('event-create-title').value,
                type: document.getElementById('event-create-type').value,
                date: document.getElementById('event-create-date').value,
                endDate: document.getElementById('event-create-enddate').value || null,
                startTime: document.getElementById('event-create-starttime').value || null,
                endTime: document.getElementById('event-create-endtime').value || null,
                venue: document.getElementById('event-create-venue').value,
                isOnline: isOnline,
                onlineUrl: document.getElementById('event-create-onlineurl').value || null,
                price: parseFloat(document.getElementById('event-create-price').value) || 0,
                totalSeats: parseInt(document.getElementById('event-create-totalseats').value) || null,
                availableSeats: parseInt(document.getElementById('event-create-availseats').value) || null,
                shortDescription: document.getElementById('event-create-short').value,
                longDescription: document.getElementById('event-create-long').value,
                status: 'published'
            };
            await sendApi('POST', '/api/admin/events', body, true);
        }
        async function testUpdateEvent() {
            const id = document.getElementById('event-manage-id').value;
            const title = document.getElementById('event-manage-title').value;
            await sendApi('PUT', `/api/admin/events/${id}`, { title }, true);
        }
        async function testDeleteEvent() {
            const id = document.getElementById('event-manage-id').value;
            if (!confirm(`Delete event ID ${id}?`)) return;
            await sendApi('DELETE', `/api/admin/events/${id}`, null, true);
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
