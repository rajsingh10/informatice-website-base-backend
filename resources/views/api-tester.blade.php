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
                    <p class="text-xs text-slate-500">All Modules: Auth, Layouts, SEO, Media, Settings</p>
                </div>
            </div>

            <!-- Token Status & Base URL -->
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

                    <button onclick="switchTab('layouts')" id="tab-layouts" class="tab-btn w-full flex items-center justify-between px-3 py-2 text-sm font-medium rounded-lg text-left text-slate-600 hover:bg-slate-50 transition">
                        <span class="flex items-center space-x-2.5">
                            <i class="fa-solid fa-table-columns text-emerald-500"></i>
                            <span>Layouts</span>
                        </span>
                        <span class="text-xs bg-emerald-100 text-emerald-700 px-2 py-0.5 rounded-full font-bold">4</span>
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

                <!-- Active Token Info Box -->
                <div class="mt-6 pt-4 border-t border-slate-100">
                    <div class="flex items-center justify-between mb-1.5">
                        <label class="text-xs font-semibold text-slate-600">Bearer Token</label>
                        <button onclick="copyToken()" class="text-xs text-blue-600 hover:underline">Copy</button>
                    </div>
                    <textarea id="tokenDisplay" rows="3" readonly class="w-full text-[10px] font-mono bg-slate-50 border border-slate-200 rounded p-2 text-slate-600 resize-none outline-none" placeholder="Login to receive token automatically..."></textarea>
                </div>

                <!-- Quick Seed Credentials Helper -->
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
                    <p class="text-xs text-slate-600 mb-3">Retrieve current authenticated admin's profile.</p>
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
                    <p class="text-xs text-slate-600 mb-3">Update name, email, phone, bio.</p>
                    <div class="grid grid-cols-2 gap-2 mb-3">
                        <div>
                            <label class="text-xs font-medium text-slate-600">Name</label>
                            <input id="profile-name" type="text" value="Super Admin" class="w-full text-xs border border-slate-200 rounded p-1.5 mt-1 outline-none">
                        </div>
                        <div>
                            <label class="text-xs font-medium text-slate-600">Email</label>
                            <input id="profile-email" type="email" value="admin@admin.com" class="w-full text-xs border border-slate-200 rounded p-1.5 mt-1 outline-none">
                        </div>
                        <div>
                            <label class="text-xs font-medium text-slate-600">Phone</label>
                            <input id="profile-phone" type="text" placeholder="+1234567890" class="w-full text-xs border border-slate-200 rounded p-1.5 mt-1 outline-none">
                        </div>
                        <div>
                            <label class="text-xs font-medium text-slate-600">Bio</label>
                            <input id="profile-bio" type="text" placeholder="Short bio..." class="w-full text-xs border border-slate-200 rounded p-1.5 mt-1 outline-none">
                        </div>
                    </div>
                    <button onclick="testUpdateProfile()" class="w-full bg-slate-800 hover:bg-slate-900 text-white font-medium text-xs py-2 rounded-lg transition">
                        <i class="fa-solid fa-id-card"></i> Send PUT /profile
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
                    <p class="text-xs text-slate-600 mb-3">Change password (revokes token).</p>
                    <div class="space-y-2 mb-3">
                        <div>
                            <label class="text-xs font-medium text-slate-600">Current Password</label>
                            <input id="pwd-current" type="password" value="Admin@1234" class="w-full text-xs border border-slate-200 rounded p-1.5 mt-1 outline-none">
                        </div>
                        <div class="grid grid-cols-2 gap-2">
                            <div>
                                <label class="text-xs font-medium text-slate-600">New Password</label>
                                <input id="pwd-new" type="password" value="Admin@1234" class="w-full text-xs border border-slate-200 rounded p-1.5 mt-1 outline-none">
                            </div>
                            <div>
                                <label class="text-xs font-medium text-slate-600">Confirm Password</label>
                                <input id="pwd-confirm" type="password" value="Admin@1234" class="w-full text-xs border border-slate-200 rounded p-1.5 mt-1 outline-none">
                            </div>
                        </div>
                    </div>
                    <button onclick="testChangePassword()" class="w-full bg-slate-800 hover:bg-slate-900 text-white font-medium text-xs py-2 rounded-lg transition">
                        <i class="fa-solid fa-key"></i> Send PUT /change-password
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
                    <p class="text-xs text-slate-600 mb-3">Invalidate current admin access token.</p>
                    <button onclick="testLogout()" class="w-full bg-red-600 hover:bg-red-700 text-white font-medium text-xs py-2 rounded-lg transition">
                        <i class="fa-solid fa-right-from-bracket"></i> Send POST /logout
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
                            <span class="font-mono text-sm font-semibold text-slate-800">/public/layouts</span>
                        </div>
                        <span class="text-xs text-slate-400">Public (No Auth)</span>
                    </div>
                    <p class="text-xs text-slate-600 mb-3">Fetch all active layouts in one object.</p>
                    <button onclick="testPublicLayouts()" class="w-full bg-emerald-600 hover:bg-emerald-700 text-white font-medium text-xs py-2.5 rounded-lg transition shadow-sm flex items-center justify-center space-x-2">
                        <i class="fa-solid fa-arrows-rotate"></i>
                        <span>Fetch All Active Layouts</span>
                    </button>
                </div>

                <div class="bg-white rounded-xl border border-slate-200 shadow-sm p-4">
                    <div class="flex items-center justify-between mb-3">
                        <div class="flex items-center space-x-2">
                            <span class="method-badge badge-put">PUT</span>
                            <span class="font-mono text-sm font-semibold text-slate-800">/admin/layouts/:key</span>
                        </div>
                        <span class="text-xs bg-slate-100 text-slate-600 px-2 py-0.5 rounded font-medium">Auth Required</span>
                    </div>
                    <p class="text-xs text-slate-600 mb-3">Set active template for one key. Validates against allowed range (returns 422 if invalid).</p>
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
                        <i class="fa-solid fa-pen-to-square"></i> Send PUT /admin/layouts/:key
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
                    <p class="text-xs text-slate-600 mb-3">Batch save / update multiple layouts in a single JSON payload.</p>
                    <textarea id="batch-save-json" rows="4" class="w-full text-xs font-mono border border-slate-200 rounded p-2 mb-3 outline-none">{
  "topbarLayout": "layout2",
  "navbarLayout": "layout1",
  "homePageLayout": "layout3"
}</textarea>
                    <button onclick="testBatchSaveLayouts()" class="w-full bg-slate-800 hover:bg-slate-900 text-white font-medium text-xs py-2 rounded-lg transition">
                        <i class="fa-solid fa-floppy-disk"></i> Send POST /admin/layouts/save
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
                            <span class="font-mono text-sm font-semibold text-slate-800">/public/seo/:page</span>
                        </div>
                        <span class="text-xs text-slate-400">Public (No Auth)</span>
                    </div>
                    <p class="text-xs text-slate-600 mb-3">Read SEO & image ALT map for a page.</p>
                    <div class="flex space-x-2 mb-3">
                        <select id="seo-public-page" class="flex-1 text-xs border border-slate-200 rounded p-2 bg-white outline-none">
                            <option value="home">home</option>
                            <option value="services">services</option>
                            <option value="service-detail">service-detail</option>
                            <option value="portfolio">portfolio</option>
                            <option value="portfolio-detail">portfolio-detail</option>
                            <option value="blog">blog</option>
                            <option value="blog-detail">blog-detail</option>
                            <option value="events">events</option>
                            <option value="event-detail">event-detail</option>
                            <option value="team">team</option>
                            <option value="contact">contact</option>
                            <option value="career">career</option>
                            <option value="testimonials">testimonials</option>
                            <option value="gallery">gallery</option>
                            <option value="faq">faq</option>
                            <option value="terms">terms</option>
                            <option value="privacy">privacy</option>
                        </select>
                        <button onclick="testPublicSeo()" class="bg-purple-600 hover:bg-purple-700 text-white px-4 py-2 rounded-lg text-xs font-medium">
                            <i class="fa-solid fa-search"></i> Read Public SEO
                        </button>
                    </div>
                </div>

                <div class="bg-white rounded-xl border border-slate-200 shadow-sm p-4">
                    <div class="flex items-center justify-between mb-3">
                        <div class="flex items-center space-x-2">
                            <span class="method-badge badge-put">PUT</span>
                            <span class="font-mono text-sm font-semibold text-slate-800">/admin/seo/:page</span>
                        </div>
                        <span class="text-xs bg-slate-100 text-slate-600 px-2 py-0.5 rounded font-medium">Auth Required</span>
                    </div>
                    <p class="text-xs text-slate-600 mb-3">Replace SEO metadata & image ALT mapping for a page.</p>

                    <div class="space-y-2 mb-3">
                        <div>
                            <label class="text-xs font-medium text-slate-600">Select Page</label>
                            <select id="seo-admin-page" class="w-full text-xs border border-slate-200 rounded p-1.5 mt-1 bg-white outline-none">
                                <option value="home">home</option>
                                <option value="services">services</option>
                                <option value="service-detail">service-detail</option>
                                <option value="portfolio">portfolio</option>
                                <option value="portfolio-detail">portfolio-detail</option>
                                <option value="blog">blog</option>
                                <option value="blog-detail">blog-detail</option>
                                <option value="events">events</option>
                                <option value="event-detail">event-detail</option>
                                <option value="team">team</option>
                                <option value="contact">contact</option>
                                <option value="career">career</option>
                                <option value="testimonials">testimonials</option>
                                <option value="gallery">gallery</option>
                                <option value="faq">faq</option>
                                <option value="terms">terms</option>
                                <option value="privacy">privacy</option>
                            </select>
                        </div>
                        <div>
                            <div class="flex justify-between">
                                <label class="text-xs font-medium text-slate-600">Title</label>
                                <span class="text-[10px] text-slate-400">max 70 chars</span>
                            </div>
                            <input id="seo-title" type="text" maxlength="70" value="NexGen Solutions - Transforming Ideas Into Digital Reality" class="w-full text-xs border border-slate-200 rounded p-1.5 mt-0.5 outline-none">
                        </div>
                        <div>
                            <div class="flex justify-between">
                                <label class="text-xs font-medium text-slate-600">Description</label>
                                <span class="text-[10px] text-slate-400">max 160 chars</span>
                            </div>
                            <input id="seo-desc" type="text" maxlength="160" value="Empowering businesses through cutting-edge digital experiences." class="w-full text-xs border border-slate-200 rounded p-1.5 mt-0.5 outline-none">
                        </div>
                        <div>
                            <label class="text-xs font-medium text-slate-600">Keywords (comma-separated)</label>
                            <input id="seo-keywords" type="text" value="web development, mobile apps, enterprise cloud" class="w-full text-xs border border-slate-200 rounded p-1.5 mt-0.5 outline-none">
                        </div>
                        <div class="grid grid-cols-2 gap-2">
                            <div>
                                <label class="text-xs font-medium text-slate-600">OG Title</label>
                                <input id="seo-ogtitle" type="text" value="NexGen Solutions" class="w-full text-xs border border-slate-200 rounded p-1.5 mt-0.5 outline-none">
                            </div>
                            <div>
                                <label class="text-xs font-medium text-slate-600">OG Image URL</label>
                                <input id="seo-ogimage" type="text" value="https://example.com/og.jpg" class="w-full text-xs border border-slate-200 rounded p-1.5 mt-0.5 outline-none">
                            </div>
                        </div>
                        <div>
                            <label class="text-xs font-medium text-slate-600">Canonical URL</label>
                            <input id="seo-canonical" type="text" value="https://example.com/" class="w-full text-xs border border-slate-200 rounded p-1.5 mt-0.5 outline-none">
                        </div>
                        <div>
                            <label class="text-xs font-medium text-slate-600">Image ALT Map (JSON slot IDs -> ALT texts)</label>
                            <textarea id="seo-altmap" rows="3" class="w-full text-xs font-mono border border-slate-200 rounded p-1.5 mt-0.5 outline-none">{
  "img-home-hero": "Modern digital workspace with AI dashboard",
  "img-home-about": "Collaborative software engineering team"
}</textarea>
                        </div>
                    </div>

                    <div class="grid grid-cols-2 gap-2">
                        <button onclick="testAdminReadSeo()" class="bg-slate-700 hover:bg-slate-800 text-white font-medium text-xs py-2 rounded-lg transition">
                            <i class="fa-solid fa-eye"></i> GET Admin Read
                        </button>
                        <button onclick="testUpdateSeo()" class="bg-purple-600 hover:bg-purple-700 text-white font-medium text-xs py-2 rounded-lg transition">
                            <i class="fa-solid fa-cloud-arrow-up"></i> PUT Replace SEO
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
                            <span class="font-mono text-sm font-semibold text-slate-800">/admin/uploads</span>
                        </div>
                        <span class="text-xs bg-slate-100 text-slate-600 px-2 py-0.5 rounded font-medium">Auth Required</span>
                    </div>
                    <p class="text-xs text-slate-600 mb-3">Upload image file (JPEG, PNG, WebP, SVG, GIF, max 5 MB). Returns permanent URL & dimensions.</p>

                    <div class="mb-3">
                        <input id="media-file-input" type="file" accept="image/*" class="w-full text-xs border border-slate-200 rounded p-2 outline-none bg-slate-50">
                    </div>

                    <button onclick="testMediaUpload()" class="w-full bg-pink-600 hover:bg-pink-700 text-white font-medium text-xs py-2.5 rounded-lg transition shadow-sm flex items-center justify-center space-x-2">
                        <i class="fa-solid fa-upload"></i>
                        <span>Upload Image File</span>
                    </button>
                </div>

                <div class="bg-white rounded-xl border border-slate-200 shadow-sm p-4">
                    <div class="flex items-center justify-between mb-3">
                        <div class="flex items-center space-x-2">
                            <span class="method-badge badge-del">DELETE</span>
                            <span class="font-mono text-sm font-semibold text-slate-800">/admin/uploads/:id</span>
                        </div>
                        <span class="text-xs bg-slate-100 text-slate-600 px-2 py-0.5 rounded font-medium">Auth Required</span>
                    </div>
                    <p class="text-xs text-slate-600 mb-3">Remove an uploaded file from server disk and database.</p>

                    <div class="flex space-x-2 mb-3">
                        <input id="delete-file-id" type="text" placeholder="e.g. f_91ab" class="flex-1 text-xs border border-slate-200 rounded p-2 outline-none font-mono">
                        <button onclick="testMediaDelete()" class="bg-red-600 hover:bg-red-700 text-white font-medium text-xs px-4 py-2 rounded-lg transition">
                            <i class="fa-solid fa-trash-can"></i> Delete
                        </button>
                    </div>
                </div>
            </div>

            <!-- ══════════════════════════════════════════════════════════ -->
            <!-- 5. SITE SETTINGS APIS -->
            <!-- ══════════════════════════════════════════════════════════ -->
            <div id="section-settings" class="space-y-4 hidden">
                <div class="bg-white rounded-xl border border-slate-200 shadow-sm p-4">
                    <div class="flex items-center justify-between mb-3">
                        <div class="flex items-center space-x-2">
                            <span class="method-badge badge-get">GET</span>
                            <span class="font-mono text-sm font-semibold text-slate-800">/public/settings</span>
                        </div>
                        <span class="text-xs text-slate-400">Public (No Auth)</span>
                    </div>
                    <p class="text-xs text-slate-600 mb-3">Fetch site-wide details (contacts, logo, offices, socials, announcement).</p>
                    <button onclick="testPublicSettings()" class="w-full bg-amber-600 hover:bg-amber-700 text-white font-medium text-xs py-2.5 rounded-lg transition shadow-sm flex items-center justify-center space-x-2">
                        <i class="fa-solid fa-sliders"></i>
                        <span>Fetch Site Settings</span>
                    </button>
                </div>

                <div class="bg-white rounded-xl border border-slate-200 shadow-sm p-4">
                    <div class="flex items-center justify-between mb-3">
                        <div class="flex items-center space-x-2">
                            <span class="method-badge badge-put">PUT</span>
                            <span class="font-mono text-sm font-semibold text-slate-800">/admin/settings</span>
                        </div>
                        <span class="text-xs bg-slate-100 text-slate-600 px-2 py-0.5 rounded font-medium">Auth Required</span>
                    </div>
                    <p class="text-xs text-slate-600 mb-3">Update site-wide details (JSON payload).</p>

                    <textarea id="settings-json" rows="12" class="w-full text-xs font-mono border border-slate-200 rounded p-2 mb-3 outline-none">{
  "siteName": "NexGen Solutions",
  "logoUrl": "https://example.com/logo.png",
  "faviconUrl": "https://example.com/favicon.ico",
  "email": "contact@nexgensolutions.com",
  "phone": "+1 (555) 019-2834",
  "address": "100 Innovation Way, Silicon Valley, CA",
  "mapEmbedUrl": "https://maps.google.com/embed?pb=...",
  "offices": [
    { "name": "Global HQ", "address": "100 Innovation Way", "phone": "+1-555-019-2834", "email": "hq@nexgen.com" }
  ],
  "socials": {
    "facebook": "https://facebook.com/nexgen",
    "instagram": "https://instagram.com/nexgen",
    "linkedin": "https://linkedin.com/company/nexgen",
    "twitter": "https://twitter.com/nexgen",
    "youtube": "https://youtube.com/@nexgen"
  },
  "announcement": {
    "enabled": true,
    "text": "🎉 Big announcement: our next-gen platform is now live!",
    "url": "https://nexgensolutions.com/explore"
  }
}</textarea>
                    <button onclick="testUpdateSettings()" class="w-full bg-slate-800 hover:bg-slate-900 text-white font-medium text-xs py-2 rounded-lg transition">
                        <i class="fa-solid fa-floppy-disk"></i> Send PUT /admin/settings
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
            'servicesPageLayout':        { name: 'Services catalog',   min: 1, max: 8, hint: 'layout1 to layout8' },
            'serviceDetailPageLayout':   { name: 'Service detail',     min: 1, max: 4, hint: 'layout1 to layout4' },
            'portfolioPageLayout':       { name: 'Portfolio catalog',  min: 1, max: 8, hint: 'layout1 to layout8' },
            'portfolioDetailPageLayout': { name: 'Case study detail',  min: 1, max: 4, hint: 'layout1 to layout4' },
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

            ['auth', 'layouts', 'seo', 'media', 'settings'].forEach(s => {
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
                options.body = body; // Let browser set boundary automatically
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
            const res = await sendApi('POST', '/api/admin/login', { email, password });
            if (res && res.data && res.data.token) {
                currentToken = res.data.token;
                localStorage.setItem('api_admin_token', currentToken);
                updateTokenDisplay();
            }
        }
        async function testMe() { await sendApi('GET', '/api/admin/me', null, true); }
        async function testUpdateProfile() {
            await sendApi('PUT', '/api/admin/profile', {
                name: document.getElementById('profile-name').value,
                email: document.getElementById('profile-email').value,
                phone: document.getElementById('profile-phone').value,
                bio: document.getElementById('profile-bio').value
            }, true);
        }
        async function testChangePassword() {
            await sendApi('PUT', '/api/admin/change-password', {
                current_password: document.getElementById('pwd-current').value,
                password: document.getElementById('pwd-new').value,
                password_confirmation: document.getElementById('pwd-confirm').value
            }, true);
        }
        async function testLogout() {
            await sendApi('POST', '/api/admin/logout', {}, true);
            clearStoredToken();
        }

        // Layouts
        async function testPublicLayouts() { await sendApi('GET', '/public/layouts'); }
        async function testUpdateLayout() {
            const key = document.getElementById('layout-key-select').value;
            const templateId = document.getElementById('layout-template-select').value;
            await sendApi('PUT', `/admin/layouts/${key}`, { templateId }, true);
        }
        async function testBatchSaveLayouts() {
            try {
                const body = JSON.parse(document.getElementById('batch-save-json').value);
                await sendApi('POST', '/api/admin/layouts/save', body, true);
            } catch (e) { alert('Invalid JSON: ' + e.message); }
        }

        // SEO
        async function testPublicSeo() {
            const page = document.getElementById('seo-public-page').value;
            await sendApi('GET', `/public/seo/${page}`);
        }
        async function testAdminReadSeo() {
            const page = document.getElementById('seo-admin-page').value;
            await sendApi('GET', `/admin/seo/${page}`, null, true);
        }
        async function testUpdateSeo() {
            const page = document.getElementById('seo-admin-page').value;
            let imageAltMap = {};
            try {
                imageAltMap = JSON.parse(document.getElementById('seo-altmap').value);
            } catch (e) {
                return alert('Invalid JSON in Image ALT Map: ' + e.message);
            }

            const body = {
                title: document.getElementById('seo-title').value,
                description: document.getElementById('seo-desc').value,
                keywords: document.getElementById('seo-keywords').value,
                ogTitle: document.getElementById('seo-ogtitle').value,
                ogDescription: document.getElementById('seo-desc').value,
                ogImage: document.getElementById('seo-ogimage').value,
                canonicalUrl: document.getElementById('seo-canonical').value,
                robots: "index, follow",
                imageAltMap: imageAltMap
            };
            await sendApi('PUT', `/admin/seo/${page}`, body, true);
        }

        // Media Upload
        async function testMediaUpload() {
            const fileInput = document.getElementById('media-file-input');
            if (!fileInput.files.length) return alert('Please select an image file first.');

            const formData = new FormData();
            formData.append('file', fileInput.files[0]);

            const res = await sendApi('POST', '/admin/uploads', formData, true, true);
            if (res && res.data && res.data.data && res.data.data.id) {
                document.getElementById('delete-file-id').value = res.data.data.id;
            }
        }
        async function testMediaDelete() {
            const id = document.getElementById('delete-file-id').value.trim();
            if (!id) return alert('Enter file ID to delete.');
            await sendApi('DELETE', `/admin/uploads/${id}`, null, true);
        }

        // Settings
        async function testPublicSettings() { await sendApi('GET', '/public/settings'); }
        async function testUpdateSettings() {
            try {
                const body = JSON.parse(document.getElementById('settings-json').value);
                await sendApi('PUT', '/admin/settings', body, true);
            } catch (e) { alert('Invalid JSON in settings: ' + e.message); }
        }

        initLayoutSelects();
        updateTokenDisplay();
    </script>
</body>
</html>
