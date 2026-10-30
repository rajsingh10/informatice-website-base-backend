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
        /* Custom slim scrollbar for nav and pre */
        .custom-scrollbar::-webkit-scrollbar {
            width: 5px;
        }
        .custom-scrollbar::-webkit-scrollbar-track {
            background: #f1f5f9;
            border-radius: 4px;
        }
        .custom-scrollbar::-webkit-scrollbar-thumb {
            background: #cbd5e1;
            border-radius: 4px;
        }
        .custom-scrollbar::-webkit-scrollbar-thumb:hover {
            background: #94a3b8;
        }
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
                <div class="flex items-center space-x-2 bg-slate-100 px-3 py-1.5 rounded-lg border border-slate-200">
                    <span class="text-slate-500 font-semibold">Base URL:</span>
                    <input id="baseUrlInput" type="text" value="{{ url('/') }}" placeholder="https://..." class="bg-transparent font-mono text-slate-800 outline-none w-60 sm:w-80 font-medium">
                    <button onclick="resetBaseUrl()" title="Reset Base URL to current domain/subfolder" class="text-slate-400 hover:text-blue-600 transition">
                        <i class="fa-solid fa-rotate-left"></i>
                    </button>
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
                <nav class="space-y-1 overflow-y-auto pr-1.5 custom-scrollbar h-[420px] max-h-[calc(100vh-320px)] min-h-[280px]">
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

                    <button onclick="switchTab('featured')" id="tab-featured" class="tab-btn w-full flex items-center justify-between px-3 py-2 text-sm font-medium rounded-lg text-left text-slate-600 hover:bg-slate-50 transition">
                        <span class="flex items-center space-x-2.5">
                            <i class="fa-solid fa-star text-amber-500"></i>
                            <span class="font-bold text-amber-900">Featured (isFeature)</span>
                        </span>
                        <span class="text-xs bg-amber-100 text-amber-700 px-2 py-0.5 rounded-full font-bold">ALL</span>
                    </button>

                    <button onclick="switchTab('layouts')" id="tab-layouts" class="tab-btn w-full flex items-center justify-between px-3 py-2 text-sm font-medium rounded-lg text-left text-slate-600 hover:bg-slate-50 transition">
                        <span class="flex items-center space-x-2.5">
                            <i class="fa-solid fa-table-columns text-emerald-500"></i>
                            <span>Layouts (Mod 1-5)</span>
                        </span>
                        <span class="text-xs bg-emerald-100 text-emerald-700 px-2 py-0.5 rounded-full font-bold">4</span>
                    </button>

                    <button onclick="switchTab('topbar')" id="tab-topbar" class="tab-btn w-full flex items-center justify-between px-3 py-2 text-sm font-medium rounded-lg text-left text-slate-600 hover:bg-slate-50 transition">
                        <span class="flex items-center space-x-2.5">
                            <i class="fa-solid fa-window-minimize text-blue-500"></i>
                            <span>Module 1: Top Bar</span>
                        </span>
                        <span class="text-xs bg-blue-100 text-blue-700 px-2 py-0.5 rounded-full font-bold">8</span>
                    </button>

                    <button onclick="switchTab('navbar')" id="tab-navbar" class="tab-btn w-full flex items-center justify-between px-3 py-2 text-sm font-medium rounded-lg text-left text-slate-600 hover:bg-slate-50 transition">
                        <span class="flex items-center space-x-2.5">
                            <i class="fa-solid fa-bars text-indigo-500"></i>
                            <span>Module 2: Navbar</span>
                        </span>
                        <span class="text-xs bg-indigo-100 text-indigo-700 px-2 py-0.5 rounded-full font-bold">7</span>
                    </button>

                    <button onclick="switchTab('home')" id="tab-home" class="tab-btn w-full flex items-center justify-between px-3 py-2 text-sm font-medium rounded-lg text-left text-slate-600 hover:bg-slate-50 transition">
                        <span class="flex items-center space-x-2.5">
                            <i class="fa-solid fa-house text-orange-500"></i>
                            <span>Module 3: Home</span>
                        </span>
                        <span class="text-xs bg-orange-100 text-orange-700 px-2 py-0.5 rounded-full font-bold">4</span>
                    </button>

                    <button onclick="switchTab('about')" id="tab-about" class="tab-btn w-full flex items-center justify-between px-3 py-2 text-sm font-medium rounded-lg text-left text-slate-600 hover:bg-slate-50 transition">
                        <span class="flex items-center space-x-2.5">
                            <i class="fa-solid fa-address-card text-sky-500"></i>
                            <span>About Page</span>
                        </span>
                        <span class="text-xs bg-sky-100 text-sky-700 px-2 py-0.5 rounded-full font-bold">5</span>
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

                    <button onclick="switchTab('team')" id="tab-team" class="tab-btn w-full flex items-center justify-between px-3 py-2 text-sm font-medium rounded-lg text-left text-slate-600 hover:bg-slate-50 transition">
                        <span class="flex items-center space-x-2.5">
                            <i class="fa-solid fa-users text-teal-600"></i>
                            <span>Module 8: Team</span>
                        </span>
                        <span class="text-xs bg-teal-100 text-teal-700 px-2 py-0.5 rounded-full font-bold">6</span>
                    </button>

                    <button onclick="switchTab('testimonials')" id="tab-testimonials" class="tab-btn w-full flex items-center justify-between px-3 py-2 text-sm font-medium rounded-lg text-left text-slate-600 hover:bg-slate-50 transition">
                        <span class="flex items-center space-x-2.5">
                            <i class="fa-solid fa-star text-amber-500"></i>
                            <span>Module 9: Testimonials</span>
                        </span>
                        <span class="text-xs bg-amber-100 text-amber-700 px-2 py-0.5 rounded-full font-bold">6</span>
                    </button>

                    <button onclick="switchTab('gallery')" id="tab-gallery" class="tab-btn w-full flex items-center justify-between px-3 py-2 text-sm font-medium rounded-lg text-left text-slate-600 hover:bg-slate-50 transition">
                        <span class="flex items-center space-x-2.5">
                            <i class="fa-solid fa-images text-indigo-500"></i>
                            <span>Module 10: Gallery</span>
                        </span>
                        <span class="text-xs bg-indigo-100 text-indigo-700 px-2 py-0.5 rounded-full font-bold">5</span>
                    </button>

                    <!-- Module 11: Contact -->
                    <button onclick="switchTab('contact')" id="tab-contact" class="tab-btn w-full flex items-center justify-between px-3 py-2 text-sm font-medium rounded-lg text-left text-slate-600 hover:bg-slate-50 transition">
                        <span class="flex items-center space-x-2.5">
                            <i class="fa-solid fa-envelope-open-text text-indigo-500"></i>
                            <span>Module 11: Contact</span>
                        </span>
                        <span class="text-xs bg-indigo-100 text-indigo-700 px-2 py-0.5 rounded-full font-bold">8</span>
                    </button>

                    <!-- Module 13: Careers -->
                    <button onclick="switchTab('careers')" id="tab-careers" class="tab-btn w-full flex items-center justify-between px-3 py-2 text-sm font-medium rounded-lg text-left text-slate-600 hover:bg-slate-50 transition">
                        <span class="flex items-center space-x-2.5">
                            <i class="fa-solid fa-briefcase text-blue-600"></i>
                            <span>Module 13: Careers</span>
                        </span>
                        <span class="text-xs bg-blue-100 text-blue-700 px-2 py-0.5 rounded-full font-bold">7</span>
                    </button>

                    <!-- Module 14: FAQs -->
                    <button onclick="switchTab('faqs')" id="tab-faqs" class="tab-btn w-full flex items-center justify-between px-3 py-2 text-sm font-medium rounded-lg text-left text-slate-600 hover:bg-slate-50 transition">
                        <span class="flex items-center space-x-2.5">
                            <i class="fa-solid fa-circle-question text-emerald-600"></i>
                            <span>Module 14: FAQs</span>
                        </span>
                        <span class="text-xs bg-emerald-100 text-emerald-700 px-2 py-0.5 rounded-full font-bold">6</span>
                    </button>

                    <!-- Module 15: Terms -->
                    <button onclick="switchTab('terms')" id="tab-terms" class="tab-btn w-full flex items-center justify-between px-3 py-2 text-sm font-medium rounded-lg text-left text-slate-600 hover:bg-slate-50 transition">
                        <span class="flex items-center space-x-2.5">
                            <i class="fa-solid fa-file-contract text-cyan-600"></i>
                            <span>Module 15: Terms</span>
                        </span>
                        <span class="text-xs bg-cyan-100 text-cyan-700 px-2 py-0.5 rounded-full font-bold">3</span>
                    </button>

                    <!-- Module 16: Privacy -->
                    <button onclick="switchTab('privacy')" id="tab-privacy" class="tab-btn w-full flex items-center justify-between px-3 py-2 text-sm font-medium rounded-lg text-left text-slate-600 hover:bg-slate-50 transition">
                        <span class="flex items-center space-x-2.5">
                            <i class="fa-solid fa-shield-halved text-violet-600"></i>
                            <span>Module 16: Privacy</span>
                        </span>
                        <span class="text-xs bg-violet-100 text-violet-700 px-2 py-0.5 rounded-full font-bold">3</span>
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

                <!-- About Page Module -->
                <div class="bg-white rounded-xl border border-slate-200 shadow-sm p-4">
                    <div class="flex items-center justify-between mb-2">
                        <div class="flex items-center space-x-2">
                            <span class="method-badge badge-get">GET</span>
                            <span class="font-mono text-sm font-semibold text-slate-800">/api/admin/about</span>
                        </div>
                        <span class="text-xs bg-purple-100 text-purple-700 px-2 py-0.5 rounded font-bold">Admin Auth</span>
                    </div>
                    <p class="text-xs text-slate-600 mb-3">About Page: Returns full company story, mission, vision, values, milestones, stats, layout, and image ALT attributes.</p>
                    <div class="grid grid-cols-2 gap-2">
                        <button onclick="sendApi('GET', '/api/admin/about', null, true)" class="bg-sky-600 hover:bg-sky-700 text-white font-medium text-xs py-2 rounded-lg transition shadow-sm">
                            <i class="fa-solid fa-address-card mr-1"></i> /admin/about
                        </button>
                        <button onclick="sendApi('GET', '/api/admin/modules/about', null, true)" class="bg-sky-800 hover:bg-sky-900 text-white font-medium text-xs py-2 rounded-lg transition shadow-sm">
                            <i class="fa-solid fa-layer-group mr-1"></i> /admin/modules/about
                        </button>
                    </div>
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

                <!-- 8. Team Members -->
                <div class="bg-white rounded-xl border border-slate-200 shadow-sm p-4">
                    <div class="flex items-center justify-between mb-2">
                        <div class="flex items-center space-x-2">
                            <span class="method-badge badge-get">GET</span>
                            <span class="font-mono text-sm font-semibold text-slate-800">/api/admin/modules/team</span>
                        </div>
                        <span class="text-xs bg-purple-100 text-purple-700 px-2 py-0.5 rounded font-bold">Admin Auth</span>
                    </div>
                    <p class="text-xs text-slate-600 mb-3">Module 8: Returns team layout (teamPageLayout) and all team members.</p>
                    <button onclick="sendApi('GET', '/api/admin/modules/team', null, true)" class="w-full bg-teal-600 hover:bg-teal-700 text-white font-medium text-xs py-2 rounded-lg transition shadow-sm">
                        <i class="fa-solid fa-users mr-1"></i> Fetch /api/admin/modules/team
                    </button>
                </div>

                <!-- 9. Testimonials & Reviews -->
                <div class="bg-white rounded-xl border border-slate-200 shadow-sm p-4">
                    <div class="flex items-center justify-between mb-2">
                        <div class="flex items-center space-x-2">
                            <span class="method-badge badge-get">GET</span>
                            <span class="font-mono text-sm font-semibold text-slate-800">/api/admin/modules/testimonials</span>
                        </div>
                        <span class="text-xs bg-purple-100 text-purple-700 px-2 py-0.5 rounded font-bold">Admin Auth</span>
                    </div>
                    <p class="text-xs text-slate-600 mb-3">Module 9: Returns testimonials layout (testimonialsPageLayout) and all client reviews.</p>
                    <button onclick="sendApi('GET', '/api/admin/modules/testimonials', null, true)" class="w-full bg-amber-600 hover:bg-amber-700 text-white font-medium text-xs py-2 rounded-lg transition shadow-sm">
                        <i class="fa-solid fa-star mr-1"></i> Fetch /api/admin/modules/testimonials
                    </button>
                </div>

                <!-- 11. Contact Page API (Module 11) -->
                <div class="bg-white rounded-xl border border-slate-200 shadow-sm p-4">
                    <div class="flex items-center justify-between mb-2">
                        <div class="flex items-center space-x-2">
                            <span class="method-badge badge-get">GET</span>
                            <span class="font-mono text-sm font-semibold text-slate-800">/api/admin/modules/contact</span>
                        </div>
                        <span class="text-xs bg-purple-100 text-purple-700 px-2 py-0.5 rounded font-bold">Admin Auth</span>
                    </div>
                    <p class="text-xs text-slate-600 mb-3">Module 11: Returns Contact Page layout (Split Form, Modern Card, etc.), contactInfo, SEO, and inquiry stats.</p>
                    <div class="grid grid-cols-2 gap-2">
                        <button onclick="sendApi('GET', '/api/admin/modules/contact', null, true)" class="bg-indigo-600 hover:bg-indigo-700 text-white font-medium text-xs py-2 rounded-lg transition shadow-sm">
                            <i class="fa-solid fa-address-book mr-1"></i> /admin/modules/contact
                        </button>
                        <button onclick="sendApi('GET', '/api/admin/contact', null, true)" class="bg-indigo-800 hover:bg-indigo-900 text-white font-medium text-xs py-2 rounded-lg transition shadow-sm">
                            <i class="fa-solid fa-envelope mr-1"></i> /admin/contact
                        </button>
                    </div>
                </div>

                <!-- 10. Gallery & Media -->
                <div class="bg-white rounded-xl border border-slate-200 shadow-sm p-4">
                    <div class="flex items-center justify-between mb-2">
                        <div class="flex items-center space-x-2">
                            <span class="method-badge badge-get">GET</span>
                            <span class="font-mono text-sm font-semibold text-slate-800">/api/admin/modules/gallery</span>
                        </div>
                        <span class="text-xs bg-purple-100 text-purple-700 px-2 py-0.5 rounded font-bold">Admin Auth</span>
                    </div>
                    <p class="text-xs text-slate-600 mb-3">Module 10: Returns gallery layout (galleryPageLayout) and all photo albums.</p>
                    <button onclick="sendApi('GET', '/api/admin/modules/gallery', null, true)" class="w-full bg-indigo-600 hover:bg-indigo-700 text-white font-medium text-xs py-2 rounded-lg transition shadow-sm">
                        <i class="fa-solid fa-images mr-1"></i> Fetch /api/admin/modules/gallery
                    </button>
                </div>

                <!-- 13. Careers (Job Listings & Openings) -->
                <div class="bg-white rounded-xl border border-slate-200 shadow-sm p-4">
                    <div class="flex items-center justify-between mb-2">
                        <div class="flex items-center space-x-2">
                            <span class="method-badge badge-get">GET</span>
                            <span class="font-mono text-sm font-semibold text-slate-800">/api/admin/modules/career</span>
                        </div>
                        <span class="text-xs bg-purple-100 text-purple-700 px-2 py-0.5 rounded font-bold">Admin Auth</span>
                    </div>
                    <p class="text-xs text-slate-600 mb-3">Module 13: Returns career layout (careerPageLayout) and all open/closed positions.</p>
                    <button onclick="sendApi('GET', '/api/admin/modules/career', null, true)" class="w-full bg-blue-600 hover:bg-blue-700 text-white font-medium text-xs py-2 rounded-lg transition shadow-sm">
                        <i class="fa-solid fa-briefcase mr-1"></i> Fetch /api/admin/modules/career
                    </button>
                </div>

                <!-- 14. FAQs -->
                <div class="bg-white rounded-xl border border-slate-200 shadow-sm p-4">
                    <div class="flex items-center justify-between mb-2">
                        <div class="flex items-center space-x-2">
                            <span class="method-badge badge-get">GET</span>
                            <span class="font-mono text-sm font-semibold text-slate-800">/api/admin/modules/faq</span>
                        </div>
                        <span class="text-xs bg-purple-100 text-purple-700 px-2 py-0.5 rounded font-bold">Admin Auth</span>
                    </div>
                    <p class="text-xs text-slate-600 mb-3">Module 14: Returns FAQ layout (faqPageLayout), categories, and all FAQ items.</p>
                    <button onclick="sendApi('GET', '/api/admin/modules/faq', null, true)" class="w-full bg-emerald-600 hover:bg-emerald-700 text-white font-medium text-xs py-2 rounded-lg transition shadow-sm">
                        <i class="fa-solid fa-circle-question mr-1"></i> Fetch /api/admin/modules/faq
                    </button>
                </div>

                <!-- 15. Terms of Service -->
                <div class="bg-white rounded-xl border border-slate-200 shadow-sm p-4">
                    <div class="flex items-center justify-between mb-2">
                        <div class="flex items-center space-x-2">
                            <span class="method-badge badge-get">GET</span>
                            <span class="font-mono text-sm font-semibold text-slate-800">/api/admin/modules/terms</span>
                        </div>
                        <span class="text-xs bg-purple-100 text-purple-700 px-2 py-0.5 rounded font-bold">Admin Auth</span>
                    </div>
                    <p class="text-xs text-slate-600 mb-3">Module 15: Returns terms layout (termsPageLayout) and legal text content.</p>
                    <button onclick="sendApi('GET', '/api/admin/modules/terms', null, true)" class="w-full bg-cyan-600 hover:bg-cyan-700 text-white font-medium text-xs py-2 rounded-lg transition shadow-sm">
                        <i class="fa-solid fa-file-contract mr-1"></i> Fetch /api/admin/modules/terms
                    </button>
                </div>

                <!-- 16. Privacy Policy -->
                <div class="bg-white rounded-xl border border-slate-200 shadow-sm p-4">
                    <div class="flex items-center justify-between mb-2">
                        <div class="flex items-center space-x-2">
                            <span class="method-badge badge-get">GET</span>
                            <span class="font-mono text-sm font-semibold text-slate-800">/api/admin/modules/privacy</span>
                        </div>
                        <span class="text-xs bg-purple-100 text-purple-700 px-2 py-0.5 rounded font-bold">Admin Auth</span>
                    </div>
                    <p class="text-xs text-slate-600 mb-3">Module 16: Returns privacy layout (privacyPageLayout) and policy content.</p>
                    <button onclick="sendApi('GET', '/api/admin/modules/privacy', null, true)" class="w-full bg-violet-600 hover:bg-violet-700 text-white font-medium text-xs py-2 rounded-lg transition shadow-sm">
                        <i class="fa-solid fa-shield-halved mr-1"></i> Fetch /api/admin/modules/privacy
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
            <!-- FEATURED (isFeature) ALL MODULES APIS -->
            <!-- ══════════════════════════════════════════════════════════ -->
            <div id="section-featured" class="space-y-4 hidden">
                <div class="bg-amber-50 border border-amber-200 rounded-xl p-4 text-xs text-amber-900 leading-relaxed">
                    <p class="font-bold mb-1"><i class="fa-solid fa-star mr-1 text-amber-600"></i> Featured Modules API (isFeature / isFeatured)</p>
                    <p>Dedicated standalone API that aggregates all entities marked <code class="bg-amber-100 px-1 py-0.5 rounded font-mono font-bold">isFeatured = true</code> across <strong>all website modules</strong>: Services, Portfolio, Blogs, Events, Team, Testimonials, Gallery, Careers, and FAQs.</p>
                </div>

                <!-- 1. Public isFeature Main Endpoint -->
                <div class="bg-white rounded-xl border border-slate-200 shadow-sm p-4">
                    <div class="flex items-center justify-between mb-2">
                        <div class="flex items-center space-x-2">
                            <span class="method-badge badge-get">GET</span>
                            <span class="font-mono text-sm font-semibold text-slate-800">/api/isFeature</span>
                        </div>
                        <span class="text-xs bg-emerald-100 text-emerald-700 px-2 py-0.5 rounded font-bold">Public (No Auth)</span>
                    </div>
                    <p class="text-xs text-slate-600 mb-3">Fetches all featured items across all modules with item counts per module and overall total.</p>
                    <div class="grid grid-cols-2 gap-2">
                        <button onclick="sendApi('GET', '/api/isFeature')" class="bg-amber-600 hover:bg-amber-700 text-white font-medium text-xs py-2 rounded-lg transition shadow-sm">
                            <i class="fa-solid fa-star mr-1"></i> Fetch /api/isFeature
                        </button>
                        <button onclick="sendApi('GET', '/api/public/featured')" class="bg-slate-800 hover:bg-slate-900 text-white font-medium text-xs py-2 rounded-lg transition shadow-sm">
                            <i class="fa-solid fa-bolt mr-1"></i> Fetch /api/public/featured
                        </button>
                    </div>
                </div>

                <!-- 2. Public Filter by Specific Module -->
                <div class="bg-white rounded-xl border border-slate-200 shadow-sm p-4">
                    <div class="flex items-center justify-between mb-2">
                        <div class="flex items-center space-x-2">
                            <span class="method-badge badge-get">GET</span>
                            <span class="font-mono text-sm font-semibold text-slate-800">/api/isFeature?module={name}&limit={n}</span>
                        </div>
                        <span class="text-xs bg-emerald-100 text-emerald-700 px-2 py-0.5 rounded font-bold">Public (No Auth)</span>
                    </div>
                    <p class="text-xs text-slate-600 mb-3">Filter featured items by specific module or apply a limit per module.</p>
                    <div class="grid grid-cols-1 sm:grid-cols-3 gap-2 mb-3">
                        <div>
                            <label class="block text-slate-500 text-[10px] uppercase font-bold mb-1">Module</label>
                            <select id="featModuleSelect" class="w-full bg-slate-50 border border-slate-200 rounded px-2 py-1.5 text-xs text-slate-800 font-medium">
                                <option value="">All Modules</option>
                                <option value="services">Module 4: Services</option>
                                <option value="portfolio">Module 5: Portfolio</option>
                                <option value="blogs">Module 6: Blogs</option>
                                <option value="events">Module 7: Events</option>
                                <option value="team">Module 8: Team Members</option>
                                <option value="testimonials">Module 9: Testimonials</option>
                                <option value="gallery">Module 10: Gallery</option>
                                <option value="careers">Module 13: Careers</option>
                                <option value="faqs">Module 14: FAQs</option>
                            </select>
                        </div>
                        <div>
                            <label class="block text-slate-500 text-[10px] uppercase font-bold mb-1">Limit (Optional)</label>
                            <input id="featLimitInput" type="number" placeholder="e.g. 5" class="w-full bg-slate-50 border border-slate-200 rounded px-2 py-1.5 text-xs text-slate-800">
                        </div>
                        <div class="flex items-end">
                            <button onclick="
                                const mod = document.getElementById('featModuleSelect').value;
                                const lim = document.getElementById('featLimitInput').value;
                                let params = [];
                                if (mod) params.push('module=' + encodeURIComponent(mod));
                                if (lim) params.push('limit=' + encodeURIComponent(lim));
                                const qs = params.length ? '?' + params.join('&') : '';
                                sendApi('GET', '/api/isFeature' + qs);
                            " class="w-full bg-blue-600 hover:bg-blue-700 text-white font-medium text-xs py-2 rounded-lg transition shadow-sm">
                                <i class="fa-solid fa-filter mr-1"></i> Filter Featured
                            </button>
                        </div>
                    </div>

                    <!-- Quick buttons -->
                    <div class="flex flex-wrap gap-1.5 pt-2 border-t border-slate-100 text-[11px]">
                        <span class="text-slate-400 font-semibold self-center mr-1">Quick:</span>
                        <button onclick="sendApi('GET', '/api/isFeature?module=services')" class="px-2 py-1 rounded bg-slate-100 hover:bg-slate-200 text-slate-700 font-mono">services</button>
                        <button onclick="sendApi('GET', '/api/isFeature?module=portfolio')" class="px-2 py-1 rounded bg-slate-100 hover:bg-slate-200 text-slate-700 font-mono">portfolio</button>
                        <button onclick="sendApi('GET', '/api/isFeature?module=blogs')" class="px-2 py-1 rounded bg-slate-100 hover:bg-slate-200 text-slate-700 font-mono">blogs</button>
                        <button onclick="sendApi('GET', '/api/isFeature?module=events')" class="px-2 py-1 rounded bg-slate-100 hover:bg-slate-200 text-slate-700 font-mono">events</button>
                        <button onclick="sendApi('GET', '/api/isFeature?module=team')" class="px-2 py-1 rounded bg-slate-100 hover:bg-slate-200 text-slate-700 font-mono">team</button>
                        <button onclick="sendApi('GET', '/api/isFeature?module=testimonials')" class="px-2 py-1 rounded bg-slate-100 hover:bg-slate-200 text-slate-700 font-mono">testimonials</button>
                        <button onclick="sendApi('GET', '/api/isFeature?module=gallery')" class="px-2 py-1 rounded bg-slate-100 hover:bg-slate-200 text-slate-700 font-mono">gallery</button>
                        <button onclick="sendApi('GET', '/api/isFeature?module=careers')" class="px-2 py-1 rounded bg-slate-100 hover:bg-slate-200 text-slate-700 font-mono">careers</button>
                        <button onclick="sendApi('GET', '/api/isFeature?module=faqs')" class="px-2 py-1 rounded bg-slate-100 hover:bg-slate-200 text-slate-700 font-mono">faqs</button>
                    </div>
                </div>

                <!-- 3. Admin Featured Endpoint -->
                <div class="bg-white rounded-xl border border-slate-200 shadow-sm p-4">
                    <div class="flex items-center justify-between mb-2">
                        <div class="flex items-center space-x-2">
                            <span class="method-badge badge-get">GET</span>
                            <span class="font-mono text-sm font-semibold text-slate-800">/api/admin/isFeature</span>
                        </div>
                        <span class="text-xs bg-purple-100 text-purple-700 px-2 py-0.5 rounded font-bold">Admin Auth</span>
                    </div>
                    <p class="text-xs text-slate-600 mb-3">Admin view returning full administration payloads for all featured items across all modules.</p>
                    <div class="grid grid-cols-2 gap-2">
                        <button onclick="sendApi('GET', '/api/admin/isFeature', null, true)" class="bg-purple-700 hover:bg-purple-800 text-white font-medium text-xs py-2 rounded-lg transition shadow-sm">
                            <i class="fa-solid fa-shield-halved mr-1"></i> /api/admin/isFeature
                        </button>
                        <button onclick="sendApi('GET', '/api/admin/featured', null, true)" class="bg-slate-700 hover:bg-slate-800 text-white font-medium text-xs py-2 rounded-lg transition shadow-sm">
                            <i class="fa-solid fa-shield-halved mr-1"></i> /api/admin/featured
                        </button>
                    </div>
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
            <!-- MODULE 1: TOP BAR APIS -->
            <!-- ══════════════════════════════════════════════════════════ -->
            <div id="section-topbar" class="space-y-4 hidden">
                <div class="bg-blue-50 border border-blue-200 rounded-xl p-4 text-xs text-blue-900 leading-relaxed flex items-center justify-between">
                    <div>
                        <p class="font-bold mb-1"><i class="fa-solid fa-window-minimize mr-1 text-blue-600"></i> Module 1: Top Bar Header APIs</p>
                        <p>Manage the top notification/contact bar (Email, Phone, Notice Badge/Text, Social Media Icons, CTA Button & Templates layout0–layout4).</p>
                    </div>
                    <button onclick="testToggleTopbar()" class="bg-blue-600 hover:bg-blue-700 text-white font-semibold text-xs px-3 py-1.5 rounded-lg transition shadow-sm whitespace-nowrap">
                        <i class="fa-solid fa-power-off mr-1"></i> Toggle On / Off
                    </button>
                </div>

                <!-- 1. GET Public & Admin Topbar Row -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div class="bg-white rounded-xl border border-slate-200 shadow-sm p-4">
                        <div class="flex items-center justify-between mb-2">
                            <div class="flex items-center space-x-2">
                                <span class="method-badge badge-get">GET</span>
                                <span class="font-mono text-sm font-semibold text-slate-800">/api/public/topbar</span>
                            </div>
                            <span class="text-xs text-slate-400">Public (No Auth)</span>
                        </div>
                        <p class="text-xs text-slate-500 mb-3">Fetch public topbar data: active template, email, phone, notice, socials & CTA button.</p>
                        <button onclick="testPublicTopbar()" class="w-full bg-blue-600 hover:bg-blue-700 text-white font-medium text-xs py-2 rounded-lg transition shadow-sm flex items-center justify-center space-x-1">
                            <i class="fa-solid fa-arrow-down mr-1"></i>
                            <span>Fetch Public Topbar</span>
                        </button>
                    </div>

                    <div class="bg-white rounded-xl border border-slate-200 shadow-sm p-4">
                        <div class="flex items-center justify-between mb-2">
                            <div class="flex items-center space-x-2">
                                <span class="method-badge badge-get">GET</span>
                                <span class="font-mono text-sm font-semibold text-slate-800">/api/admin/topbar</span>
                            </div>
                            <span class="text-xs bg-purple-100 text-purple-700 px-2 py-0.5 rounded font-bold">Admin Auth</span>
                        </div>
                        <p class="text-xs text-slate-500 mb-3">Fetch full admin topbar config: includes disabled links & allowed templates.</p>
                        <button onclick="testAdminTopbar()" class="w-full bg-slate-900 hover:bg-slate-800 text-white font-medium text-xs py-2 rounded-lg transition shadow-sm flex items-center justify-center space-x-1">
                            <i class="fa-solid fa-lock mr-1"></i>
                            <span>Fetch Admin Topbar</span>
                        </button>
                    </div>
                </div>

                <!-- 2. PUT / POST Edit Topbar Full Form -->
                <div class="bg-white rounded-xl border border-blue-200 shadow-sm p-4">
                    <div class="flex items-center justify-between mb-3">
                        <div class="flex items-center space-x-2">
                            <span class="method-badge badge-put">PUT</span>
                            <span class="font-mono text-sm font-semibold text-blue-900">/api/admin/topbar</span>
                        </div>
                        <span class="text-xs bg-blue-100 text-blue-700 px-2 py-0.5 rounded font-bold">Edit / Update</span>
                    </div>
                    <p class="text-xs text-slate-600 mb-4">Edit all Topbar components shown in the header (Email, Phone, Notice, CTA & Layout).</p>

                    <div class="space-y-3 mb-4">
                        <!-- Layout & Status -->
                        <div class="grid grid-cols-2 gap-3">
                            <div>
                                <label class="block text-xs font-semibold text-slate-700 mb-1">Topbar Layout Template</label>
                                <select id="topbar-edit-layout" class="w-full text-xs border border-slate-300 rounded-lg p-2 font-mono">
                                    <option value="layout1">layout1 (Default Standard)</option>
                                    <option value="layout2">layout2 (Compact)</option>
                                    <option value="layout3">layout3 (Centered Notice)</option>
                                    <option value="layout4">layout4 (Accent Background)</option>
                                    <option value="layout0">layout0 (Hidden / Disabled)</option>
                                </select>
                            </div>
                            <div>
                                <label class="block text-xs font-semibold text-slate-700 mb-1">Visibility Status</label>
                                <select id="topbar-edit-enabled" class="w-full text-xs border border-slate-300 rounded-lg p-2 font-mono">
                                    <option value="true">Enabled (Visible)</option>
                                    <option value="false">Disabled (Hidden)</option>
                                </select>
                            </div>
                        </div>

                        <!-- Contact Info -->
                        <div class="grid grid-cols-2 gap-3">
                            <div>
                                <label class="block text-xs font-semibold text-slate-700 mb-1"><i class="fa-solid fa-envelope mr-1 text-slate-400"></i> Email Address</label>
                                <input type="text" id="topbar-edit-email" value="hello@nexgensolutions.com" class="w-full text-xs border border-slate-300 rounded-lg p-2 font-mono">
                            </div>
                            <div>
                                <label class="block text-xs font-semibold text-slate-700 mb-1"><i class="fa-solid fa-phone mr-1 text-slate-400"></i> Phone Number</label>
                                <input type="text" id="topbar-edit-phone" value="+91 98765 43210" class="w-full text-xs border border-slate-300 rounded-lg p-2 font-mono">
                            </div>
                        </div>

                        <!-- Notice / Announcement Bar -->
                        <div class="border border-slate-200 rounded-lg p-3 bg-slate-50/50 space-y-2">
                            <div class="flex items-center justify-between">
                                <label class="text-xs font-bold text-slate-700"><i class="fa-solid fa-bullhorn mr-1 text-blue-500"></i> Center Notice / Announcement</label>
                                <label class="flex items-center text-xs text-slate-600 space-x-1 cursor-pointer">
                                    <input type="checkbox" id="topbar-edit-notice-enabled" checked class="rounded border-slate-300 text-blue-600">
                                    <span>Enabled</span>
                                </label>
                            </div>
                            <div class="grid grid-cols-3 gap-2">
                                <div>
                                    <label class="block text-[11px] text-slate-500 mb-0.5">Badge Text</label>
                                    <input type="text" id="topbar-edit-notice-badge" value="Notice" class="w-full text-xs border border-slate-300 rounded p-1.5">
                                </div>
                                <div class="col-span-2">
                                    <label class="block text-[11px] text-slate-500 mb-0.5">Notice Headline / Text</label>
                                    <input type="text" id="topbar-edit-notice-text" value="Transforming Ideas Into Digital Reality" class="w-full text-xs border border-slate-300 rounded p-1.5">
                                </div>
                            </div>
                            <div>
                                <label class="block text-[11px] text-slate-500 mb-0.5">Destination URL (optional)</label>
                                <input type="text" id="topbar-edit-notice-url" value="/contact" class="w-full text-xs border border-slate-300 rounded p-1.5 font-mono">
                            </div>
                        </div>

                        <!-- CTA Button -->
                        <div class="border border-slate-200 rounded-lg p-3 bg-slate-50/50 space-y-2">
                            <div class="flex items-center justify-between">
                                <label class="text-xs font-bold text-slate-700"><i class="fa-solid fa-arrow-up-right-from-square mr-1 text-emerald-500"></i> Right Action / CTA Button</label>
                                <label class="flex items-center text-xs text-slate-600 space-x-1 cursor-pointer">
                                    <input type="checkbox" id="topbar-edit-cta-enabled" checked class="rounded border-slate-300 text-blue-600">
                                    <span>Enabled</span>
                                </label>
                            </div>
                            <div class="grid grid-cols-2 gap-2">
                                <div>
                                    <label class="block text-[11px] text-slate-500 mb-0.5">Button Label</label>
                                    <input type="text" id="topbar-edit-cta-label" value="Get a Quote" class="w-full text-xs border border-slate-300 rounded p-1.5">
                                </div>
                                <div>
                                    <label class="block text-[11px] text-slate-500 mb-0.5">Button Destination URL</label>
                                    <input type="text" id="topbar-edit-cta-url" value="/contact" class="w-full text-xs border border-slate-300 rounded p-1.5 font-mono">
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="grid grid-cols-3 gap-2">
                        <button onclick="testUpdateTopbar()" class="col-span-2 bg-blue-600 hover:bg-blue-700 text-white font-medium text-xs py-2.5 rounded-lg transition shadow-sm flex items-center justify-center space-x-1">
                            <i class="fa-solid fa-floppy-disk mr-1"></i>
                            <span>Save Topbar Changes (PUT /api/admin/topbar)</span>
                        </button>
                        <button onclick="testDeleteTopbar()" class="bg-red-50 hover:bg-red-100 text-red-600 border border-red-200 font-medium text-xs py-2.5 rounded-lg transition">
                            <i class="fa-solid fa-trash-can mr-1"></i> Delete / Reset
                        </button>
                    </div>
                </div>

                <!-- 3. Topbar Social Links & Items Manager -->
                <div class="bg-white rounded-xl border border-slate-200 shadow-sm p-4">
                    <div class="flex items-center justify-between mb-3">
                        <div class="flex items-center space-x-2">
                            <span class="method-badge badge-get">GET</span>
                            <span class="method-badge badge-post">POST</span>
                            <span class="method-badge badge-del">DELETE</span>
                            <span class="font-mono text-sm font-semibold text-slate-800">/api/admin/topbar/items</span>
                        </div>
                        <button onclick="testFetchTopbarItems()" class="text-xs bg-slate-100 hover:bg-slate-200 text-slate-700 px-2.5 py-1 rounded-md transition font-medium">
                            <i class="fa-solid fa-rotate mr-1"></i> Fetch Items
                        </button>
                    </div>
                    <p class="text-xs text-slate-600 mb-4">Add, edit, toggle show/hide, or delete social media links & custom links in Topbar.</p>

                    <!-- Add New Link Form -->
                    <div class="border border-blue-200 bg-blue-50/40 rounded-xl p-3 mb-4 space-y-2">
                        <h4 class="text-xs font-bold text-blue-900 flex items-center">
                            <i class="fa-solid fa-plus-circle mr-1.5 text-blue-600"></i> Add New Topbar Link / Social
                        </h4>
                        <div class="grid grid-cols-1 md:grid-cols-4 gap-2">
                            <div>
                                <label class="block text-[11px] text-slate-600 mb-1">Platform</label>
                                <select id="topbar-add-platform" onchange="topbarPlatformChanged()" class="w-full text-xs border border-slate-300 rounded-lg p-2 bg-white">
                                    <option value="facebook">Facebook</option>
                                    <option value="twitter">Twitter / X</option>
                                    <option value="instagram">Instagram</option>
                                    <option value="linkedin">LinkedIn</option>
                                    <option value="youtube">YouTube</option>
                                    <option value="github">GitHub</option>
                                    <option value="whatsapp">WhatsApp</option>
                                    <option value="custom">Custom Link</option>
                                </select>
                            </div>
                            <div>
                                <label class="block text-[11px] text-slate-600 mb-1">Label / Title</label>
                                <input type="text" id="topbar-add-label" value="Facebook" class="w-full text-xs border border-slate-300 rounded-lg p-2 bg-white">
                            </div>
                            <div class="md:col-span-2">
                                <label class="block text-[11px] text-slate-600 mb-1">Destination URL</label>
                                <div class="flex space-x-2">
                                    <input type="text" id="topbar-add-url" value="https://facebook.com/nexgen" class="flex-1 text-xs border border-slate-300 rounded-lg p-2 bg-white font-mono">
                                    <button onclick="testAddTopbarItem()" class="bg-blue-600 hover:bg-blue-700 text-white font-semibold text-xs px-4 py-2 rounded-lg transition whitespace-nowrap">
                                        <i class="fa-solid fa-plus mr-1"></i> Add
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Manage Existing Link by ID -->
                    <div class="border border-slate-200 rounded-xl p-3 space-y-2">
                        <h4 class="text-xs font-bold text-slate-700 flex items-center">
                            <i class="fa-solid fa-sliders mr-1.5 text-slate-500"></i> Manage Existing Link by ID (soc-1, soc-2, etc.)
                        </h4>
                        <div class="grid grid-cols-1 md:grid-cols-4 gap-2">
                            <div>
                                <label class="block text-[11px] text-slate-600 mb-1">Item ID / Platform</label>
                                <input type="text" id="topbar-manage-id" value="soc-1" class="w-full text-xs border border-slate-300 rounded-lg p-2 font-mono">
                            </div>
                            <div>
                                <label class="block text-[11px] text-slate-600 mb-1">New Label (optional)</label>
                                <input type="text" id="topbar-manage-label" placeholder="Leave empty to keep" class="w-full text-xs border border-slate-300 rounded-lg p-2">
                            </div>
                            <div class="md:col-span-2">
                                <label class="block text-[11px] text-slate-600 mb-1">New Destination URL</label>
                                <input type="text" id="topbar-manage-url" placeholder="https://..." class="w-full text-xs border border-slate-300 rounded-lg p-2 font-mono">
                            </div>
                        </div>
                        <div class="flex flex-wrap gap-2 pt-1">
                            <button onclick="testUpdateTopbarItem()" class="bg-indigo-600 hover:bg-indigo-700 text-white font-medium text-xs px-3 py-1.5 rounded-lg transition">
                                <i class="fa-solid fa-pen-to-square mr-1"></i> Update Link
                            </button>
                            <button onclick="testToggleTopbarItem()" class="bg-amber-600 hover:bg-amber-700 text-white font-medium text-xs px-3 py-1.5 rounded-lg transition">
                                <i class="fa-solid fa-eye-slash mr-1"></i> Toggle Show / Hide
                            </button>
                            <button onclick="testDeleteTopbarItem()" class="bg-red-600 hover:bg-red-700 text-white font-medium text-xs px-3 py-1.5 rounded-lg transition">
                                <i class="fa-solid fa-trash mr-1"></i> Delete Link
                            </button>
                        </div>
                    </div>
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
                            <i class="fa-solid fa-plus-circle text-indigo-600 mr-1"></i> Add New Navigation Link / Submenu (POST)
                        </span>
                        <div class="grid grid-cols-3 gap-2 mb-2">
                            <div>
                                <label class="text-[11px] font-medium text-slate-600 block mb-0.5">Link Name *</label>
                                <input type="text" id="nav-item-add-label" placeholder="e.g. Pricing, Docs, Career" value="Pricing" class="w-full text-xs border border-slate-300 rounded p-1.5 outline-none">
                            </div>
                            <div>
                                <label class="text-[11px] font-medium text-slate-600 block mb-0.5">URL Path *</label>
                                <input type="text" id="nav-item-add-url" placeholder="e.g. /pricing, /docs" value="/pricing" class="w-full text-xs border border-slate-300 rounded p-1.5 outline-none font-mono">
                            </div>
                            <div>
                                <label class="text-[11px] font-medium text-slate-600 block mb-0.5">Parent ID (for Submenu)</label>
                                <input type="text" id="nav-item-add-parent" placeholder="Empty = Root, or e.g. nav-3" class="w-full text-xs border border-slate-300 rounded p-1.5 outline-none font-mono">
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
                            <i class="fa-solid fa-pen-to-square text-indigo-600 mr-1"></i> Edit, Show/Hide, Reorder &amp; Delete Link (Any Level)
                        </span>
                        <div class="grid grid-cols-3 gap-2 mb-2">
                            <div>
                                <label class="text-[11px] font-medium text-slate-600 block mb-0.5">Item ID *</label>
                                <input type="text" id="nav-item-edit-id" value="nav-1" placeholder="e.g. nav-1 or sub-id" class="w-full text-xs border border-slate-300 rounded p-1.5 outline-none font-mono">
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

                <!-- 5. Multi-Layer Submenu Hierarchy Builder (PUT /api/admin/navigation/navbar/items) -->
                <div class="bg-white rounded-xl border border-indigo-200 shadow-sm p-4">
                    <div class="flex items-center justify-between mb-3">
                        <div class="flex items-center space-x-2">
                            <span class="method-badge badge-put">PUT</span>
                            <span class="font-mono text-sm font-semibold text-indigo-900">/api/admin/navigation/navbar/items</span>
                        </div>
                        <span class="text-xs bg-indigo-100 text-indigo-700 px-2 py-0.5 rounded font-bold">Multi-Layer Submenus</span>
                    </div>
                    <p class="text-xs text-slate-600 mb-2">Configure multi-layer navigation menus (Submenus, Sub-submenus of arbitrary depth). Children can be placed under <code>submenu</code>, <code>children</code>, or <code>subitems</code>.</p>
                    <textarea id="navbar-multi-tree-json" rows="12" class="w-full text-xs font-mono border border-slate-200 rounded p-2 mb-3 outline-none">{
  "items": [
    { "id": "nav-1", "label": "Home", "url": "/", "enabled": true },
    { "id": "nav-2", "label": "About", "url": "/about", "enabled": true },
    {
      "id": "nav-3",
      "label": "Services",
      "url": "/services",
      "enabled": true,
      "submenu": [
        {
          "id": "nav-8",
          "label": "Cloud Solutions",
          "url": "/services/cloud",
          "enabled": true,
          "submenu": [
            { "id": "nav-9", "label": "AWS Infrastructure", "url": "/services/cloud/aws", "enabled": true },
            { "id": "nav-10", "label": "Google Cloud Architecture", "url": "/services/cloud/gcp", "enabled": true },
            { "id": "nav-11", "label": "Azure Enterprise", "url": "/services/cloud/azure", "enabled": true }
          ]
        },
        {
          "id": "nav-12",
          "label": "AI & Machine Learning",
          "url": "/services/ai",
          "enabled": true,
          "submenu": [
            { "id": "nav-13", "label": "LLM Orchestration", "url": "/services/ai/llm", "enabled": true },
            { "id": "nav-14", "label": "Vector Search & RAG", "url": "/services/ai/rag", "enabled": true }
          ]
        },
        { "id": "nav-15", "label": "Cybersecurity", "url": "/services/security", "enabled": true }
      ]
    },
    { "id": "nav-4", "label": "Portfolio", "url": "/portfolio", "enabled": true },
    { "id": "nav-5", "label": "Blog", "url": "/blog", "enabled": true },
    { "id": "nav-6", "label": "Careers", "url": "/careers", "enabled": true },
    { "id": "nav-7", "label": "Contact", "url": "/contact", "enabled": true }
  ]
}</textarea>
                    <button onclick="testBulkSaveNavbarTree()" class="w-full bg-indigo-600 hover:bg-indigo-700 text-white font-medium text-xs py-2.5 rounded-lg transition shadow-sm flex items-center justify-center space-x-1">
                        <i class="fa-solid fa-sitemap mr-1"></i>
                        <span>Save Multi-Layer Menu Tree</span>
                    </button>
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
            <!-- ABOUT PAGE MODULE APIS & CONTENT CRUD -->
            <!-- ══════════════════════════════════════════════════════════ -->
            <div id="section-about" class="space-y-4 hidden">
                <!-- 1. Layout Switching -->
                <div class="bg-white rounded-xl border border-slate-200 shadow-sm p-4">
                    <div class="flex items-center justify-between mb-3">
                        <div class="flex items-center space-x-2">
                            <span class="method-badge badge-put">PUT</span>
                            <span class="font-mono text-sm font-semibold text-slate-800">/api/admin/layouts/aboutPageLayout</span>
                        </div>
                        <span class="text-xs bg-slate-100 text-slate-600 px-2 py-0.5 rounded font-medium">Auth Required</span>
                    </div>
                    <p class="text-xs text-slate-600 mb-3">Switch About page template: layout1 (Classic Story), layout2 (Modern Timeline), layout3 (Bento Grid), layout4 (Split Editorial), layout5 (Minimal Corporate).</p>
                    <div class="grid grid-cols-2 gap-2 mb-3">
                        <div>
                            <label class="text-[10px] text-slate-400 font-medium">Template (layout1 - layout5)</label>
                            <select id="about-layout-select" class="w-full text-xs border border-slate-200 rounded p-1.5 outline-none bg-white">
                                <option value="layout1">layout1: Classic Story & Team Focus</option>
                                <option value="layout2">layout2: Modern Timeline & Mission</option>
                                <option value="layout3">layout3: Bento Grid with Core Values</option>
                                <option value="layout4">layout4: Split Visual Editorial</option>
                                <option value="layout5">layout5: Minimal Corporate Overview</option>
                            </select>
                        </div>
                        <div class="flex items-end">
                            <button onclick="testUpdateAboutLayout()" class="w-full bg-sky-600 hover:bg-sky-700 text-white font-medium text-xs py-2 rounded-lg transition shadow-sm">
                                Save About Layout
                            </button>
                        </div>
                    </div>
                </div>

                <!-- 2. Read Endpoints (Public & Admin) -->
                <div class="bg-white rounded-xl border border-slate-200 shadow-sm p-4">
                    <div class="flex items-center justify-between mb-3">
                        <div class="flex items-center space-x-2">
                            <span class="method-badge badge-get">GET</span>
                            <span class="font-mono text-sm font-semibold text-slate-800">/api/public/about</span>
                        </div>
                        <span class="text-xs text-slate-400">Public (No Auth)</span>
                    </div>
                    <p class="text-xs text-slate-600 mb-3">Fetch complete About page: story, mission, vision, values, milestones, stats, and structured image ALT attributes.</p>
                    <div class="grid grid-cols-2 gap-2">
                        <button onclick="testPublicAbout()" class="bg-sky-600 hover:bg-sky-700 text-white font-medium text-xs py-2 rounded-lg transition shadow-sm flex items-center justify-center space-x-1">
                            <i class="fa-solid fa-globe mr-1"></i> Public /api/public/about
                        </button>
                        <button onclick="testAdminAbout()" class="bg-slate-800 hover:bg-slate-900 text-white font-medium text-xs py-2 rounded-lg transition shadow-sm flex items-center justify-center space-x-1">
                            <i class="fa-solid fa-lock mr-1"></i> Admin /api/admin/about
                        </button>
                    </div>
                </div>

                <!-- 3. Update About Content & Stories -->
                <div class="bg-white rounded-xl border border-slate-200 shadow-sm p-4">
                    <div class="flex items-center justify-between mb-3">
                        <div class="flex items-center space-x-2">
                            <span class="method-badge badge-put">PUT</span>
                            <span class="font-mono text-sm font-semibold text-slate-800">/api/admin/about</span>
                        </div>
                        <span class="text-xs bg-slate-100 text-slate-600 px-2 py-0.5 rounded font-medium">Auth Required</span>
                    </div>
                    <p class="text-xs text-slate-600 mb-3">Update company introduction, headline, mission, and vision.</p>
                    <div class="space-y-2 mb-3">
                        <div class="grid grid-cols-2 gap-2">
                            <div>
                                <label class="text-xs font-medium text-slate-600">Badge</label>
                                <input id="about-badge" type="text" value="About Us" class="w-full text-xs border border-slate-200 rounded p-1.5 mt-0.5 outline-none">
                            </div>
                            <div>
                                <label class="text-xs font-medium text-slate-600">Title</label>
                                <input id="about-title" type="text" value="Driving Innovation & Sustainable Digital Growth" class="w-full text-xs border border-slate-200 rounded p-1.5 mt-0.5 outline-none">
                            </div>
                        </div>
                        <div>
                            <label class="text-xs font-medium text-slate-600">Tagline / Subtitle</label>
                            <input id="about-tagline" type="text" value="Empowering forward-thinking enterprises with modern cloud architecture and reliable engineering." class="w-full text-xs border border-slate-200 rounded p-1.5 mt-0.5 outline-none">
                        </div>
                        <div>
                            <label class="text-xs font-medium text-slate-600">Story Title</label>
                            <input id="about-story-title" type="text" value="A Decade of Engineering Excellence" class="w-full text-xs border border-slate-200 rounded p-1.5 mt-0.5 outline-none">
                        </div>
                        <div>
                            <label class="text-xs font-medium text-slate-600">Story Content</label>
                            <textarea id="about-story-content" rows="3" class="w-full text-xs border border-slate-200 rounded p-1.5 mt-0.5 outline-none">Founded with a clear vision to bridge complex technical challenges with elegant digital experiences, our team combines deep software engineering expertise with design-thinking to craft solutions that scale effortlessly.</textarea>
                        </div>
                        <div class="grid grid-cols-2 gap-2">
                            <div>
                                <label class="text-xs font-medium text-slate-600">Mission Statement</label>
                                <textarea id="about-mission" rows="2" class="w-full text-xs border border-slate-200 rounded p-1.5 mt-0.5 outline-none">To build transformative, secure, and scalable digital solutions that empower global businesses to succeed in an ever-evolving world.</textarea>
                            </div>
                            <div>
                                <label class="text-xs font-medium text-slate-600">Vision Statement</label>
                                <textarea id="about-vision" rows="2" class="w-full text-xs border border-slate-200 rounded p-1.5 mt-0.5 outline-none">To be the most trusted technology partner recognized globally for human-centric design, engineering integrity, and innovation.</textarea>
                            </div>
                        </div>
                    </div>
                    <button onclick="testUpdateAbout()" class="w-full bg-sky-600 hover:bg-sky-700 text-white font-medium text-xs py-2 rounded-lg transition shadow-sm flex items-center justify-center space-x-1">
                        <i class="fa-solid fa-floppy-disk mr-1"></i> Save About Page Content
                    </button>
                </div>

                <!-- 4. Image URLs & Proper ALT Attributes -->
                <div class="bg-white rounded-xl border border-slate-200 shadow-sm p-4">
                    <div class="flex items-center justify-between mb-3">
                        <div class="flex items-center space-x-2">
                            <span class="method-badge badge-put">PUT</span>
                            <span class="font-mono text-sm font-semibold text-slate-800">/api/admin/about/images</span>
                        </div>
                        <span class="text-xs bg-purple-100 text-purple-700 px-2 py-0.5 rounded font-bold">Image ALT Control</span>
                    </div>
                    <p class="text-xs text-slate-600 mb-3">Manage About page visual assets and configure proper ALT attributes for accessibility and SEO imageAltMap.</p>
                    <div class="space-y-3 mb-3">
                        <div class="p-3 bg-slate-50 rounded-lg border border-slate-100 space-y-2">
                            <p class="text-xs font-bold text-slate-700"><i class="fa-solid fa-image text-sky-500 mr-1"></i> Hero Image Asset</p>
                            <div>
                                <label class="text-[11px] font-medium text-slate-500">Hero Image URL</label>
                                <input id="about-hero-img" type="text" value="https://images.unsplash.com/photo-1522071820081-009f0129c71c?auto=format&fit=crop&w=1200&q=80" class="w-full text-xs border border-slate-200 rounded p-1.5 mt-0.5 outline-none bg-white">
                            </div>
                            <div>
                                <label class="text-[11px] font-medium text-emerald-700">Hero Image ALT Attribute (Screen readers & SEO)</label>
                                <input id="about-hero-alt" type="text" value="NexGen engineering team collaborating on product architecture in a high-tech conference room" class="w-full text-xs border border-emerald-300 rounded p-1.5 mt-0.5 outline-none bg-emerald-50 text-emerald-900 font-medium">
                            </div>
                        </div>

                        <div class="p-3 bg-slate-50 rounded-lg border border-slate-100 space-y-2">
                            <p class="text-xs font-bold text-slate-700"><i class="fa-solid fa-image text-sky-500 mr-1"></i> Story Image Asset</p>
                            <div>
                                <label class="text-[11px] font-medium text-slate-500">Story Image URL</label>
                                <input id="about-story-img" type="text" value="https://images.unsplash.com/photo-1553877522-43269d4ea984?auto=format&fit=crop&w=1000&q=80" class="w-full text-xs border border-slate-200 rounded p-1.5 mt-0.5 outline-none bg-white">
                            </div>
                            <div>
                                <label class="text-[11px] font-medium text-emerald-700">Story Image ALT Attribute (Screen readers & SEO)</label>
                                <input id="about-story-alt" type="text" value="Software engineers reviewing system designs and strategic roadmaps on digital displays" class="w-full text-xs border border-emerald-300 rounded p-1.5 mt-0.5 outline-none bg-emerald-50 text-emerald-900 font-medium">
                            </div>
                        </div>
                    </div>
                    <div class="grid grid-cols-2 gap-2">
                        <button onclick="testUpdateAboutImages()" class="bg-emerald-600 hover:bg-emerald-700 text-white font-medium text-xs py-2 rounded-lg transition shadow-sm flex items-center justify-center space-x-1">
                            <i class="fa-solid fa-tags mr-1"></i> Save Image URLs & ALTs
                        </button>
                        <button onclick="testResetAbout()" class="bg-slate-700 hover:bg-slate-800 text-white font-medium text-xs py-2 rounded-lg transition shadow-sm flex items-center justify-center space-x-1">
                            <i class="fa-solid fa-rotate-left mr-1"></i> Reset to Defaults
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
            <!-- 2.8 TEAM MODULE APIS -->
            <!-- ══════════════════════════════════════════════════════════ -->
            <div id="section-team" class="space-y-4 hidden">
                <!-- 1. GET /api/public/team -->
                <div class="bg-white rounded-xl border border-slate-200 shadow-sm p-4">
                    <div class="flex items-center justify-between mb-3">
                        <div class="flex items-center space-x-2">
                            <span class="method-badge badge-get">GET</span>
                            <span class="font-mono text-sm font-semibold text-slate-800">/api/public/team</span>
                        </div>
                        <span class="text-xs text-slate-400">Public (No Auth)</span>
                    </div>
                    <p class="text-xs text-slate-600 mb-3">Public members list (supports ?department=, ?isLeadership=1|0, ?limit=). Returns published team members sorted by order.</p>
                    <div class="grid grid-cols-3 gap-2 mb-3">
                        <div>
                            <label class="text-[10px] text-slate-400 font-medium">Department</label>
                            <select id="team-pub-dept" class="w-full text-xs border border-slate-200 rounded p-1.5 outline-none bg-white">
                                <option value="" selected>All Departments</option>
                                <option value="Management">Management</option>
                                <option value="Engineering">Engineering</option>
                                <option value="Design">Design</option>
                                <option value="Marketing">Marketing</option>
                            </select>
                        </div>
                        <div>
                            <label class="text-[10px] text-slate-400 font-medium">Leadership</label>
                            <select id="team-pub-leadership" class="w-full text-xs border border-slate-200 rounded p-1.5 outline-none bg-white">
                                <option value="" selected>All Members</option>
                                <option value="1">Leadership Only</option>
                                <option value="0">Non-Leadership</option>
                            </select>
                        </div>
                        <div>
                            <label class="text-[10px] text-slate-400 font-medium">Limit</label>
                            <input id="team-pub-limit" type="number" placeholder="Limit" value="20" class="w-full text-xs border border-slate-200 rounded p-1.5 outline-none">
                        </div>
                    </div>
                    <button onclick="testPublicTeam()" class="w-full bg-teal-600 hover:bg-teal-700 text-white font-medium text-xs py-2.5 rounded-lg transition shadow-sm flex items-center justify-center space-x-2">
                        <i class="fa-solid fa-users"></i>
                        <span>Fetch Public Team List</span>
                    </button>
                </div>

                <!-- 2. GET /api/admin/team & /api/admin/team/:id -->
                <div class="bg-white rounded-xl border border-slate-200 shadow-sm p-4">
                    <div class="flex items-center justify-between mb-3">
                        <div class="flex items-center space-x-2">
                            <span class="method-badge badge-get">GET</span>
                            <span class="font-mono text-sm font-semibold text-slate-800">/api/admin/team</span>
                        </div>
                        <span class="text-xs bg-slate-100 text-slate-600 px-2 py-0.5 rounded font-medium">Auth Required</span>
                    </div>
                    <p class="text-xs text-slate-600 mb-3">Admin list of all members (including drafts and hidden members) or single member by ID.</p>
                    <div class="grid grid-cols-2 gap-2 mb-3">
                        <button onclick="testAdminTeam()" class="bg-slate-800 hover:bg-slate-900 text-white font-medium text-xs py-2 rounded-lg transition">
                            Fetch All Admin Team
                        </button>
                        <div class="flex space-x-1">
                            <input id="team-admin-fetch-id" type="number" value="1" placeholder="ID" class="w-16 text-xs border border-slate-200 rounded p-1.5 outline-none font-mono">
                            <button onclick="testAdminTeamSingle()" class="flex-1 bg-teal-700 hover:bg-teal-800 text-white font-medium text-xs py-2 rounded-lg transition">
                                Fetch By ID
                            </button>
                        </div>
                    </div>
                </div>

                <!-- 3. POST /api/admin/team (Create) -->
                <div class="bg-white rounded-xl border border-slate-200 shadow-sm p-4">
                    <div class="flex items-center justify-between mb-3">
                        <div class="flex items-center space-x-2">
                            <span class="method-badge badge-post">POST</span>
                            <span class="font-mono text-sm font-semibold text-slate-800">/api/admin/team</span>
                        </div>
                        <span class="text-xs bg-slate-100 text-slate-600 px-2 py-0.5 rounded font-medium">Auth Required</span>
                    </div>
                    <p class="text-xs text-slate-600 mb-3">Add team member (name, designation, department, bio, social links, isLeadership, order).</p>
                    <div class="space-y-2 mb-3">
                        <div class="grid grid-cols-3 gap-2">
                            <div class="col-span-2">
                                <label class="text-xs font-medium text-slate-600">Full Name *</label>
                                <input id="team-create-name" type="text" value="Dr. Arvind Sharma" class="w-full text-xs border border-slate-200 rounded p-1.5 mt-0.5 outline-none">
                            </div>
                            <div>
                                <label class="text-xs font-medium text-slate-600">Department</label>
                                <select id="team-create-dept" class="w-full text-xs border border-slate-200 rounded p-1.5 mt-0.5 outline-none bg-white">
                                    <option value="Management">Management</option>
                                    <option value="Engineering" selected>Engineering</option>
                                    <option value="Design">Design</option>
                                    <option value="Marketing">Marketing</option>
                                </select>
                            </div>
                        </div>
                        <div class="grid grid-cols-3 gap-2">
                            <div class="col-span-2">
                                <label class="text-xs font-medium text-slate-600">Designation *</label>
                                <input id="team-create-desig" type="text" value="Principal AI Architect" class="w-full text-xs border border-slate-200 rounded p-1.5 mt-0.5 outline-none">
                            </div>
                            <div>
                                <label class="text-xs font-medium text-slate-600">Leadership Spotlight?</label>
                                <select id="team-create-leadership" class="w-full text-xs border border-slate-200 rounded p-1.5 mt-0.5 outline-none bg-white">
                                    <option value="0">No</option>
                                    <option value="1" selected>Yes</option>
                                </select>
                            </div>
                        </div>
                        <div>
                            <label class="text-xs font-medium text-slate-600">Photo URL</label>
                            <input id="team-create-photo" type="url" value="https://images.unsplash.com/photo-1534528741775-53994a69daeb?w=800" class="w-full text-xs border border-slate-200 rounded p-1.5 mt-0.5 outline-none font-mono">
                        </div>
                        <div>
                            <label class="text-xs font-medium text-slate-600">Bio</label>
                            <textarea id="team-create-bio" rows="2" class="w-full text-xs border border-slate-200 rounded p-1.5 mt-0.5 outline-none">Pioneering neural search architectures and LLM distillation models for enterprise software systems.</textarea>
                        </div>
                        <div class="grid grid-cols-3 gap-2">
                            <div>
                                <label class="text-xs font-medium text-slate-600">LinkedIn URL</label>
                                <input id="team-create-linkedin" type="url" value="https://linkedin.com/in/arvindsharma" class="w-full text-xs border border-slate-200 rounded p-1.5 mt-0.5 outline-none font-mono">
                            </div>
                            <div>
                                <label class="text-xs font-medium text-slate-600">Twitter URL</label>
                                <input id="team-create-twitter" type="url" value="https://x.com/arvindsharma" class="w-full text-xs border border-slate-200 rounded p-1.5 mt-0.5 outline-none font-mono">
                            </div>
                            <div>
                                <label class="text-xs font-medium text-slate-600">GitHub URL</label>
                                <input id="team-create-github" type="url" value="https://github.com/arvindsharma" class="w-full text-xs border border-slate-200 rounded p-1.5 mt-0.5 outline-none font-mono">
                            </div>
                        </div>
                        <div class="grid grid-cols-2 gap-2">
                            <div>
                                <label class="text-xs font-medium text-slate-600">Display Order</label>
                                <input id="team-create-order" type="number" value="9" class="w-full text-xs border border-slate-200 rounded p-1.5 mt-0.5 outline-none">
                            </div>
                            <div>
                                <label class="text-xs font-medium text-slate-600">Status</label>
                                <select id="team-create-published" class="w-full text-xs border border-slate-200 rounded p-1.5 mt-0.5 outline-none bg-white">
                                    <option value="1" selected>Published</option>
                                    <option value="0">Draft</option>
                                </select>
                            </div>
                        </div>
                    </div>
                    <button onclick="testCreateTeamMember()" class="w-full bg-teal-600 hover:bg-teal-700 text-white font-medium text-xs py-2 rounded-lg transition shadow-sm">
                        Add Team Member
                    </button>
                </div>

                <!-- 4. PUT & DELETE /api/admin/team/:id -->
                <div class="bg-white rounded-xl border border-slate-200 shadow-sm p-4">
                    <div class="flex items-center justify-between mb-3">
                        <div class="flex items-center space-x-2">
                            <span class="method-badge badge-put">PUT</span>
                            <span class="method-badge badge-del">DELETE</span>
                            <span class="font-mono text-sm font-semibold text-slate-800">/api/admin/team/:id</span>
                        </div>
                        <span class="text-xs bg-slate-100 text-slate-600 px-2 py-0.5 rounded font-medium">Auth Required</span>
                    </div>
                    <div class="grid grid-cols-3 gap-2 mb-3">
                        <div>
                            <label class="text-xs font-medium text-slate-600">Member ID</label>
                            <input id="team-manage-id" type="number" value="1" class="w-full text-xs border border-slate-200 rounded p-1.5 mt-0.5 outline-none font-mono">
                        </div>
                        <div class="col-span-2">
                            <label class="text-xs font-medium text-slate-600">Updated Designation</label>
                            <input id="team-manage-desig" type="text" value="Founder & Chief Executive Officer" class="w-full text-xs border border-slate-200 rounded p-1.5 mt-0.5 outline-none">
                        </div>
                    </div>
                    <div class="grid grid-cols-2 gap-2">
                        <button onclick="testUpdateTeamMember()" class="bg-slate-800 hover:bg-slate-900 text-white font-medium text-xs py-2 rounded-lg transition">
                            Update Member
                        </button>
                        <button onclick="testDeleteTeamMember()" class="bg-red-600 hover:bg-red-700 text-white font-medium text-xs py-2 rounded-lg transition">
                            Delete Member
                        </button>
                    </div>
                </div>
            </div>


            <!-- ══════════════════════════════════════════════════════════ -->
            <!-- 2.9 TESTIMONIALS MODULE APIS -->
            <!-- ══════════════════════════════════════════════════════════ -->
            <div id="section-testimonials" class="space-y-4 hidden">
                <!-- 1. GET /api/public/testimonials -->
                <div class="bg-white rounded-xl border border-slate-200 shadow-sm p-4">
                    <div class="flex items-center justify-between mb-3">
                        <div class="flex items-center space-x-2">
                            <span class="method-badge badge-get">GET</span>
                            <span class="font-mono text-sm font-semibold text-slate-800">/api/public/testimonials</span>
                        </div>
                        <span class="text-xs text-slate-400">Public (No Auth)</span>
                    </div>
                    <p class="text-xs text-slate-600 mb-3">Public reviews feed (supports ?featured=1|0, ?rating=1..5, ?limit=). Returns published reviews sorted by order.</p>
                    <div class="grid grid-cols-3 gap-2 mb-3">
                        <div>
                            <label class="text-[10px] text-slate-400 font-medium">Featured Only?</label>
                            <select id="testim-pub-feat" class="w-full text-xs border border-slate-200 rounded p-1.5 outline-none bg-white">
                                <option value="" selected>All Reviews</option>
                                <option value="1">Featured Only</option>
                                <option value="0">Standard Only</option>
                            </select>
                        </div>
                        <div>
                            <label class="text-[10px] text-slate-400 font-medium">Rating</label>
                            <select id="testim-pub-rating" class="w-full text-xs border border-slate-200 rounded p-1.5 outline-none bg-white">
                                <option value="" selected>Any Rating</option>
                                <option value="5">5 Stars only</option>
                                <option value="4">4 Stars only</option>
                                <option value="3">3 Stars only</option>
                            </select>
                        </div>
                        <div>
                            <label class="text-[10px] text-slate-400 font-medium">Limit</label>
                            <input id="testim-pub-limit" type="number" placeholder="Limit" value="20" class="w-full text-xs border border-slate-200 rounded p-1.5 outline-none">
                        </div>
                    </div>
                    <button onclick="testPublicTestimonials()" class="w-full bg-amber-600 hover:bg-amber-700 text-white font-medium text-xs py-2.5 rounded-lg transition shadow-sm flex items-center justify-center space-x-2">
                        <i class="fa-solid fa-star"></i>
                        <span>Fetch Public Testimonials</span>
                    </button>
                </div>

                <!-- 2. GET /api/admin/testimonials & /api/admin/testimonials/:id -->
                <div class="bg-white rounded-xl border border-slate-200 shadow-sm p-4">
                    <div class="flex items-center justify-between mb-3">
                        <div class="flex items-center space-x-2">
                            <span class="method-badge badge-get">GET</span>
                            <span class="font-mono text-sm font-semibold text-slate-800">/api/admin/testimonials</span>
                        </div>
                        <span class="text-xs bg-slate-100 text-slate-600 px-2 py-0.5 rounded font-medium">Auth Required</span>
                    </div>
                    <p class="text-xs text-slate-600 mb-3">Admin list of all reviews (including drafts and unpublished) or single review by ID.</p>
                    <div class="grid grid-cols-2 gap-2 mb-3">
                        <button onclick="testAdminTestimonials()" class="bg-slate-800 hover:bg-slate-900 text-white font-medium text-xs py-2 rounded-lg transition">
                            Fetch All Admin Reviews
                        </button>
                        <div class="flex space-x-1">
                            <input id="testim-admin-fetch-id" type="number" value="1" placeholder="ID" class="w-16 text-xs border border-slate-200 rounded p-1.5 outline-none font-mono">
                            <button onclick="testAdminTestimonialSingle()" class="flex-1 bg-amber-700 hover:bg-amber-800 text-white font-medium text-xs py-2 rounded-lg transition">
                                Fetch By ID
                            </button>
                        </div>
                    </div>
                </div>

                <!-- 3. POST /api/admin/testimonials (Create) -->
                <div class="bg-white rounded-xl border border-slate-200 shadow-sm p-4">
                    <div class="flex items-center justify-between mb-3">
                        <div class="flex items-center space-x-2">
                            <span class="method-badge badge-post">POST</span>
                            <span class="font-mono text-sm font-semibold text-slate-800">/api/admin/testimonials</span>
                        </div>
                        <span class="text-xs bg-slate-100 text-slate-600 px-2 py-0.5 rounded font-medium">Auth Required</span>
                    </div>
                    <p class="text-xs text-slate-600 mb-3">Add review (author name, designation, company, rating 1-5, quote, videoUrl, metric, order).</p>
                    <div class="space-y-2 mb-3">
                        <div class="grid grid-cols-3 gap-2">
                            <div>
                                <label class="text-xs font-medium text-slate-600">Client Name *</label>
                                <input id="testim-create-name" type="text" value="Anil Deshmukh" class="w-full text-xs border border-slate-200 rounded p-1.5 mt-0.5 outline-none">
                            </div>
                            <div>
                                <label class="text-xs font-medium text-slate-600">Designation</label>
                                <input id="testim-create-desig" type="text" value="Chief Digital Officer" class="w-full text-xs border border-slate-200 rounded p-1.5 mt-0.5 outline-none">
                            </div>
                            <div>
                                <label class="text-xs font-medium text-slate-600">Company</label>
                                <input id="testim-create-company" type="text" value="FinEdge Global" class="w-full text-xs border border-slate-200 rounded p-1.5 mt-0.5 outline-none">
                            </div>
                        </div>
                        <div class="grid grid-cols-3 gap-2">
                            <div>
                                <label class="text-xs font-medium text-slate-600">Rating (1 to 5) *</label>
                                <select id="testim-create-rating" class="w-full text-xs border border-slate-200 rounded p-1.5 mt-0.5 outline-none bg-white">
                                    <option value="5" selected>5 Stars (★★★★★)</option>
                                    <option value="4">4 Stars (★★★★☆)</option>
                                    <option value="3">3 Stars (★★★☆☆)</option>
                                    <option value="2">2 Stars (★★☆☆☆)</option>
                                    <option value="1">1 Star (★☆☆☆☆)</option>
                                </select>
                            </div>
                            <div>
                                <label class="text-xs font-medium text-slate-600">Featured?</label>
                                <select id="testim-create-feat" class="w-full text-xs border border-slate-200 rounded p-1.5 mt-0.5 outline-none bg-white">
                                    <option value="1" selected>Yes (Featured)</option>
                                    <option value="0">No (Standard)</option>
                                </select>
                            </div>
                            <div>
                                <label class="text-xs font-medium text-slate-600">Status</label>
                                <select id="testim-create-published" class="w-full text-xs border border-slate-200 rounded p-1.5 mt-0.5 outline-none bg-white">
                                    <option value="1" selected>Published</option>
                                    <option value="0">Draft</option>
                                </select>
                            </div>
                        </div>
                        <div>
                            <label class="text-xs font-medium text-slate-600">Review Quote Text *</label>
                            <textarea id="testim-create-text" rows="2" class="w-full text-xs border border-slate-200 rounded p-1.5 mt-0.5 outline-none">Informatic built our core trading application with zero latency degradation. Outstanding engineering quality.</textarea>
                        </div>
                        <div class="grid grid-cols-2 gap-2">
                            <div>
                                <label class="text-xs font-medium text-slate-600">Client Photo URL</label>
                                <input id="testim-create-photo" type="url" value="https://images.unsplash.com/photo-1500648767791-00dcc994a43e?w=800" class="w-full text-xs border border-slate-200 rounded p-1.5 mt-0.5 outline-none font-mono">
                            </div>
                            <div>
                                <label class="text-xs font-medium text-slate-600">Video URL (optional)</label>
                                <input id="testim-create-videourl" type="url" placeholder="https://youtube.com/..." class="w-full text-xs border border-slate-200 rounded p-1.5 mt-0.5 outline-none font-mono">
                            </div>
                        </div>
                        <div class="grid grid-cols-3 gap-2">
                            <div>
                                <label class="text-xs font-medium text-slate-600">Metric Label (optional)</label>
                                <input id="testim-create-metric-label" type="text" value="System Latency" class="w-full text-xs border border-slate-200 rounded p-1.5 mt-0.5 outline-none">
                            </div>
                            <div>
                                <label class="text-xs font-medium text-slate-600">Metric Value (optional)</label>
                                <input id="testim-create-metric-val" type="text" value="-70%" class="w-full text-xs border border-slate-200 rounded p-1.5 mt-0.5 outline-none font-mono">
                            </div>
                            <div>
                                <label class="text-xs font-medium text-slate-600">Display Order</label>
                                <input id="testim-create-order" type="number" value="7" class="w-full text-xs border border-slate-200 rounded p-1.5 mt-0.5 outline-none">
                            </div>
                        </div>
                    </div>
                    <button onclick="testCreateTestimonial()" class="w-full bg-amber-600 hover:bg-amber-700 text-white font-medium text-xs py-2 rounded-lg transition shadow-sm">
                        Add Review
                    </button>
                </div>

                <!-- 4. PUT & DELETE /api/admin/testimonials/:id -->
                <div class="bg-white rounded-xl border border-slate-200 shadow-sm p-4">
                    <div class="flex items-center justify-between mb-3">
                        <div class="flex items-center space-x-2">
                            <span class="method-badge badge-put">PUT</span>
                            <span class="method-badge badge-del">DELETE</span>
                            <span class="font-mono text-sm font-semibold text-slate-800">/api/admin/testimonials/:id</span>
                        </div>
                        <span class="text-xs bg-slate-100 text-slate-600 px-2 py-0.5 rounded font-medium">Auth Required</span>
                    </div>
                    <div class="grid grid-cols-3 gap-2 mb-3">
                        <div>
                            <label class="text-xs font-medium text-slate-600">Review ID</label>
                            <input id="testim-manage-id" type="number" value="1" class="w-full text-xs border border-slate-200 rounded p-1.5 mt-0.5 outline-none font-mono">
                        </div>
                        <div>
                            <label class="text-xs font-medium text-slate-600">Rating</label>
                            <select id="testim-manage-rating" class="w-full text-xs border border-slate-200 rounded p-1.5 mt-0.5 outline-none bg-white">
                                <option value="5" selected>5 Stars</option>
                                <option value="4">4 Stars</option>
                                <option value="3">3 Stars</option>
                            </select>
                        </div>
                        <div>
                            <label class="text-xs font-medium text-slate-600">Featured?</label>
                            <select id="testim-manage-feat" class="w-full text-xs border border-slate-200 rounded p-1.5 mt-0.5 outline-none bg-white">
                                <option value="1" selected>Yes</option>
                                <option value="0">No</option>
                            </select>
                        </div>
                    </div>
                    <div class="grid grid-cols-2 gap-2">
                        <button onclick="testUpdateTestimonial()" class="bg-slate-800 hover:bg-slate-900 text-white font-medium text-xs py-2 rounded-lg transition">
                            Update Review
                        </button>
                        <button onclick="testDeleteTestimonial()" class="bg-red-600 hover:bg-red-700 text-white font-medium text-xs py-2 rounded-lg transition">
                            Delete Review
                        </button>
                    </div>
                </div>
            </div>

            <!-- ══════════════════════════════════════════════════════════ -->
            <!-- 2.10 GALLERY MODULE APIS -->
            <!-- ══════════════════════════════════════════════════════════ -->
            <div id="section-gallery" class="space-y-4 hidden">
                <!-- 1. GET /api/public/gallery -->
                <div class="bg-white rounded-xl border border-slate-200 shadow-sm p-4">
                    <div class="flex items-center justify-between mb-3">
                        <div class="flex items-center space-x-2">
                            <span class="method-badge badge-get">GET</span>
                            <span class="font-mono text-sm font-semibold text-slate-800">/api/public/gallery</span>
                        </div>
                        <span class="text-xs text-slate-400">Public (No Auth)</span>
                    </div>
                    <p class="text-xs text-slate-600 mb-3">Public albums feed (supports ?category=, ?limit=). Returns published albums with photo items.</p>
                    <div class="grid grid-cols-2 gap-2 mb-3">
                        <div>
                            <label class="text-[10px] text-slate-400 font-medium">Category</label>
                            <select id="gallery-pub-cat" class="w-full text-xs border border-slate-200 rounded p-1.5 outline-none bg-white">
                                <option value="" selected>All Categories</option>
                                <option value="Culture">Culture</option>
                                <option value="Events">Events</option>
                                <option value="Workplace">Workplace</option>
                            </select>
                        </div>
                        <div>
                            <label class="text-[10px] text-slate-400 font-medium">Limit</label>
                            <input id="gallery-pub-limit" type="number" placeholder="Limit" value="20" class="w-full text-xs border border-slate-200 rounded p-1.5 outline-none">
                        </div>
                    </div>
                    <button onclick="testPublicGallery()" class="w-full bg-indigo-600 hover:bg-indigo-700 text-white font-medium text-xs py-2.5 rounded-lg transition shadow-sm flex items-center justify-center space-x-2">
                        <i class="fa-solid fa-images"></i>
                        <span>Fetch Public Gallery Albums</span>
                    </button>
                </div>

                <!-- 2. GET /api/admin/gallery & /api/admin/gallery/:id -->
                <div class="bg-white rounded-xl border border-slate-200 shadow-sm p-4">
                    <div class="flex items-center justify-between mb-3">
                        <div class="flex items-center space-x-2">
                            <span class="method-badge badge-get">GET</span>
                            <span class="font-mono text-sm font-semibold text-slate-800">/api/admin/gallery</span>
                        </div>
                        <span class="text-xs bg-slate-100 text-slate-600 px-2 py-0.5 rounded font-medium">Auth Required</span>
                    </div>
                    <p class="text-xs text-slate-600 mb-3">Admin list of all albums (including drafts and unpublished) or single album by ID.</p>
                    <div class="grid grid-cols-2 gap-2 mb-3">
                        <button onclick="testAdminGallery()" class="bg-slate-800 hover:bg-slate-900 text-white font-medium text-xs py-2 rounded-lg transition">
                            Fetch All Admin Albums
                        </button>
                        <div class="flex space-x-1">
                            <input id="gallery-admin-fetch-id" type="number" value="1" placeholder="ID" class="w-16 text-xs border border-slate-200 rounded p-1.5 outline-none font-mono">
                            <button onclick="testAdminGallerySingle()" class="flex-1 bg-indigo-700 hover:bg-indigo-800 text-white font-medium text-xs py-2 rounded-lg transition">
                                Fetch By ID
                            </button>
                        </div>
                    </div>
                </div>

                <!-- 3. POST /api/admin/gallery (Create) -->
                <div class="bg-white rounded-xl border border-slate-200 shadow-sm p-4">
                    <div class="flex items-center justify-between mb-3">
                        <div class="flex items-center space-x-2">
                            <span class="method-badge badge-post">POST</span>
                            <span class="font-mono text-sm font-semibold text-slate-800">/api/admin/gallery</span>
                        </div>
                        <span class="text-xs bg-slate-100 text-slate-600 px-2 py-0.5 rounded font-medium">Auth Required</span>
                    </div>
                    <p class="text-xs text-slate-600 mb-3">Create album with photo items (name, category, description, coverImage, images JSON, order).</p>
                    <div class="space-y-2 mb-3">
                        <div class="grid grid-cols-3 gap-2">
                            <div class="col-span-2">
                                <label class="text-xs font-medium text-slate-600">Album Name *</label>
                                <input id="gallery-create-name" type="text" value="Community & Open Source Meetups" class="w-full text-xs border border-slate-200 rounded p-1.5 mt-0.5 outline-none">
                            </div>
                            <div>
                                <label class="text-xs font-medium text-slate-600">Category</label>
                                <input id="gallery-create-cat" type="text" value="Events" class="w-full text-xs border border-slate-200 rounded p-1.5 mt-0.5 outline-none">
                            </div>
                        </div>
                        <div>
                            <label class="text-xs font-medium text-slate-600">Description</label>
                            <input id="gallery-create-desc" type="text" value="Photos from community developer gatherings and technical workshops." class="w-full text-xs border border-slate-200 rounded p-1.5 mt-0.5 outline-none">
                        </div>
                        <div class="grid grid-cols-3 gap-2">
                            <div class="col-span-2">
                                <label class="text-xs font-medium text-slate-600">Cover Image URL</label>
                                <input id="gallery-create-cover" type="url" value="https://images.unsplash.com/photo-1511578314322-379afb476865?w=1200" class="w-full text-xs border border-slate-200 rounded p-1.5 mt-0.5 outline-none font-mono">
                            </div>
                            <div>
                                <label class="text-xs font-medium text-slate-600">Status</label>
                                <select id="gallery-create-published" class="w-full text-xs border border-slate-200 rounded p-1.5 mt-0.5 outline-none bg-white">
                                    <option value="1" selected>Published</option>
                                    <option value="0">Draft</option>
                                </select>
                            </div>
                        </div>
                        <div>
                            <label class="text-xs font-medium text-slate-600">Images Array (JSON)</label>
                            <textarea id="gallery-create-images" rows="3" class="w-full text-xs border border-slate-200 rounded p-1.5 mt-0.5 outline-none font-mono">[
  { "url": "https://images.unsplash.com/photo-1511578314322-379afb476865?w=1200", "title": "Opening Talk", "caption": "Welcoming our community builders" },
  { "url": "https://images.unsplash.com/photo-1522071820081-009f0129c71c?w=1200", "title": "Networking Session", "caption": "Developers sharing code insights" }
]</textarea>
                        </div>
                    </div>
                    <button onclick="testCreateGallery()" class="w-full bg-indigo-600 hover:bg-indigo-700 text-white font-medium text-xs py-2 rounded-lg transition shadow-sm">
                        Create Gallery Album
                    </button>
                </div>

                <!-- 4. PUT & DELETE /api/admin/gallery/:id -->
                <div class="bg-white rounded-xl border border-slate-200 shadow-sm p-4">
                    <div class="flex items-center justify-between mb-3">
                        <div class="flex items-center space-x-2">
                            <span class="method-badge badge-put">PUT</span>
                            <span class="method-badge badge-del">DELETE</span>
                            <span class="font-mono text-sm font-semibold text-slate-800">/api/admin/gallery/:id</span>
                        </div>
                        <span class="text-xs bg-slate-100 text-slate-600 px-2 py-0.5 rounded font-medium">Auth Required</span>
                    </div>
                    <div class="grid grid-cols-3 gap-2 mb-3">
                        <div>
                            <label class="text-xs font-medium text-slate-600">Album ID</label>
                            <input id="gallery-manage-id" type="number" value="1" class="w-full text-xs border border-slate-200 rounded p-1.5 mt-0.5 outline-none font-mono">
                        </div>
                        <div class="col-span-2">
                            <label class="text-xs font-medium text-slate-600">Updated Name</label>
                            <input id="gallery-manage-name" type="text" value="Office & Culture (Updated)" class="w-full text-xs border border-slate-200 rounded p-1.5 mt-0.5 outline-none">
                        </div>
                    </div>
                    <div class="grid grid-cols-2 gap-2">
                        <button onclick="testUpdateGallery()" class="bg-slate-800 hover:bg-slate-900 text-white font-medium text-xs py-2 rounded-lg transition">
                            Update Album
                        </button>
                        <button onclick="testDeleteGallery()" class="bg-red-600 hover:bg-red-700 text-white font-medium text-xs py-2 rounded-lg transition">
                            Delete Album
                        </button>
                    </div>
                </div>
            </div>

            <!-- ══════════════════════════════════════════════════════════ -->
            <!-- MODULE 11: CONTACT PAGE AND INQUIRY FORMS               -->
            <!-- ══════════════════════════════════════════════════════════ -->
            <div id="section-contact" class="space-y-4 hidden">
                <!-- Info banner -->
                <div class="bg-indigo-50 border border-indigo-200 rounded-xl p-4 text-xs text-indigo-900 leading-relaxed">
                    <p class="font-bold mb-1"><i class="fa-solid fa-envelope-open-text mr-1 text-indigo-600"></i> Module 11: Contact Page & Inquiry Forms</p>
                    <p>Includes public contact visitor submission with honeypot spam protection, 4 customizable layout templates (<code class="bg-indigo-100 px-1 py-0.5 rounded font-mono">contactPageLayout</code>), full admin inquiry management (list, view, status updates, deletion), and SEO configuration.</p>
                </div>

                <!-- 1. Layout Switching -->
                <div class="bg-white rounded-xl border border-slate-200 shadow-sm p-4">
                    <div class="flex items-center justify-between mb-3">
                        <div class="flex items-center space-x-2">
                            <span class="method-badge badge-put">PUT</span>
                            <span class="font-mono text-sm font-semibold text-slate-800">/api/admin/layouts/contactPageLayout</span>
                        </div>
                        <span class="text-xs bg-slate-100 text-slate-600 px-2 py-0.5 rounded font-medium">Auth Required</span>
                    </div>
                    <p class="text-xs text-slate-600 mb-3">Switch between the 4 contact page templates: Split Form with Google Map, Modern Card with Social Hub, Corporate Multi-office, Minimal Quick Reach.</p>
                    <div class="grid grid-cols-3 gap-2 mb-3">
                        <div class="col-span-2">
                            <label class="text-xs font-medium text-slate-600">Select Template</label>
                            <select id="contact-layout-select" class="w-full text-xs border border-slate-200 rounded p-2 bg-white mt-0.5 font-medium">
                                <option value="layout1">layout1: Split Form with Google Map</option>
                                <option value="layout2">layout2: Modern Card with Social Hub</option>
                                <option value="layout3">layout3: Corporate Multi-office</option>
                                <option value="layout4">layout4: Minimal Quick Reach</option>
                            </select>
                        </div>
                        <div class="flex items-end">
                            <button onclick="testSwitchContactLayout()" class="w-full bg-indigo-600 hover:bg-indigo-700 text-white font-medium text-xs py-2 rounded-lg transition shadow-sm">
                                Switch Layout
                            </button>
                        </div>
                    </div>
                </div>

                <!-- 2. Public Visitor Submission -->
                <div class="bg-white rounded-xl border border-slate-200 shadow-sm p-4">
                    <div class="flex items-center justify-between mb-3">
                        <div class="flex items-center space-x-2">
                            <span class="method-badge badge-post">POST</span>
                            <span class="font-mono text-sm font-semibold text-slate-800">/api/public/contact/inquiry</span>
                        </div>
                        <span class="text-xs text-slate-400">Public (No Auth)</span>
                    </div>
                    <p class="text-xs text-slate-600 mb-3">Visitor inquiry submission. Validates required fields, email format, max 2000 chars message, and silent honeypot discard.</p>
                    <div class="grid grid-cols-2 gap-2 mb-2">
                        <div>
                            <label class="text-xs font-medium text-slate-600">Name *</label>
                            <input id="contact-name" type="text" value="John Doe" class="w-full text-xs border border-slate-200 rounded p-1.5 mt-0.5 outline-none">
                        </div>
                        <div>
                            <label class="text-xs font-medium text-slate-600">Email *</label>
                            <input id="contact-email" type="email" value="john.doe@clientcompany.com" class="w-full text-xs border border-slate-200 rounded p-1.5 mt-0.5 outline-none font-mono">
                        </div>
                    </div>
                    <div class="grid grid-cols-2 gap-2 mb-2">
                        <div>
                            <label class="text-xs font-medium text-slate-600">Phone</label>
                            <input id="contact-phone" type="text" value="+1 555 123 4567" class="w-full text-xs border border-slate-200 rounded p-1.5 mt-0.5 outline-none">
                        </div>
                        <div>
                            <label class="text-xs font-medium text-slate-600">Service Interested</label>
                            <input id="contact-service" type="text" value="Web Development" class="w-full text-xs border border-slate-200 rounded p-1.5 mt-0.5 outline-none">
                        </div>
                    </div>
                    <div class="mb-2">
                        <label class="text-xs font-medium text-slate-600">Message (max 2000 chars) *</label>
                        <textarea id="contact-message" rows="2" class="w-full text-xs border border-slate-200 rounded p-1.5 mt-0.5 outline-none">We would like to request a proposal for our redesign.</textarea>
                    </div>
                    <div class="mb-3">
                        <label class="text-xs font-medium text-slate-500">Honeypot field (hidden from real users; bots fill this)</label>
                        <input id="contact-website" type="text" placeholder="Leave empty for valid user submission" class="w-full text-xs border border-amber-200 rounded p-1.5 mt-0.5 outline-none bg-amber-50/50 font-mono">
                    </div>
                    <div class="grid grid-cols-2 gap-2">
                        <button onclick="testSubmitInquiry(false)" class="bg-indigo-600 hover:bg-indigo-700 text-white font-medium text-xs py-2 rounded-lg transition shadow-sm">
                            <i class="fa-solid fa-paper-plane mr-1"></i> Send Valid Submission (201)
                        </button>
                        <button onclick="testSubmitInquiry(true)" class="bg-amber-600 hover:bg-amber-700 text-white font-medium text-xs py-2 rounded-lg transition shadow-sm" title="Simulates a bot filling in the honeypot field. Returns 201 silently but does not store in DB.">
                            <i class="fa-solid fa-robot mr-1"></i> Test Spambot Honeypot
                        </button>
                    </div>
                </div>

                <!-- 3. Public Contact Info & Services -->
                <div class="bg-white rounded-xl border border-slate-200 shadow-sm p-4">
                    <div class="flex items-center justify-between mb-2">
                        <div class="flex items-center space-x-2">
                            <span class="method-badge badge-get">GET</span>
                            <span class="font-mono text-sm font-semibold text-slate-800">/api/public/contact</span>
                        </div>
                        <span class="text-xs text-slate-400">Public (No Auth)</span>
                    </div>
                    <p class="text-xs text-slate-600 mb-3">Fetch active contact layout, site contact details, published services (for dropdown), and SEO.</p>
                    <button onclick="testGetContactPublic()" class="w-full bg-slate-800 hover:bg-slate-900 text-white font-medium text-xs py-2 rounded-lg transition shadow-sm">
                        <i class="fa-solid fa-eye mr-1"></i> Fetch Public Contact Config
                    </button>
                </div>

                <!-- 4. Admin List Inquiries -->
                <div class="bg-white rounded-xl border border-slate-200 shadow-sm p-4">
                    <div class="flex items-center justify-between mb-3">
                        <div class="flex items-center space-x-2">
                            <span class="method-badge badge-get">GET</span>
                            <span class="font-mono text-sm font-semibold text-slate-800">/api/admin/inquiries</span>
                        </div>
                        <span class="text-xs bg-slate-100 text-slate-600 px-2 py-0.5 rounded font-medium">Auth Required</span>
                    </div>
                    <p class="text-xs text-slate-600 mb-3">List visitor inquiries with status filtering (?status=) and search (?q=).</p>
                    <div class="grid grid-cols-3 gap-2 mb-3">
                        <div>
                            <label class="text-xs font-medium text-slate-600">Status Filter</label>
                            <select id="inquiries-filter-status" class="w-full text-xs border border-slate-200 rounded p-1.5 mt-0.5 bg-white outline-none">
                                <option value="">All Statuses</option>
                                <option value="new">new</option>
                                <option value="read">read</option>
                                <option value="replied">replied</option>
                                <option value="archived">archived</option>
                            </select>
                        </div>
                        <div>
                            <label class="text-xs font-medium text-slate-600">Search (?q=)</label>
                            <input id="inquiries-filter-q" type="text" placeholder="Search name, email, keyword..." class="w-full text-xs border border-slate-200 rounded p-1.5 mt-0.5 outline-none">
                        </div>
                        <div class="flex items-end">
                            <button onclick="testListInquiries()" class="w-full bg-slate-900 hover:bg-slate-800 text-white font-medium text-xs py-2 rounded-lg transition shadow-sm">
                                <i class="fa-solid fa-list mr-1"></i> List Inquiries
                            </button>
                        </div>
                    </div>
                </div>

                <!-- 5. Single Inquiry Management (GET, PUT status, DELETE) -->
                <div class="bg-white rounded-xl border border-slate-200 shadow-sm p-4">
                    <div class="flex items-center justify-between mb-3">
                        <div class="flex items-center space-x-2">
                            <span class="method-badge badge-get">GET</span>
                            <span class="method-badge badge-put">PUT</span>
                            <span class="method-badge badge-del">DELETE</span>
                            <span class="font-mono text-sm font-semibold text-slate-800">/api/admin/inquiries/:id</span>
                        </div>
                        <span class="text-xs bg-slate-100 text-slate-600 px-2 py-0.5 rounded font-medium">Auth Required</span>
                    </div>
                    <p class="text-xs text-slate-600 mb-3">View single inquiry, update status (new, read, replied, archived) with optional notes, or delete inquiry.</p>
                    <div class="grid grid-cols-3 gap-2 mb-2">
                        <div>
                            <label class="text-xs font-medium text-slate-600">Inquiry ID *</label>
                            <input id="inquiry-id" type="number" value="1" class="w-full text-xs border border-slate-200 rounded p-1.5 mt-0.5 font-mono outline-none">
                        </div>
                        <div>
                            <label class="text-xs font-medium text-slate-600">New Status</label>
                            <select id="inquiry-status-select" class="w-full text-xs border border-slate-200 rounded p-1.5 mt-0.5 bg-white outline-none">
                                <option value="read">read</option>
                                <option value="replied">replied</option>
                                <option value="archived">archived</option>
                                <option value="new">new</option>
                            </select>
                        </div>
                        <div>
                            <label class="text-xs font-medium text-slate-600">Admin Notes</label>
                            <input id="inquiry-notes" type="text" placeholder="Internal follow-up notes" class="w-full text-xs border border-slate-200 rounded p-1.5 mt-0.5 outline-none">
                        </div>
                    </div>
                    <div class="grid grid-cols-3 gap-2">
                        <button onclick="testGetSingleInquiry()" class="bg-slate-700 hover:bg-slate-800 text-white font-medium text-xs py-2 rounded-lg transition shadow-sm">
                            <i class="fa-solid fa-magnifying-glass mr-1"></i> Get Inquiry
                        </button>
                        <button onclick="testUpdateInquiryStatus()" class="bg-indigo-600 hover:bg-indigo-700 text-white font-medium text-xs py-2 rounded-lg transition shadow-sm">
                            <i class="fa-solid fa-pen-to-square mr-1"></i> Update Status
                        </button>
                        <button onclick="testDeleteInquiry()" class="bg-red-600 hover:bg-red-700 text-white font-medium text-xs py-2 rounded-lg transition shadow-sm">
                            <i class="fa-solid fa-trash mr-1"></i> Delete
                        </button>
                    </div>
                </div>

                <!-- 6. Contact SEO -->
                <div class="bg-white rounded-xl border border-slate-200 shadow-sm p-4">
                    <div class="flex items-center justify-between mb-3">
                        <div class="flex items-center space-x-2">
                            <span class="method-badge badge-get">GET</span>
                            <span class="method-badge badge-put">PUT</span>
                            <span class="font-mono text-sm font-semibold text-slate-800">/api/public/seo/contact, /api/admin/seo/contact</span>
                        </div>
                        <span class="text-xs bg-slate-100 text-slate-600 px-2 py-0.5 rounded font-medium">Public / Auth</span>
                    </div>
                    <div class="grid grid-cols-2 gap-2 mb-2">
                        <div>
                            <label class="text-xs font-medium text-slate-600">Meta Title</label>
                            <input id="contact-seo-title" type="text" value="Contact Us | Informatic Tech Solutions" class="w-full text-xs border border-slate-200 rounded p-1.5 mt-0.5 outline-none">
                        </div>
                        <div>
                            <label class="text-xs font-medium text-slate-600">Keywords</label>
                            <input id="contact-seo-keywords" type="text" value="contact, inquiries, proposal, it services" class="w-full text-xs border border-slate-200 rounded p-1.5 mt-0.5 outline-none">
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="text-xs font-medium text-slate-600">Meta Description</label>
                        <input id="contact-seo-desc" type="text" value="Get in touch with our expert engineering team for project quotes and inquiries." class="w-full text-xs border border-slate-200 rounded p-1.5 mt-0.5 outline-none">
                    </div>
                    <div class="grid grid-cols-2 gap-2">
                        <button onclick="testGetContactSeo()" class="bg-slate-700 hover:bg-slate-800 text-white font-medium text-xs py-2 rounded-lg transition shadow-sm">
                            <i class="fa-solid fa-eye mr-1"></i> Fetch Contact SEO
                        </button>
                        <button onclick="testUpdateContactSeo()" class="bg-purple-600 hover:bg-purple-700 text-white font-medium text-xs py-2 rounded-lg transition shadow-sm">
                            <i class="fa-solid fa-floppy-disk mr-1"></i> Update Contact SEO
                        </button>
                    </div>
                </div>

                <!-- 7. Contact Info (Email, Phone, Address, Map, Offices, Socials) -->
                <div class="bg-white rounded-xl border border-slate-200 shadow-sm p-4">
                    <div class="flex items-center justify-between mb-3">
                        <div class="flex items-center space-x-2">
                            <span class="method-badge badge-put">PUT</span>
                            <span class="font-mono text-sm font-semibold text-slate-800">/api/admin/contact (contactInfo)</span>
                        </div>
                        <span class="text-xs bg-slate-100 text-slate-600 px-2 py-0.5 rounded font-medium">Auth Required</span>
                    </div>
                    <p class="text-xs text-slate-600 mb-3">Add or update contact details, Google Map embed URL, multi-office locations array, and social media hub links.</p>

                    <div class="grid grid-cols-2 gap-2 mb-2">
                        <div>
                            <label class="text-xs font-medium text-slate-600">Contact Email</label>
                            <input id="contact-info-email" type="email" value="contact@clientcompany.com" class="w-full text-xs border border-slate-200 rounded p-1.5 mt-0.5 font-mono outline-none">
                        </div>
                        <div>
                            <label class="text-xs font-medium text-slate-600">Contact Phone</label>
                            <input id="contact-info-phone" type="text" value="+1 555 123 4567" class="w-full text-xs border border-slate-200 rounded p-1.5 mt-0.5 outline-none">
                        </div>
                    </div>

                    <div class="grid grid-cols-2 gap-2 mb-2">
                        <div>
                            <label class="text-xs font-medium text-slate-600">Primary Address</label>
                            <input id="contact-info-address" type="text" value="742 Evergreen Terrace, Springfield, OR" class="w-full text-xs border border-slate-200 rounded p-1.5 mt-0.5 outline-none">
                        </div>
                        <div>
                            <label class="text-xs font-medium text-slate-600">Google Map Embed URL</label>
                            <input id="contact-info-map" type="text" value="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3022.183792036069!2d-73.98773128459418!3d40.75889497932681!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x89c25855c6480299%3A0x55194ec5a1ae072e!2sTimes%20Square!5e0!3m2!1sen!2sus!4v1620000000000!5m2!1sen!2sus" class="w-full text-xs border border-slate-200 rounded p-1.5 mt-0.5 font-mono outline-none">
                        </div>
                    </div>

                    <div class="mb-2">
                        <label class="text-xs font-medium text-slate-600">Offices List (JSON)</label>
                        <textarea id="contact-info-offices" rows="4" class="w-full text-xs font-mono border border-slate-200 rounded p-1.5 mt-0.5 outline-none">[
  {
    "name": "Springfield HQ",
    "address": "742 Evergreen Terrace, Springfield, OR",
    "phone": "+1 555 123 4567",
    "email": "hq@clientcompany.com"
  },
  {
    "name": "New York Branch",
    "address": "350 5th Ave, New York, NY",
    "phone": "+1 212 555 0199",
    "email": "ny@clientcompany.com"
  }
]</textarea>
                    </div>

                    <div class="mb-3">
                        <label class="text-xs font-medium text-slate-600">Socials Links (JSON)</label>
                        <textarea id="contact-info-socials" rows="3" class="w-full text-xs font-mono border border-slate-200 rounded p-1.5 mt-0.5 outline-none">{
  "facebook": "https://facebook.com/clientcompany",
  "instagram": "https://instagram.com/clientcompany",
  "linkedin": "https://linkedin.com/company/clientcompany",
  "twitter": "https://twitter.com/clientcompany",
  "youtube": "https://youtube.com/@clientcompany"
}</textarea>
                    </div>

                    <div class="grid grid-cols-2 gap-2">
                        <button onclick="testSaveContactInfo()" class="bg-indigo-600 hover:bg-indigo-700 text-white font-medium text-xs py-2 rounded-lg transition shadow-sm">
                            <i class="fa-solid fa-floppy-disk mr-1"></i> Save Contact Info
                        </button>
                        <button onclick="testFetchContactInfo()" class="bg-slate-800 hover:bg-slate-900 text-white font-medium text-xs py-2 rounded-lg transition shadow-sm">
                            <i class="fa-solid fa-arrows-rotate mr-1"></i> Reload Current Info
                        </button>
                    </div>
                </div>
            </div>

            <!-- ══════════════════════════════════════════════════════════ -->
            <!-- MODULE 13: CAREERS APIS -->
            <!-- ══════════════════════════════════════════════════════════ -->
            <div id="section-careers" class="space-y-4 hidden">
                <!-- 1. GET /api/public/careers -->
                <div class="bg-white rounded-xl border border-slate-200 shadow-sm p-4">
                    <div class="flex items-center justify-between mb-3">
                        <div class="flex items-center space-x-2">
                            <span class="method-badge badge-get">GET</span>
                            <span class="font-mono text-sm font-semibold text-slate-800">/api/public/careers</span>
                        </div>
                        <span class="text-xs text-slate-400">Public (No Auth)</span>
                    </div>
                    <p class="text-xs text-slate-600 mb-3">Public feed of open job positions. Supports filtering by department, type, location, search query, and limit.</p>
                    <div class="grid grid-cols-4 gap-2 mb-3">
                        <div>
                            <label class="text-[10px] text-slate-400 font-medium">Department</label>
                            <select id="career-pub-dept" class="w-full text-xs border border-slate-200 rounded p-1.5 outline-none bg-white">
                                <option value="" selected>All Departments</option>
                                <option value="Engineering">Engineering</option>
                                <option value="DevOps & Cloud">DevOps & Cloud</option>
                                <option value="Design">Design</option>
                                <option value="AI & Data">AI & Data</option>
                            </select>
                        </div>
                        <div>
                            <label class="text-[10px] text-slate-400 font-medium">Type</label>
                            <select id="career-pub-type" class="w-full text-xs border border-slate-200 rounded p-1.5 outline-none bg-white">
                                <option value="" selected>All Types</option>
                                <option value="full-time">full-time</option>
                                <option value="part-time">part-time</option>
                                <option value="contract">contract</option>
                                <option value="internship">internship</option>
                            </select>
                        </div>
                        <div>
                            <label class="text-[10px] text-slate-400 font-medium">Search</label>
                            <input id="career-pub-search" type="text" placeholder="Title, keyword..." class="w-full text-xs border border-slate-200 rounded p-1.5 outline-none">
                        </div>
                        <div>
                            <label class="text-[10px] text-slate-400 font-medium">Limit</label>
                            <input id="career-pub-limit" type="number" value="20" class="w-full text-xs border border-slate-200 rounded p-1.5 outline-none">
                        </div>
                    </div>
                    <button onclick="testPublicCareers()" class="w-full bg-blue-600 hover:bg-blue-700 text-white font-medium text-xs py-2.5 rounded-lg transition shadow-sm flex items-center justify-center space-x-2">
                        <i class="fa-solid fa-briefcase"></i>
                        <span>Fetch Public Careers Feed</span>
                    </button>
                </div>

                <!-- 2. GET /api/public/careers/:slug -->
                <div class="bg-white rounded-xl border border-slate-200 shadow-sm p-4">
                    <div class="flex items-center justify-between mb-3">
                        <div class="flex items-center space-x-2">
                            <span class="method-badge badge-get">GET</span>
                            <span class="font-mono text-sm font-semibold text-slate-800">/api/public/careers/{slug}</span>
                        </div>
                        <span class="text-xs text-slate-400">Public (No Auth)</span>
                    </div>
                    <p class="text-xs text-slate-600 mb-3">Fetch single open job position detail by URL slug.</p>
                    <div class="flex space-x-2">
                        <input id="career-pub-slug" type="text" value="senior-full-stack-engineer-laravel-react" placeholder="Job slug" class="flex-1 text-xs border border-slate-200 rounded p-2 outline-none font-mono">
                        <button onclick="testPublicCareerDetail()" class="bg-slate-800 hover:bg-slate-900 text-white px-4 py-2 rounded-lg text-xs font-medium transition">
                            Fetch Job Detail
                        </button>
                    </div>
                </div>

                <!-- 3. GET /api/admin/careers & /api/admin/careers/:id -->
                <div class="bg-white rounded-xl border border-slate-200 shadow-sm p-4">
                    <div class="flex items-center justify-between mb-3">
                        <div class="flex items-center space-x-2">
                            <span class="method-badge badge-get">GET</span>
                            <span class="font-mono text-sm font-semibold text-slate-800">/api/admin/careers</span>
                        </div>
                        <span class="text-xs bg-slate-100 text-slate-600 px-2 py-0.5 rounded font-medium">Auth Required</span>
                    </div>
                    <p class="text-xs text-slate-600 mb-3">Admin list of all job openings (open & closed) or single position by integer ID.</p>
                    <div class="grid grid-cols-2 gap-2 mb-3">
                        <div>
                            <label class="text-[10px] text-slate-400 font-medium">Status Filter</label>
                            <select id="career-admin-status" class="w-full text-xs border border-slate-200 rounded p-1.5 outline-none bg-white">
                                <option value="" selected>All Statuses</option>
                                <option value="open">Open</option>
                                <option value="closed">Closed</option>
                            </select>
                        </div>
                        <div>
                            <label class="text-[10px] text-slate-400 font-medium">Department</label>
                            <input id="career-admin-dept" type="text" placeholder="Department" class="w-full text-xs border border-slate-200 rounded p-1.5 outline-none">
                        </div>
                    </div>
                    <div class="grid grid-cols-2 gap-2">
                        <button onclick="testAdminCareers()" class="bg-slate-800 hover:bg-slate-900 text-white font-medium text-xs py-2 rounded-lg transition">
                            Fetch All Admin Careers
                        </button>
                        <div class="flex space-x-1">
                            <input id="career-admin-fetch-id" type="number" value="1" placeholder="ID" class="w-16 text-xs border border-slate-200 rounded p-1.5 outline-none font-mono">
                            <button onclick="testAdminCareerSingle()" class="flex-1 bg-blue-700 hover:bg-blue-800 text-white font-medium text-xs py-2 rounded-lg transition">
                                Fetch By ID
                            </button>
                        </div>
                    </div>
                </div>

                <!-- 4. POST /api/admin/careers (Create) -->
                <div class="bg-white rounded-xl border border-slate-200 shadow-sm p-4">
                    <div class="flex items-center justify-between mb-3">
                        <div class="flex items-center space-x-2">
                            <span class="method-badge badge-post">POST</span>
                            <span class="font-mono text-sm font-semibold text-slate-800">/api/admin/careers</span>
                        </div>
                        <span class="text-xs bg-slate-100 text-slate-600 px-2 py-0.5 rounded font-medium">Auth Required</span>
                    </div>
                    <div class="space-y-2 mb-3">
                        <div class="grid grid-cols-2 gap-2">
                            <div>
                                <label class="text-xs font-medium text-slate-600">Job Title</label>
                                <input id="career-create-title" type="text" value="Staff Systems Architect" class="w-full text-xs border border-slate-200 rounded p-1.5 mt-0.5 outline-none">
                            </div>
                            <div>
                                <label class="text-xs font-medium text-slate-600">Department</label>
                                <input id="career-create-dept" type="text" value="Engineering" class="w-full text-xs border border-slate-200 rounded p-1.5 mt-0.5 outline-none">
                            </div>
                        </div>
                        <div class="grid grid-cols-3 gap-2">
                            <div>
                                <label class="text-xs font-medium text-slate-600">Location</label>
                                <input id="career-create-loc" type="text" value="Remote / Global" class="w-full text-xs border border-slate-200 rounded p-1.5 mt-0.5 outline-none">
                            </div>
                            <div>
                                <label class="text-xs font-medium text-slate-600">Type</label>
                                <select id="career-create-type" class="w-full text-xs border border-slate-200 rounded p-1.5 mt-0.5 bg-white outline-none">
                                    <option value="full-time" selected>full-time</option>
                                    <option value="part-time">part-time</option>
                                    <option value="contract">contract</option>
                                    <option value="internship">internship</option>
                                </select>
                            </div>
                            <div>
                                <label class="text-xs font-medium text-slate-600">Experience</label>
                                <input id="career-create-exp" type="text" value="5+ years" class="w-full text-xs border border-slate-200 rounded p-1.5 mt-0.5 outline-none">
                            </div>
                        </div>
                        <div class="grid grid-cols-2 gap-2">
                            <div>
                                <label class="text-xs font-medium text-slate-600">Apply URL / Email</label>
                                <input id="career-create-url" type="text" value="mailto:jobs@informatic.example.com" class="w-full text-xs border border-slate-200 rounded p-1.5 mt-0.5 font-mono outline-none">
                            </div>
                            <div>
                                <label class="text-xs font-medium text-slate-600">Status</label>
                                <select id="career-create-status" class="w-full text-xs border border-slate-200 rounded p-1.5 mt-0.5 bg-white outline-none">
                                    <option value="open" selected>Open</option>
                                    <option value="closed">Closed</option>
                                </select>
                            </div>
                        </div>
                        <div>
                            <label class="text-xs font-medium text-slate-600">Requirements (JSON array or lines)</label>
                            <textarea id="career-create-reqs" rows="2" class="w-full text-xs border border-slate-200 rounded p-1.5 font-mono mt-0.5 outline-none">["5+ years high scale architecture", "Experience with AWS, Docker, Kubernetes", "Strong communication skills"]</textarea>
                        </div>
                        <div>
                            <label class="text-xs font-medium text-slate-600">Description (HTML / text)</label>
                            <textarea id="career-create-desc" rows="2" class="w-full text-xs border border-slate-200 rounded p-1.5 font-mono mt-0.5 outline-none"><p>We are seeking a Staff Systems Architect to lead platform reliability and distributed services.</p></textarea>
                        </div>
                    </div>
                    <button onclick="testCreateCareer()" class="w-full bg-blue-600 hover:bg-blue-700 text-white font-medium text-xs py-2 rounded-lg transition shadow-sm">
                        <i class="fa-solid fa-plus mr-1"></i> Create Job Opening
                    </button>
                </div>

                <!-- 5. PUT & DELETE /api/admin/careers/:id -->
                <div class="bg-white rounded-xl border border-slate-200 shadow-sm p-4">
                    <div class="flex items-center justify-between mb-3">
                        <div class="flex items-center space-x-2">
                            <span class="method-badge badge-put">PUT</span>
                            <span class="method-badge badge-del">DELETE</span>
                            <span class="font-mono text-sm font-semibold text-slate-800">/api/admin/careers/{id}</span>
                        </div>
                        <span class="text-xs bg-slate-100 text-slate-600 px-2 py-0.5 rounded font-medium">Auth Required</span>
                    </div>
                    <div class="grid grid-cols-4 gap-2 mb-3">
                        <div>
                            <label class="text-[10px] text-slate-400 font-medium">Opening ID</label>
                            <input id="career-manage-id" type="number" value="1" class="w-full text-xs border border-slate-200 rounded p-1.5 outline-none font-mono">
                        </div>
                        <div>
                            <label class="text-[10px] text-slate-400 font-medium">Updated Title</label>
                            <input id="career-manage-title" type="text" placeholder="Leave blank to keep" class="w-full text-xs border border-slate-200 rounded p-1.5 outline-none">
                        </div>
                        <div>
                            <label class="text-[10px] text-slate-400 font-medium">Status</label>
                            <select id="career-manage-status" class="w-full text-xs border border-slate-200 rounded p-1.5 outline-none bg-white">
                                <option value="open">open</option>
                                <option value="closed">closed</option>
                            </select>
                        </div>
                        <div>
                            <label class="text-[10px] text-slate-400 font-medium">Experience</label>
                            <input id="career-manage-exp" type="text" placeholder="e.g. 5-7 years" class="w-full text-xs border border-slate-200 rounded p-1.5 outline-none">
                        </div>
                    </div>
                    <div class="grid grid-cols-2 gap-2">
                        <button onclick="testUpdateCareer()" class="bg-amber-600 hover:bg-amber-700 text-white font-medium text-xs py-2 rounded-lg transition shadow-sm">
                            <i class="fa-solid fa-pen-to-square mr-1"></i> Update Career Opening
                        </button>
                        <button onclick="testDeleteCareer()" class="bg-rose-600 hover:bg-rose-700 text-white font-medium text-xs py-2 rounded-lg transition shadow-sm">
                            <i class="fa-solid fa-trash mr-1"></i> Delete Career Opening
                        </button>
                    </div>
                </div>
            </div>

            <!-- ══════════════════════════════════════════════════════════ -->
            <!-- MODULE 14: FAQS APIS -->
            <!-- ══════════════════════════════════════════════════════════ -->
            <div id="section-faqs" class="space-y-4 hidden">
                <!-- 1. GET /api/public/faqs -->
                <div class="bg-white rounded-xl border border-slate-200 shadow-sm p-4">
                    <div class="flex items-center justify-between mb-3">
                        <div class="flex items-center space-x-2">
                            <span class="method-badge badge-get">GET</span>
                            <span class="font-mono text-sm font-semibold text-slate-800">/api/public/faqs</span>
                        </div>
                        <span class="text-xs text-slate-400">Public (No Auth)</span>
                    </div>
                    <p class="text-xs text-slate-600 mb-3">Public list of published FAQs (supports category filtering and search query).</p>
                    <div class="grid grid-cols-2 gap-2 mb-3">
                        <div>
                            <label class="text-[10px] text-slate-400 font-medium">Category</label>
                            <select id="faq-pub-cat" class="w-full text-xs border border-slate-200 rounded p-1.5 outline-none bg-white">
                                <option value="" selected>All Categories</option>
                                <option value="General">General</option>
                                <option value="Process">Process</option>
                                <option value="Billing & Contracts">Billing & Contracts</option>
                                <option value="Security">Security</option>
                                <option value="Support">Support</option>
                            </select>
                        </div>
                        <div>
                            <label class="text-[10px] text-slate-400 font-medium">Search Keyword</label>
                            <input id="faq-pub-q" type="text" placeholder="Search questions & answers..." class="w-full text-xs border border-slate-200 rounded p-1.5 outline-none">
                        </div>
                    </div>
                    <button onclick="testPublicFaqs()" class="w-full bg-emerald-600 hover:bg-emerald-700 text-white font-medium text-xs py-2.5 rounded-lg transition shadow-sm flex items-center justify-center space-x-2">
                        <i class="fa-solid fa-circle-question"></i>
                        <span>Fetch Public FAQs</span>
                    </button>
                </div>

                <!-- 2. GET /api/admin/faqs & /api/admin/faqs/:id -->
                <div class="bg-white rounded-xl border border-slate-200 shadow-sm p-4">
                    <div class="flex items-center justify-between mb-3">
                        <div class="flex items-center space-x-2">
                            <span class="method-badge badge-get">GET</span>
                            <span class="font-mono text-sm font-semibold text-slate-800">/api/admin/faqs</span>
                        </div>
                        <span class="text-xs bg-slate-100 text-slate-600 px-2 py-0.5 rounded font-medium">Auth Required</span>
                    </div>
                    <p class="text-xs text-slate-600 mb-3">Admin list of all FAQs (including unpublished) or single item by ID.</p>
                    <div class="grid grid-cols-2 gap-2 mb-3">
                        <button onclick="testAdminFaqs()" class="bg-slate-800 hover:bg-slate-900 text-white font-medium text-xs py-2 rounded-lg transition">
                            Fetch All Admin FAQs
                        </button>
                        <div class="flex space-x-1">
                            <input id="faq-admin-fetch-id" type="number" value="1" placeholder="ID" class="w-16 text-xs border border-slate-200 rounded p-1.5 outline-none font-mono">
                            <button onclick="testAdminFaqSingle()" class="flex-1 bg-emerald-700 hover:bg-emerald-800 text-white font-medium text-xs py-2 rounded-lg transition">
                                Fetch By ID
                            </button>
                        </div>
                    </div>
                </div>

                <!-- 3. PUT /api/admin/faqs (Bulk replace-all & reorder) -->
                <div class="bg-white rounded-xl border border-slate-200 shadow-sm p-4">
                    <div class="flex items-center justify-between mb-3">
                        <div class="flex items-center space-x-2">
                            <span class="method-badge badge-put">PUT</span>
                            <span class="font-mono text-sm font-semibold text-slate-800">/api/admin/faqs</span>
                        </div>
                        <span class="text-xs bg-slate-100 text-slate-600 px-2 py-0.5 rounded font-medium">Auth Required</span>
                    </div>
                    <p class="text-xs text-slate-600 mb-2">Save the whole FAQ list (replace-all, preserves order). Array of FAQ objects.</p>
                    <textarea id="faq-bulk-json" rows="6" class="w-full text-xs font-mono border border-slate-200 rounded p-2 mb-3 outline-none">{
  "faqs": [
    { "id": 1, "question": "What software engineering services does Informatic provide?", "answer": "<p>We provide full-cycle engineering services including enterprise web & mobile application development, cloud architecture and DevOps, generative AI integrations, system modernization, and dedicated engineering teams.</p>", "category": "General", "order": 1, "isPublished": true },
    { "id": 2, "question": "How does Informatic guarantee project delivery timelines and code quality?", "answer": "<p>We operate on Agile sprints with bi-weekly milestone demos, automated continuous integration (CI) test suites, rigorous peer code reviews, and transparent sprint tracking through Jira and GitHub.</p>", "category": "Process", "order": 2, "isPublished": true },
    { "id": 3, "question": "What engagement and pricing models are available?", "answer": "<p>We offer flexible models tailored to your requirements: Dedicated Engineering Squads (monthly retainers), Time & Materials for evolving roadmaps, and Fixed-Price Milestone contracts for well-defined scopes.</p>", "category": "Billing & Contracts", "order": 3, "isPublished": true }
  ]
}</textarea>
                    <button onclick="testBulkSaveFaqs()" class="w-full bg-emerald-600 hover:bg-emerald-700 text-white font-medium text-xs py-2 rounded-lg transition shadow-sm">
                        <i class="fa-solid fa-layer-group mr-1"></i> Bulk Save / Replace All FAQs
                    </button>
                </div>

                <!-- 4. POST /api/admin/faqs/item (Create single) -->
                <div class="bg-white rounded-xl border border-slate-200 shadow-sm p-4">
                    <div class="flex items-center justify-between mb-3">
                        <div class="flex items-center space-x-2">
                            <span class="method-badge badge-post">POST</span>
                            <span class="font-mono text-sm font-semibold text-slate-800">/api/admin/faqs/item</span>
                        </div>
                        <span class="text-xs bg-slate-100 text-slate-600 px-2 py-0.5 rounded font-medium">Auth Required</span>
                    </div>
                    <div class="space-y-2 mb-3">
                        <div>
                            <label class="text-xs font-medium text-slate-600">Question</label>
                            <input id="faq-create-q" type="text" value="Can Informatic sign non-disclosure agreements (NDAs)?" class="w-full text-xs border border-slate-200 rounded p-1.5 mt-0.5 outline-none">
                        </div>
                        <div>
                            <label class="text-xs font-medium text-slate-600">Answer (HTML / text)</label>
                            <textarea id="faq-create-a" rows="2" class="w-full text-xs border border-slate-200 rounded p-1.5 font-mono mt-0.5 outline-none"><p>Yes, we execute mutual non-disclosure agreements prior to reviewing any proprietary documentation or codebase.</p></textarea>
                        </div>
                        <div class="grid grid-cols-3 gap-2">
                            <div>
                                <label class="text-xs font-medium text-slate-600">Category</label>
                                <input id="faq-create-cat" type="text" value="Security" class="w-full text-xs border border-slate-200 rounded p-1.5 mt-0.5 outline-none">
                            </div>
                            <div>
                                <label class="text-xs font-medium text-slate-600">Display Order</label>
                                <input id="faq-create-order" type="number" value="7" class="w-full text-xs border border-slate-200 rounded p-1.5 mt-0.5 outline-none font-mono">
                            </div>
                            <div>
                                <label class="text-xs font-medium text-slate-600">Published</label>
                                <select id="faq-create-published" class="w-full text-xs border border-slate-200 rounded p-1.5 mt-0.5 bg-white outline-none">
                                    <option value="1" selected>Published</option>
                                    <option value="0">Draft</option>
                                </select>
                            </div>
                        </div>
                    </div>
                    <button onclick="testCreateFaq()" class="w-full bg-emerald-600 hover:bg-emerald-700 text-white font-medium text-xs py-2 rounded-lg transition shadow-sm">
                        <i class="fa-solid fa-plus mr-1"></i> Create Single FAQ Item
                    </button>
                </div>

                <!-- 5. PUT & DELETE /api/admin/faqs/:id -->
                <div class="bg-white rounded-xl border border-slate-200 shadow-sm p-4">
                    <div class="flex items-center justify-between mb-3">
                        <div class="flex items-center space-x-2">
                            <span class="method-badge badge-put">PUT</span>
                            <span class="method-badge badge-del">DELETE</span>
                            <span class="font-mono text-sm font-semibold text-slate-800">/api/admin/faqs/{id}</span>
                        </div>
                        <span class="text-xs bg-slate-100 text-slate-600 px-2 py-0.5 rounded font-medium">Auth Required</span>
                    </div>
                    <div class="grid grid-cols-4 gap-2 mb-3">
                        <div>
                            <label class="text-[10px] text-slate-400 font-medium">FAQ ID</label>
                            <input id="faq-manage-id" type="number" value="1" class="w-full text-xs border border-slate-200 rounded p-1.5 outline-none font-mono">
                        </div>
                        <div class="col-span-2">
                            <label class="text-[10px] text-slate-400 font-medium">Updated Question</label>
                            <input id="faq-manage-q" type="text" value="Updated question text?" class="w-full text-xs border border-slate-200 rounded p-1.5 outline-none">
                        </div>
                        <div>
                            <label class="text-[10px] text-slate-400 font-medium">Status</label>
                            <select id="faq-manage-pub" class="w-full text-xs border border-slate-200 rounded p-1.5 outline-none bg-white">
                                <option value="1">Published</option>
                                <option value="0">Draft</option>
                            </select>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="text-[10px] text-slate-400 font-medium">Category</label>
                        <input id="faq-manage-cat" type="text" value="General" class="w-full text-xs border border-slate-200 rounded p-1.5 outline-none">
                    </div>
                    <div class="grid grid-cols-2 gap-2">
                        <button onclick="testUpdateFaq()" class="bg-amber-600 hover:bg-amber-700 text-white font-medium text-xs py-2 rounded-lg transition shadow-sm">
                            <i class="fa-solid fa-pen-to-square mr-1"></i> Update FAQ Item
                        </button>
                        <button onclick="testDeleteFaq()" class="bg-rose-600 hover:bg-rose-700 text-white font-medium text-xs py-2 rounded-lg transition shadow-sm">
                            <i class="fa-solid fa-trash mr-1"></i> Delete FAQ Item
                        </button>
                    </div>
                </div>
            </div>

            <!-- ══════════════════════════════════════════════════════════ -->
            <!-- MODULE 15: TERMS OF SERVICE APIS -->
            <!-- ══════════════════════════════════════════════════════════ -->
            <div id="section-terms" class="space-y-4 hidden">
                <!-- 1. GET Public & Admin Terms -->
                <div class="bg-white rounded-xl border border-slate-200 shadow-sm p-4">
                    <div class="flex items-center justify-between mb-3">
                        <div class="flex items-center space-x-2">
                            <span class="method-badge badge-get">GET</span>
                            <span class="font-mono text-sm font-semibold text-slate-800">/api/public/terms & /api/admin/terms</span>
                        </div>
                        <span class="text-xs text-slate-400">Public & Admin</span>
                    </div>
                    <p class="text-xs text-slate-600 mb-3">Fetch Terms of Service content (also available under <code>/api/public/legal/terms</code> and <code>/api/admin/legal/terms</code>).</p>
                    <div class="grid grid-cols-2 gap-2">
                        <button onclick="testPublicTerms()" class="bg-cyan-600 hover:bg-cyan-700 text-white font-medium text-xs py-2.5 rounded-lg transition shadow-sm flex items-center justify-center space-x-2">
                            <i class="fa-solid fa-globe"></i>
                            <span>Fetch Public Terms</span>
                        </button>
                        <button onclick="testAdminTerms()" class="bg-slate-800 hover:bg-slate-900 text-white font-medium text-xs py-2.5 rounded-lg transition shadow-sm flex items-center justify-center space-x-2">
                            <i class="fa-solid fa-lock"></i>
                            <span>Fetch Admin Terms</span>
                        </button>
                    </div>
                </div>

                <!-- 2. PUT /api/admin/terms (Update Content) -->
                <div class="bg-white rounded-xl border border-slate-200 shadow-sm p-4">
                    <div class="flex items-center justify-between mb-3">
                        <div class="flex items-center space-x-2">
                            <span class="method-badge badge-put">PUT</span>
                            <span class="font-mono text-sm font-semibold text-slate-800">/api/admin/terms & /api/admin/legal/terms</span>
                        </div>
                        <span class="text-xs bg-slate-100 text-slate-600 px-2 py-0.5 rounded font-medium">Auth Required</span>
                    </div>
                    <div class="space-y-3 mb-3">
                        <div class="grid grid-cols-2 gap-2">
                            <div>
                                <label class="text-xs font-medium text-slate-600">Page Title</label>
                                <input id="terms-title" type="text" value="Terms of Service" class="w-full text-xs border border-slate-200 rounded p-1.5 mt-0.5 outline-none">
                            </div>
                            <div>
                                <label class="text-xs font-medium text-slate-600">Last Updated String</label>
                                <input id="terms-last-updated" type="text" value="October 2026" class="w-full text-xs border border-slate-200 rounded p-1.5 mt-0.5 outline-none font-mono">
                            </div>
                        </div>
                        <div>
                            <label class="text-xs font-medium text-slate-600">Legal Content (HTML formatted)</label>
                            <textarea id="terms-content" rows="6" class="w-full text-xs font-mono border border-slate-200 rounded p-2 outline-none"><h2>1. Acceptance of Terms</h2><p>By accessing our services, you agree to these Terms.</p><h2>2. Intellectual Property</h2><p>All source code and proprietary architectures are protected.</p><h2>3. Limitation of Liability</h2><p>We are not liable for incidental or consequential damages.</p></textarea>
                        </div>
                    </div>
                    <button onclick="testUpdateTerms()" class="w-full bg-cyan-600 hover:bg-cyan-700 text-white font-medium text-xs py-2 rounded-lg transition shadow-sm">
                        <i class="fa-solid fa-floppy-disk mr-1"></i> Save Terms of Service
                    </button>
                </div>
            </div>

            <!-- ══════════════════════════════════════════════════════════ -->
            <!-- MODULE 16: PRIVACY POLICY APIS -->
            <!-- ══════════════════════════════════════════════════════════ -->
            <div id="section-privacy" class="space-y-4 hidden">
                <!-- 1. GET Public & Admin Privacy -->
                <div class="bg-white rounded-xl border border-slate-200 shadow-sm p-4">
                    <div class="flex items-center justify-between mb-3">
                        <div class="flex items-center space-x-2">
                            <span class="method-badge badge-get">GET</span>
                            <span class="font-mono text-sm font-semibold text-slate-800">/api/public/privacy & /api/admin/privacy</span>
                        </div>
                        <span class="text-xs text-slate-400">Public & Admin</span>
                    </div>
                    <p class="text-xs text-slate-600 mb-3">Fetch Privacy Policy content (also available under <code>/api/public/legal/privacy</code> and <code>/api/admin/legal/privacy</code>).</p>
                    <div class="grid grid-cols-2 gap-2">
                        <button onclick="testPublicPrivacy()" class="bg-violet-600 hover:bg-violet-700 text-white font-medium text-xs py-2.5 rounded-lg transition shadow-sm flex items-center justify-center space-x-2">
                            <i class="fa-solid fa-globe"></i>
                            <span>Fetch Public Privacy</span>
                        </button>
                        <button onclick="testAdminPrivacy()" class="bg-slate-800 hover:bg-slate-900 text-white font-medium text-xs py-2.5 rounded-lg transition shadow-sm flex items-center justify-center space-x-2">
                            <i class="fa-solid fa-lock"></i>
                            <span>Fetch Admin Privacy</span>
                        </button>
                    </div>
                </div>

                <!-- 2. PUT /api/admin/privacy (Update Content) -->
                <div class="bg-white rounded-xl border border-slate-200 shadow-sm p-4">
                    <div class="flex items-center justify-between mb-3">
                        <div class="flex items-center space-x-2">
                            <span class="method-badge badge-put">PUT</span>
                            <span class="font-mono text-sm font-semibold text-slate-800">/api/admin/privacy & /api/admin/legal/privacy</span>
                        </div>
                        <span class="text-xs bg-slate-100 text-slate-600 px-2 py-0.5 rounded font-medium">Auth Required</span>
                    </div>
                    <div class="space-y-3 mb-3">
                        <div class="grid grid-cols-2 gap-2">
                            <div>
                                <label class="text-xs font-medium text-slate-600">Page Title</label>
                                <input id="privacy-title" type="text" value="Privacy Policy" class="w-full text-xs border border-slate-200 rounded p-1.5 mt-0.5 outline-none">
                            </div>
                            <div>
                                <label class="text-xs font-medium text-slate-600">Last Updated String</label>
                                <input id="privacy-last-updated" type="text" value="October 2026" class="w-full text-xs border border-slate-200 rounded p-1.5 mt-0.5 outline-none font-mono">
                            </div>
                        </div>
                        <div>
                            <label class="text-xs font-medium text-slate-600">Policy Content (HTML formatted)</label>
                            <textarea id="privacy-content" rows="6" class="w-full text-xs font-mono border border-slate-200 rounded p-2 outline-none"><h2>1. Information We Collect</h2><p>We respect your privacy and protect all client data.</p><h2>2. Security Controls</h2><p>Enterprise encryption at rest and in transit.</p><h2>3. Data Rights</h2><p>You can request your data records at any time.</p></textarea>
                        </div>
                    </div>
                    <button onclick="testUpdatePrivacy()" class="w-full bg-violet-600 hover:bg-violet-700 text-white font-medium text-xs py-2 rounded-lg transition shadow-sm">
                        <i class="fa-solid fa-floppy-disk mr-1"></i> Save Privacy Policy
                    </button>
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
                            <option value="team">team</option>
                            <option value="contact">contact</option>
                            <option value="testimonials">testimonials</option>
                            <option value="gallery">gallery</option>
                            <option value="career">career</option>
                            <option value="faq">faq</option>
                            <option value="terms">terms</option>
                            <option value="privacy">privacy</option>
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
            'topbarLayout':              { name: 'Top bar',            min: 0, max: 4, hint: 'layout0 (hidden) to layout4' },
            'navbarLayout':              { name: 'Navbar',             min: 1, max: 5, hint: 'layout1 to layout5' },
            'homePageLayout':            { name: 'Home',               min: 1, max: 10, hint: 'layout1 to layout10' },
            'aboutPageLayout':           { name: 'About',              min: 1, max: 5, hint: 'layout1 to layout5 (Classic Story, Modern Timeline, Bento Grid, Split Editorial, Minimal)' },
            'servicesPageLayout':        { name: 'Services catalog',   min: 1, max: 8, hint: 'layout1 to layout8' },
            'serviceDetailPageLayout':   { name: 'Service detail',     min: 1, max: 4, hint: 'layout1 to layout4' },
            'portfolioPageLayout':       { name: 'Portfolio catalog',  min: 1, max: 8, hint: 'layout1 to layout8 (Filterable Grid, Fullwidth Masonry, Interactive Carousel, Minimal Cards, Showcase, Bento, Split Stack)' },
            'portfolioDetailPageLayout': { name: 'Case study detail',  min: 1, max: 4, hint: 'layout1 to layout4 (Story Driven, Metrics & Impact, Visual Gallery, Sidebar Overview)' },
            'blogPageLayout':            { name: 'Blog catalog',       min: 1, max: 4, hint: 'layout1 to layout4 (Bento Grid, Classic Editorial, Minimalist Card, Full-width Media Feed)' },
            'blogDetailPageLayout':      { name: 'Blog article',       min: 1, max: 3, hint: 'layout1 to layout3 (Clean Editorial, Modern Hero with Sticky TOC, Media Centric)' },
            'eventsPageLayout':          { name: 'Events feed',        min: 1, max: 3, hint: 'layout1 to layout3' },
            'eventDetailPageLayout':     { name: 'Event detail',       min: 1, max: 4, hint: 'layout1 to layout4' },
            'teamPageLayout':            { name: 'Team',               min: 1, max: 5, hint: 'layout1 to layout5' },
            'testimonialsPageLayout':    { name: 'Testimonials',       min: 1, max: 5, hint: 'layout1 to layout5' },
            'galleryPageLayout':         { name: 'Gallery',            min: 1, max: 4, hint: 'layout1 to layout4' },
            'contactPageLayout':         { name: 'Contact',            min: 1, max: 4, hint: 'layout1 to layout4 (Split Form with Google Map, Modern Card with Social Hub, Corporate Multi-office, Minimal Quick Reach)' }, // Contact Page API
            'footerLayout':              { name: 'Footer',             min: 1, max: 4, hint: 'layout1 to layout4' },
            'careerPageLayout':          { name: 'Career',             min: 1, max: 4, hint: 'layout1 to layout4' },
            'faqPageLayout':             { name: 'FAQ',                min: 1, max: 4, hint: 'layout1 to layout4' },
            'termsPageLayout':           { name: 'Terms',              min: 1, max: 3, hint: 'layout1 to layout3' },
            'privacyPageLayout':         { name: 'Privacy',            min: 1, max: 3, hint: 'layout1 to layout3' }
        };

        let currentToken = localStorage.getItem('api_admin_token') || '';
        // Auto-detect Base URL from browser location (on localhost, always use local server)
        const isLocalHost = window.location.hostname === 'localhost' || window.location.hostname === '127.0.0.1';
        const configAppUrl = @json(rtrim(config('app.url') ?? '', '/'));
        const currentPath = window.location.pathname.replace(/\/api-tester\/?$/, '').replace(/\/+$/, '');
        const browserOriginUrl = (window.location.origin + (currentPath && currentPath !== '/' ? currentPath : '')).replace(/\/+$/, '');

        const autoBaseUrl = isLocalHost
            ? browserOriginUrl
            : ((configAppUrl && configAppUrl.includes('http')) ? configAppUrl : browserOriginUrl);

        let storedBaseUrl = localStorage.getItem('api_tester_base_url');
        // If testing on localhost, discard any stored remote URL
        if (isLocalHost && storedBaseUrl && !storedBaseUrl.includes('localhost') && !storedBaseUrl.includes('127.0.0.1')) {
            storedBaseUrl = null;
            localStorage.removeItem('api_tester_base_url');
        }

        const baseUrlEl = document.getElementById('baseUrlInput');
        baseUrlEl.value = storedBaseUrl || autoBaseUrl;
        baseUrlEl.addEventListener('input', (e) => {
            localStorage.setItem('api_tester_base_url', e.target.value.trim());
        });

        function resetBaseUrl() {
            localStorage.removeItem('api_tester_base_url');
            baseUrlEl.value = autoBaseUrl;
            alert(`Base URL reset to: ${autoBaseUrl}`);
        }

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
            const activeBtn = document.getElementById(`tab-${name}`);
            if (activeBtn) activeBtn.classList.add('active');

            // Hide all sections dynamically
            document.querySelectorAll('[id^="section-"]').forEach(el => el.classList.add('hidden'));

            // Show active section
            const activeSec = document.getElementById(`section-${name}`);
            if (activeSec) activeSec.classList.remove('hidden');

            // Reset page scroll to top so the module content is instantly visible
            window.scrollTo({ top: 0, behavior: 'instant' });
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
                const contentType = response.headers.get('content-type') || '';

                if (contentType.includes('application/json')) {
                    const data = await response.json();
                    renderResponse(response.status, response.statusText, duration, data);
                    return { status: response.status, data: data };
                }

                // If server returned non-JSON (e.g. HTML from a frontend SPA index.html or nginx error)
                const rawText = await response.text();
                const isHtml = rawText.trim().startsWith('<!doctype') || rawText.trim().startsWith('<html') || rawText.trim().startsWith('<');
                let hint = 'Response was not JSON.';
                if (isHtml) {
                    hint = `The server returned an HTML webpage instead of JSON from Laravel. You called: "${url}". If your Laravel backend is located in a subfolder (e.g. /services), set the Base URL input above to: ${window.location.origin}/services`;
                }

                renderResponse(response.status, isHtml ? `${response.status} (HTML Page Returned)` : response.statusText, duration, {
                    error: isHtml ? 'Received HTML webpage instead of Laravel API JSON response' : 'Non-JSON response',
                    status: response.status,
                    urlCalled: url,
                    hint: hint,
                    preview: rawText.substring(0, 400)
                });
                return { status: response.status, data: rawText };
            } catch (err) {
                const duration = Math.round(performance.now() - startTime);
                renderResponse(0, 'Network / Server Error', duration, {
                    error: err.message,
                    urlCalled: url,
                    hint: 'Request failed to reach endpoint. Check network, CORS, and Base URL.'
                });
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
        // ══════════════════════════════════════════════════════════
        // Module 1: Topbar Functions
        // ══════════════════════════════════════════════════════════
        async function testPublicTopbar() {
            await sendApi('GET', '/api/public/topbar');
        }

        async function testAdminTopbar() {
            await sendApi('GET', '/api/admin/topbar', null, true);
        }

        async function testUpdateTopbar() {
            const layout = document.getElementById('topbar-edit-layout').value;
            const enabled = document.getElementById('topbar-edit-enabled').value === 'true';
            const email = document.getElementById('topbar-edit-email').value;
            const phone = document.getElementById('topbar-edit-phone').value;

            const noticeBadge = document.getElementById('topbar-edit-notice-badge').value;
            const noticeText = document.getElementById('topbar-edit-notice-text').value;
            const noticeUrl = document.getElementById('topbar-edit-notice-url').value;
            const noticeEnabled = document.getElementById('topbar-edit-notice-enabled').checked;

            const ctaLabel = document.getElementById('topbar-edit-cta-label').value;
            const ctaUrl = document.getElementById('topbar-edit-cta-url').value;
            const ctaEnabled = document.getElementById('topbar-edit-cta-enabled').checked;

            const payload = {
                layout: layout,
                enabled: enabled,
                email: email,
                phone: phone,
                announcement: {
                    enabled: noticeEnabled,
                    badge: noticeBadge,
                    text: noticeText,
                    url: noticeUrl
                },
                cta: {
                    enabled: ctaEnabled,
                    label: ctaLabel,
                    url: ctaUrl
                }
            };

            await sendApi('PUT', '/api/admin/topbar', payload, true);
        }

        async function testToggleTopbar() {
            await sendApi('POST', '/api/admin/topbar/toggle', null, true);
        }

        async function testDeleteTopbar() {
            if (!confirm('Are you sure you want to disable and reset Topbar?')) return;
            await sendApi('DELETE', '/api/admin/topbar', null, true);
        }

        async function testFetchTopbarItems() {
            await sendApi('GET', '/api/admin/topbar/items', null, true);
        }

        function topbarPlatformChanged() {
            const plat = document.getElementById('topbar-add-platform').value;
            const labelInput = document.getElementById('topbar-add-label');
            const urlInput = document.getElementById('topbar-add-url');
            const defaultUrls = {
                facebook: 'https://facebook.com/nexgen',
                twitter: 'https://twitter.com/nexgen',
                instagram: 'https://instagram.com/nexgen',
                linkedin: 'https://linkedin.com/company/nexgen',
                youtube: 'https://youtube.com/@nexgen',
                github: 'https://github.com/nexgen',
                whatsapp: 'https://wa.me/919876543210',
                custom: '/about'
            };
            labelInput.value = plat.charAt(0).toUpperCase() + plat.slice(1);
            if (defaultUrls[plat]) urlInput.value = defaultUrls[plat];
        }

        async function testAddTopbarItem() {
            const platform = document.getElementById('topbar-add-platform').value;
            const label = document.getElementById('topbar-add-label').value;
            const url = document.getElementById('topbar-add-url').value;
            if (!url) return alert('Please enter destination URL');

            await sendApi('POST', '/api/admin/topbar/items', {
                platform: platform,
                label: label,
                url: url,
                enabled: true
            }, true);
        }

        async function testUpdateTopbarItem() {
            const id = document.getElementById('topbar-manage-id').value;
            if (!id) return alert('Please enter Item ID or platform name');
            const label = document.getElementById('topbar-manage-label').value;
            const url = document.getElementById('topbar-manage-url').value;

            const payload = {};
            if (label) payload.label = label;
            if (url) payload.url = url;

            await sendApi('PUT', `/api/admin/topbar/items/${id}`, payload, true);
        }

        async function testToggleTopbarItem() {
            const id = document.getElementById('topbar-manage-id').value;
            if (!id) return alert('Please enter Item ID or platform name');
            await sendApi('POST', `/api/admin/topbar/items/${id}/toggle`, null, true);
        }

        async function testDeleteTopbarItem() {
            const id = document.getElementById('topbar-manage-id').value;
            if (!id) return alert('Please enter Item ID or platform name');
            if (!confirm(`Delete topbar link '${id}'?`)) return;
            await sendApi('DELETE', `/api/admin/topbar/items/${id}`, null, true);
        }

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
            const parentId = document.getElementById('nav-item-add-parent').value.trim();
            if (!label || !url) return alert('Link name and URL are required.');
            const body = { label, url, enabled: true };
            if (parentId) body.parentId = parentId;
            await sendApi('POST', '/api/admin/navigation/navbar/items', body, true);
        }
        async function testBulkSaveNavbarTree() {
            let data;
            try {
                data = JSON.parse(document.getElementById('navbar-multi-tree-json').value);
            } catch(e) {
                return alert('Invalid JSON format for navbar hierarchy.');
            }
            await sendApi('PUT', '/api/admin/navigation/navbar/items', data, true);
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

        // About Page Module JS
        async function testPublicAbout() {
            await sendApi('GET', '/api/public/about');
        }

        async function testAdminAbout() {
            await sendApi('GET', '/api/admin/about', null, true);
        }

        async function testUpdateAboutLayout() {
            const templateId = document.getElementById('about-layout-select').value;
            await sendApi('PUT', '/api/admin/layouts/aboutPageLayout', { templateId }, true);
        }

        async function testUpdateAbout() {
            const payload = {
                badge: document.getElementById('about-badge').value,
                title: document.getElementById('about-title').value,
                tagline: document.getElementById('about-tagline').value,
                storyTitle: document.getElementById('about-story-title').value,
                storyContent: document.getElementById('about-story-content').value,
                mission: document.getElementById('about-mission').value,
                vision: document.getElementById('about-vision').value,
            };
            await sendApi('PUT', '/api/admin/about', payload, true);
        }

        async function testUpdateAboutImages() {
            const payload = {
                heroImage: document.getElementById('about-hero-img').value,
                heroImageAlt: document.getElementById('about-hero-alt').value,
                storyImage: document.getElementById('about-story-img').value,
                storyImageAlt: document.getElementById('about-story-alt').value,
            };
            await sendApi('PUT', '/api/admin/about/images', payload, true);
        }

        async function testResetAbout() {
            if (!confirm('Reset About page to default content and ALT attributes?')) return;
            await sendApi('POST', '/api/admin/about/reset', null, true);
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

        // Team Module JS
        async function testPublicTeam() {
            const dept = document.getElementById('team-pub-dept').value;
            const leadership = document.getElementById('team-pub-leadership').value;
            const limit = document.getElementById('team-pub-limit').value || 20;
            let path = `/api/public/team?limit=${limit}`;
            if (dept) path += `&department=${encodeURIComponent(dept)}`;
            if (leadership !== '') path += `&isLeadership=${encodeURIComponent(leadership)}`;
            await sendApi('GET', path);
        }
        async function testAdminTeam() {
            await sendApi('GET', '/api/admin/team', null, true);
        }
        async function testAdminTeamSingle() {
            const id = document.getElementById('team-admin-fetch-id').value;
            if (!id) return alert('Enter member ID.');
            await sendApi('GET', `/api/admin/team/${id}`, null, true);
        }
        async function testCreateTeamMember() {
            const isLeadership = document.getElementById('team-create-leadership').value === '1';
            const isPublished = document.getElementById('team-create-published').value === '1';
            const body = {
                name: document.getElementById('team-create-name').value,
                designation: document.getElementById('team-create-desig').value,
                department: document.getElementById('team-create-dept').value,
                photo: document.getElementById('team-create-photo').value,
                bio: document.getElementById('team-create-bio').value,
                social: {
                    linkedin: document.getElementById('team-create-linkedin').value,
                    twitter: document.getElementById('team-create-twitter').value,
                    github: document.getElementById('team-create-github').value,
                },
                isLeadership: isLeadership,
                order: parseInt(document.getElementById('team-create-order').value) || 0,
                isPublished: isPublished
            };
            await sendApi('POST', '/api/admin/team', body, true);
        }
        async function testUpdateTeamMember() {
            const id = document.getElementById('team-manage-id').value;
            const designation = document.getElementById('team-manage-desig').value;
            await sendApi('PUT', `/api/admin/team/${id}`, { designation }, true);
        }
        async function testDeleteTeamMember() {
            const id = document.getElementById('team-manage-id').value;
            if (!confirm(`Delete team member ID ${id}?`)) return;
            await sendApi('DELETE', `/api/admin/team/${id}`, null, true);
        }

        // ─── Contact Page API (Module 11) Functions ───
        async function testSubmitInquiry(botMode = false) {
            const name = document.getElementById('contact-name').value;
            const email = document.getElementById('contact-email').value;
            const phone = document.getElementById('contact-phone').value;
            const serviceInterested = document.getElementById('contact-service').value;
            const message = document.getElementById('contact-message').value;
            const website = botMode ? (document.getElementById('contact-website').value || 'http://spambot.xyz') : document.getElementById('contact-website').value;

            const body = {
                name,
                email,
                phone,
                serviceInterested,
                message,
                website
            };

            await sendApi('POST', '/api/public/contact/inquiry', body, false);
        }

        async function testSwitchContactLayout() {
            const layout = document.getElementById('contact-layout-select').value;
            await sendApi('PUT', '/api/admin/layouts/contactPageLayout', { layout }, true);
        }

        async function testListInquiries() {
            const status = document.getElementById('inquiries-filter-status').value;
            const q = document.getElementById('inquiries-filter-q').value;
            let path = '/api/admin/inquiries?';
            if (status) path += `status=${encodeURIComponent(status)}&`;
            if (q) path += `q=${encodeURIComponent(q)}&`;
            await sendApi('GET', path.replace(/[?&]$/, ''), null, true);
        }

        async function testGetSingleInquiry() {
            const id = document.getElementById('inquiry-id').value;
            await sendApi('GET', `/api/admin/inquiries/${id}`, null, true);
        }

        async function testUpdateInquiryStatus() {
            const id = document.getElementById('inquiry-id').value;
            const status = document.getElementById('inquiry-status-select').value;
            const adminNotes = document.getElementById('inquiry-notes').value;
            await sendApi('PUT', `/api/admin/inquiries/${id}`, { status, adminNotes }, true);
        }

        async function testDeleteInquiry() {
            const id = document.getElementById('inquiry-id').value;
            if (!confirm(`Delete inquiry #${id}?`)) return;
            await sendApi('DELETE', `/api/admin/inquiries/${id}`, null, true);
        }

        async function testGetContactPublic() {
            await sendApi('GET', '/api/public/contact');
        }

        async function testGetContactSeo() {
            await sendApi('GET', '/api/public/seo/contact');
        }

        async function testUpdateContactSeo() {
            const title = document.getElementById('contact-seo-title').value;
            const description = document.getElementById('contact-seo-desc').value;
            const keywords = document.getElementById('contact-seo-keywords').value;
            await sendApi('PUT', '/api/admin/seo/contact', {
                title,
                description,
                keywords,
                ogTitle: title,
                ogDescription: description
            }, true);
        }

        async function testSaveContactInfo() {
            let offices = [];
            let socials = {};
            try {
                offices = JSON.parse(document.getElementById('contact-info-offices').value);
            } catch (e) {
                return alert('Invalid JSON in Offices field: ' + e.message);
            }
            try {
                socials = JSON.parse(document.getElementById('contact-info-socials').value);
            } catch (e) {
                return alert('Invalid JSON in Socials field: ' + e.message);
            }

            const body = {
                contactInfo: {
                    email: document.getElementById('contact-info-email').value,
                    phone: document.getElementById('contact-info-phone').value,
                    address: document.getElementById('contact-info-address').value,
                    mapEmbedUrl: document.getElementById('contact-info-map').value,
                    offices: offices,
                    socials: socials
                }
            };

            await sendApi('PUT', '/api/admin/contact', body, true);
        }

        async function testFetchContactInfo() {
            const resp = await sendApi('GET', '/api/admin/modules/contact', null, true);
            if (resp && resp.data && resp.data.contactInfo) {
                const c = resp.data.contactInfo;
                if (c.email) document.getElementById('contact-info-email').value = c.email;
                if (c.phone) document.getElementById('contact-info-phone').value = c.phone;
                if (c.address) document.getElementById('contact-info-address').value = c.address;
                if (c.mapEmbedUrl) document.getElementById('contact-info-map').value = c.mapEmbedUrl;
                if (c.offices) document.getElementById('contact-info-offices').value = JSON.stringify(c.offices, null, 2);
                if (c.socials) document.getElementById('contact-info-socials').value = JSON.stringify(c.socials, null, 2);
            }
        }
        // ─── End Contact Page API Functions ───
        // Testimonials Module JS
        async function testPublicTestimonials() {
            const feat = document.getElementById('testim-pub-feat').value;
            const rating = document.getElementById('testim-pub-rating').value;
            const limit = document.getElementById('testim-pub-limit').value || 20;
            let path = `/api/public/testimonials?limit=${limit}`;
            if (feat !== '') path += `&featured=${encodeURIComponent(feat)}`;
            if (rating) path += `&rating=${encodeURIComponent(rating)}`;
            await sendApi('GET', path);
        }
        async function testAdminTestimonials() {
            await sendApi('GET', '/api/admin/testimonials', null, true);
        }
        async function testAdminTestimonialSingle() {
            const id = document.getElementById('testim-admin-fetch-id').value;
            if (!id) return alert('Enter review ID.');
            await sendApi('GET', `/api/admin/testimonials/${id}`, null, true);
        }
        async function testCreateTestimonial() {
            const isFeatured = document.getElementById('testim-create-feat').value === '1';
            const isPublished = document.getElementById('testim-create-published').value === '1';
            const body = {
                name: document.getElementById('testim-create-name').value,
                designation: document.getElementById('testim-create-desig').value,
                company: document.getElementById('testim-create-company').value,
                photo: document.getElementById('testim-create-photo').value,
                rating: parseInt(document.getElementById('testim-create-rating').value) || 5,
                text: document.getElementById('testim-create-text').value,
                isFeatured: isFeatured,
                videoUrl: document.getElementById('testim-create-videourl').value || '',
                metric: {
                    label: document.getElementById('testim-create-metric-label').value || '',
                    value: document.getElementById('testim-create-metric-val').value || '',
                },
                order: parseInt(document.getElementById('testim-create-order').value) || 0,
                isPublished: isPublished
            };
            await sendApi('POST', '/api/admin/testimonials', body, true);
        }
        async function testUpdateTestimonial() {
            const id = document.getElementById('testim-manage-id').value;
            const rating = parseInt(document.getElementById('testim-manage-rating').value) || 5;
            const isFeatured = document.getElementById('testim-manage-feat').value === '1';
            await sendApi('PUT', `/api/admin/testimonials/${id}`, { rating, isFeatured }, true);
        }
        async function testDeleteTestimonial() {
            const id = document.getElementById('testim-manage-id').value;
            if (!confirm(`Delete review ID ${id}?`)) return;
            await sendApi('DELETE', `/api/admin/testimonials/${id}`, null, true);
        }

        // Gallery Module JS
        async function testPublicGallery() {
            const cat = document.getElementById('gallery-pub-cat').value;
            const limit = document.getElementById('gallery-pub-limit').value || 20;
            let path = `/api/public/gallery?limit=${limit}`;
            if (cat) path += `&category=${encodeURIComponent(cat)}`;
            await sendApi('GET', path);
        }
        async function testAdminGallery() {
            await sendApi('GET', '/api/admin/gallery', null, true);
        }
        async function testAdminGallerySingle() {
            const id = document.getElementById('gallery-admin-fetch-id').value;
            if (!id) return alert('Enter album ID.');
            await sendApi('GET', `/api/admin/gallery/${id}`, null, true);
        }
        async function testCreateGallery() {
            let images = [];
            try {
                images = JSON.parse(document.getElementById('gallery-create-images').value);
            } catch (e) {
                return alert('Invalid images JSON format.');
            }
            const isPublished = document.getElementById('gallery-create-published').value === '1';
            const body = {
                name: document.getElementById('gallery-create-name').value,
                category: document.getElementById('gallery-create-cat').value,
                description: document.getElementById('gallery-create-desc').value,
                coverImage: document.getElementById('gallery-create-cover').value,
                images: images,
                isPublished: isPublished
            };
            await sendApi('POST', '/api/admin/gallery', body, true);
        }
        async function testUpdateGallery() {
            const id = document.getElementById('gallery-manage-id').value;
            const name = document.getElementById('gallery-manage-name').value;
            await sendApi('PUT', `/api/admin/gallery/${id}`, { name }, true);
        }
        async function testDeleteGallery() {
            const id = document.getElementById('gallery-manage-id').value;
            if (!confirm(`Delete album ID ${id}?`)) return;
            await sendApi('DELETE', `/api/admin/gallery/${id}`, null, true);
        }

        // Careers Module JS
        async function testPublicCareers() {
            const dept = document.getElementById('career-pub-dept').value;
            const type = document.getElementById('career-pub-type').value;
            const q = document.getElementById('career-pub-search').value;
            const limit = document.getElementById('career-pub-limit').value || 20;
            let path = `/api/public/careers?limit=${limit}`;
            if (dept) path += `&department=${encodeURIComponent(dept)}`;
            if (type) path += `&type=${encodeURIComponent(type)}`;
            if (q) path += `&q=${encodeURIComponent(q)}`;
            await sendApi('GET', path);
        }
        async function testPublicCareerDetail() {
            const slug = document.getElementById('career-pub-slug').value.trim();
            if (!slug) return alert('Enter job slug.');
            await sendApi('GET', `/api/public/careers/${slug}`);
        }
        async function testAdminCareers() {
            const status = document.getElementById('career-admin-status').value;
            const dept = document.getElementById('career-admin-dept').value;
            let path = '/api/admin/careers';
            const params = [];
            if (status) params.push(`status=${encodeURIComponent(status)}`);
            if (dept) params.push(`department=${encodeURIComponent(dept)}`);
            if (params.length) path += '?' + params.join('&');
            await sendApi('GET', path, null, true);
        }
        async function testAdminCareerSingle() {
            const id = document.getElementById('career-admin-fetch-id').value;
            if (!id) return alert('Enter career ID.');
            await sendApi('GET', `/api/admin/careers/${id}`, null, true);
        }
        async function testCreateCareer() {
            let reqs = [];
            const reqsRaw = document.getElementById('career-create-reqs').value.trim();
            if (reqsRaw.startsWith('[')) {
                try { reqs = JSON.parse(reqsRaw); } catch(e) { return alert('Invalid JSON array for requirements.'); }
            } else if (reqsRaw) {
                reqs = reqsRaw.split('\n').map(s => s.trim()).filter(Boolean);
            }
            const body = {
                title: document.getElementById('career-create-title').value,
                department: document.getElementById('career-create-dept').value,
                location: document.getElementById('career-create-loc').value,
                type: document.getElementById('career-create-type').value,
                experience: document.getElementById('career-create-exp').value,
                description: document.getElementById('career-create-desc').value,
                requirements: reqs,
                applyUrl: document.getElementById('career-create-url').value,
                status: document.getElementById('career-create-status').value
            };
            await sendApi('POST', '/api/admin/careers', body, true);
        }
        async function testUpdateCareer() {
            const id = document.getElementById('career-manage-id').value;
            if (!id) return alert('Enter career ID to update.');
            const body = {};
            const title = document.getElementById('career-manage-title').value.trim();
            const status = document.getElementById('career-manage-status').value;
            const exp = document.getElementById('career-manage-exp').value.trim();
            if (title) body.title = title;
            if (status) body.status = status;
            if (exp) body.experience = exp;
            await sendApi('PUT', `/api/admin/careers/${id}`, body, true);
        }
        async function testDeleteCareer() {
            const id = document.getElementById('career-manage-id').value;
            if (!id) return alert('Enter career ID to delete.');
            if (!confirm(`Delete career opening ID ${id}?`)) return;
            await sendApi('DELETE', `/api/admin/careers/${id}`, null, true);
        }

        // FAQs Module JS
        async function testPublicFaqs() {
            const cat = document.getElementById('faq-pub-cat').value;
            const q = document.getElementById('faq-pub-q').value;
            let path = '/api/public/faqs';
            const params = [];
            if (cat) params.push(`category=${encodeURIComponent(cat)}`);
            if (q) params.push(`q=${encodeURIComponent(q)}`);
            if (params.length) path += '?' + params.join('&');
            await sendApi('GET', path);
        }
        async function testAdminFaqs() {
            const cat = document.getElementById('faq-admin-cat').value;
            let path = '/api/admin/faqs';
            if (cat) path += `?category=${encodeURIComponent(cat)}`;
            await sendApi('GET', path, null, true);
        }
        async function testAdminFaqSingle() {
            const id = document.getElementById('faq-admin-fetch-id').value;
            if (!id) return alert('Enter FAQ ID.');
            await sendApi('GET', `/api/admin/faqs/${id}`, null, true);
        }
        async function testCreateFaq() {
            const body = {
                question: document.getElementById('faq-create-q').value,
                answer: document.getElementById('faq-create-a').value,
                category: document.getElementById('faq-create-cat').value,
                order: parseInt(document.getElementById('faq-create-order').value || '1'),
                isPublished: document.getElementById('faq-create-published').value === '1'
            };
            await sendApi('POST', '/api/admin/faqs/item', body, true);
        }
        async function testBulkSaveFaqs() {
            let data;
            try {
                data = JSON.parse(document.getElementById('faq-bulk-json').value);
            } catch(e) {
                return alert('Invalid JSON in bulk FAQs payload.');
            }
            await sendApi('PUT', '/api/admin/faqs', data, true);
        }
        async function testUpdateFaq() {
            const id = document.getElementById('faq-manage-id').value;
            if (!id) return alert('Enter FAQ ID.');
            const question = document.getElementById('faq-manage-q').value;
            const category = document.getElementById('faq-manage-cat').value;
            const isPublished = document.getElementById('faq-manage-pub').value === '1';
            await sendApi('PUT', `/api/admin/faqs/${id}`, { question, category, isPublished }, true);
        }
        async function testDeleteFaq() {
            const id = document.getElementById('faq-manage-id').value;
            if (!id) return alert('Enter FAQ ID.');
            if (!confirm(`Delete FAQ ID ${id}?`)) return;
            await sendApi('DELETE', `/api/admin/faqs/${id}`, null, true);
        }

        // Terms of Service JS
        async function testPublicTerms() { await sendApi('GET', '/api/public/terms'); }
        async function testAdminTerms() { await sendApi('GET', '/api/admin/terms', null, true); }
        async function testUpdateTerms() {
            const title = document.getElementById('terms-title').value;
            const lastUpdated = document.getElementById('terms-last-updated').value;
            const content = document.getElementById('terms-content').value;
            await sendApi('PUT', '/api/admin/terms', { title, lastUpdated, content }, true);
        }

        // Privacy Policy JS
        async function testPublicPrivacy() { await sendApi('GET', '/api/public/privacy'); }
        async function testAdminPrivacy() { await sendApi('GET', '/api/admin/privacy', null, true); }
        async function testUpdatePrivacy() {
            const title = document.getElementById('privacy-title').value;
            const lastUpdated = document.getElementById('privacy-last-updated').value;
            const content = document.getElementById('privacy-content').value;
            await sendApi('PUT', '/api/admin/privacy', { title, lastUpdated, content }, true);
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
