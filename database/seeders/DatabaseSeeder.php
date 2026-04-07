<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Product;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // ─── Admin User ────────────────────────────────────────────────────────
        User::updateOrCreate(
            ["email" => "admin@shurizastore.com"],
            [
                "name" => "Admin Shuriza",
                "email" => "admin@shurizastore.com",
                "password" => Hash::make("admin123"),
                "role" => "admin",
                "email_verified_at" => now(),
            ],
        );

        // ─── Test User ─────────────────────────────────────────────────────────
        User::updateOrCreate(
            ["email" => "user@example.com"],
            [
                "name" => "User Test",
                "email" => "user@example.com",
                "password" => Hash::make("password"),
                "role" => "user",
                "email_verified_at" => now(),
            ],
        );

        // ─── Categories ────────────────────────────────────────────────────────
        $categories = [
            [
                "name" => "Streaming",
                "slug" => "streaming",
                "icon" => "fas fa-play-circle",
                "color" => "#e50914",
                "description" =>
                    "Layanan streaming musik, film, dan konten digital.",
                "sort_order" => 1,
            ],
            [
                "name" => "Desain",
                "slug" => "desain",
                "icon" => "fas fa-palette",
                "color" => "#7c3aed",
                "description" =>
                    "Aplikasi dan tools untuk desain grafis profesional.",
                "sort_order" => 2,
            ],
            [
                "name" => "Produktivitas",
                "slug" => "produktivitas",
                "icon" => "fas fa-briefcase",
                "color" => "#0ea5e9",
                "description" =>
                    "Aplikasi produktivitas untuk bekerja lebih efisien.",
                "sort_order" => 3,
            ],
            [
                "name" => "Gaming",
                "slug" => "gaming",
                "icon" => "fas fa-gamepad",
                "color" => "#16a34a",
                "description" =>
                    "Voucher game, top up, dan akun gaming premium.",
                "sort_order" => 4,
            ],
            [
                "name" => "Jasa Digital",
                "slug" => "jasa",
                "icon" => "fas fa-hands-helping",
                "color" => "#f59e0b",
                "description" =>
                    "Jasa pembuatan website, desain, dan layanan digital lainnya.",
                "sort_order" => 5,
            ],
            [
                "name" => "Edukasi",
                "slug" => "edukasi",
                "icon" => "fas fa-graduation-cap",
                "color" => "#06b6d4",
                "description" =>
                    "Kursus online, e-book, dan materi belajar digital.",
                "sort_order" => 6,
            ],
            [
                "name" => "Voucher",
                "slug" => "voucher",
                "icon" => "fas fa-ticket-alt",
                "color" => "#ec4899",
                "description" =>
                    "Voucher belanja, pulsa, dan layanan digital lainnya.",
                "sort_order" => 7,
            ],
            [
                "name" => "AI Tools",
                "slug" => "ai-tools",
                "icon" => "fas fa-robot",
                "color" => "#10b981",
                "description" =>
                    "Layanan AI premium seperti ChatGPT, Claude, Midjourney, dan lainnya.",
                "sort_order" => 8,
            ],
        ];

        $createdCategories = [];
        foreach ($categories as $cat) {
            $createdCategories[$cat["slug"]] = Category::updateOrCreate(
                ["slug" => $cat["slug"]],
                array_merge($cat, ["is_active" => true]),
            );
        }

        // ─── Products ──────────────────────────────────────────────────────────
        $products = [
            // ── Streaming ──────────────────────────────────────────────────────
            [
                "category" => "streaming",
                "name" => "Netflix Premium 1 Bulan",
                "slug" => "netflix-premium-1-bulan",
                "short_description" =>
                    "Akun Netflix Premium private 1 bulan, 4K Ultra HD.",
                "description" =>
                    "Akun Netflix Premium 1 Bulan\n\n✅ Private / Tidak dibagi\n✅ Resolusi 4K Ultra HD\n✅ Bisa di 4 perangkat\n✅ Garansi full 1 bulan\n✅ Pengiriman instan setelah pembayaran\n\nNote: Harap segera ubah password setelah menerima akun.",
                "price" => 45000,
                "original_price" => 75000,
                "stock" => 50,
                "badge" => "hot",
                "is_popular" => true,
                "sort_order" => 1,
            ],
            [
                "category" => "streaming",
                "name" => "Spotify Premium 1 Bulan",
                "slug" => "spotify-premium-1-bulan",
                "short_description" =>
                    "Akun Spotify Premium family plan upgrade 1 bulan.",
                "description" =>
                    "Spotify Premium 1 Bulan\n\n✅ Family Plan (Upgrade)\n✅ Tanpa iklan\n✅ Download lagu offline\n✅ Audio kualitas tinggi\n✅ Garansi penuh 1 bulan",
                "price" => 18000,
                "original_price" => 35000,
                "stock" => 100,
                "badge" => "sale",
                "is_popular" => true,
                "sort_order" => 2,
            ],
            [
                "category" => "streaming",
                "name" => "YouTube Premium 1 Bulan",
                "slug" => "youtube-premium-1-bulan",
                "short_description" =>
                    "YouTube Premium tanpa iklan + YouTube Music 1 bulan.",
                "description" =>
                    "YouTube Premium 1 Bulan\n\n✅ Tanpa iklan di YouTube\n✅ Termasuk YouTube Music Premium\n✅ Background play di HP\n✅ Download video offline\n✅ Garansi 1 bulan penuh",
                "price" => 20000,
                "original_price" => 40000,
                "stock" => 80,
                "badge" => "new",
                "is_popular" => false,
                "sort_order" => 3,
            ],
            [
                "category" => "streaming",
                "name" => "Disney+ Hotstar 1 Bulan",
                "slug" => "disneyplus-hotstar-1-bulan",
                "short_description" =>
                    "Disney+ Hotstar premium 1 bulan, film & serial eksklusif.",
                "description" =>
                    "Disney+ Hotstar Premium 1 Bulan\n\n✅ Akses semua konten Disney, Marvel, Star Wars\n✅ Kualitas hingga 4K\n✅ Bisa download offline\n✅ Garansi penuh",
                "price" => 25000,
                "original_price" => 49000,
                "stock" => 30,
                "badge" => "sale",
                "is_popular" => false,
                "sort_order" => 4,
            ],

            // ── Desain ─────────────────────────────────────────────────────────
            [
                "category" => "desain",
                "name" => "Canva Pro 1 Bulan",
                "slug" => "canva-pro-1-bulan",
                "short_description" =>
                    "Canva Pro 1 bulan, akses semua template & elemen premium.",
                "description" =>
                    "Canva Pro 1 Bulan\n\n✅ Akses 100+ juta elemen premium\n✅ Background remover otomatis\n✅ Brand Kit lengkap\n✅ Resize desain 1 klik\n✅ Storage 1TB\n✅ Garansi penuh 1 bulan",
                "price" => 35000,
                "original_price" => 60000,
                "stock" => 60,
                "badge" => "hot",
                "is_popular" => true,
                "sort_order" => 1,
            ],
            [
                "category" => "desain",
                "name" => "Adobe Creative Cloud 1 Bulan",
                "slug" => "adobe-creative-cloud-1-bulan",
                "short_description" =>
                    "Akses Photoshop, Illustrator, Premiere Pro & semua aplikasi Adobe.",
                "description" =>
                    "Adobe Creative Cloud All Apps\n\n✅ Termasuk 20+ aplikasi Adobe\n✅ Photoshop, Illustrator, Premiere Pro\n✅ After Effects, XD, dll\n✅ Cloud storage 100GB\n✅ Garansi penuh 1 bulan",
                "price" => 85000,
                "original_price" => 150000,
                "stock" => 15,
                "badge" => "hot",
                "is_popular" => true,
                "sort_order" => 2,
            ],
            [
                "category" => "desain",
                "name" => "Figma Professional 1 Bulan",
                "slug" => "figma-professional-1-bulan",
                "short_description" =>
                    "Figma Professional plan untuk desain UI/UX tanpa batas.",
                "description" =>
                    "Figma Professional 1 Bulan\n\n✅ Unlimited projects & files\n✅ Advanced prototyping\n✅ Team libraries\n✅ Audio conversations\n✅ Garansi penuh",
                "price" => 55000,
                "original_price" => 90000,
                "stock" => 20,
                "badge" => "new",
                "is_popular" => false,
                "sort_order" => 3,
            ],

            // ── Produktivitas ──────────────────────────────────────────────────
            [
                "category" => "produktivitas",
                "name" => "Microsoft Office 365 1 Bulan",
                "slug" => "microsoft-office-365-1-bulan",
                "short_description" =>
                    "Office 365 lengkap: Word, Excel, PowerPoint, Teams & OneDrive.",
                "description" =>
                    "Microsoft Office 365 Personal 1 Bulan\n\n✅ Word, Excel, PowerPoint versi terbaru\n✅ Teams untuk meeting online\n✅ OneDrive 1TB storage\n✅ Outlook premium\n✅ Garansi penuh 1 bulan",
                "price" => 35000,
                "original_price" => 65000,
                "stock" => 40,
                "badge" => "sale",
                "is_popular" => true,
                "sort_order" => 1,
            ],
            [
                "category" => "produktivitas",
                "name" => "Zoom Pro 1 Bulan",
                "slug" => "zoom-pro-1-bulan",
                "short_description" =>
                    "Zoom Pro, meeting tanpa batas waktu hingga 100 peserta.",
                "description" =>
                    "Zoom Pro 1 Bulan\n\n✅ Meeting unlimited (tanpa batas 40 menit)\n✅ Hingga 100 peserta per meeting\n✅ Rekam meeting ke cloud\n✅ Social media streaming\n✅ Garansi penuh",
                "price" => 55000,
                "original_price" => 99000,
                "stock" => 25,
                "badge" => null,
                "is_popular" => false,
                "sort_order" => 2,
            ],
            [
                "category" => "produktivitas",
                "name" => "Notion Plus 1 Bulan",
                "slug" => "notion-plus-1-bulan",
                "short_description" =>
                    "Notion Plus untuk produktivitas dan manajemen proyek personal.",
                "description" =>
                    "Notion Plus 1 Bulan\n\n✅ Unlimited blocks & storage\n✅ Invite guests tanpa batas\n✅ Version history 30 hari\n✅ Priority customer support\n✅ Garansi penuh",
                "price" => 25000,
                "original_price" => 48000,
                "stock" => 35,
                "badge" => "new",
                "is_popular" => false,
                "sort_order" => 3,
            ],

            // ── Gaming ─────────────────────────────────────────────────────────
            [
                "category" => "gaming",
                "name" => "Mobile Legends Diamond 275+28",
                "slug" => "mobile-legends-diamond-275",
                "short_description" =>
                    "Top up 275+28 Diamond Mobile Legends langsung ke akun kamu.",
                "description" =>
                    "Top Up Mobile Legends\n275 + 28 Bonus Diamond\n\n✅ Proses cepat < 5 menit\n✅ Langsung masuk ke akun\n✅ Aman dan terpercaya\n✅ Tidak perlu data login\n\nCara order: Sertakan ID & Server ML kamu di catatan.",
                "price" => 79000,
                "original_price" => 95000,
                "stock" => 999,
                "badge" => "hot",
                "is_popular" => true,
                "sort_order" => 1,
            ],
            [
                "category" => "gaming",
                "name" => "Free Fire 1040 Diamonds",
                "slug" => "free-fire-1040-diamonds",
                "short_description" =>
                    "Top up 1040 Diamonds Free Fire ke akun kamu.",
                "description" =>
                    "Top Up Free Fire\n1040 Diamonds\n\n✅ Proses otomatis < 5 menit\n✅ Masuk langsung ke akun\n✅ Aman & terjamin\n\nCara order: Sertakan ID FF & Nickname di catatan.",
                "price" => 135000,
                "original_price" => 150000,
                "stock" => 999,
                "badge" => "sale",
                "is_popular" => false,
                "sort_order" => 2,
            ],
            [
                "category" => "gaming",
                "name" => "Steam Wallet Rp 100.000",
                "slug" => "steam-wallet-100rb",
                "short_description" =>
                    "Steam Wallet Code senilai Rp 100.000 untuk beli game Steam.",
                "description" =>
                    "Steam Wallet Code Rp 100.000\n\n✅ Kode langsung dikirim via WA/email\n✅ Bisa dipakai beli game & item\n✅ Berlaku selamanya\n✅ Tanpa expired",
                "price" => 105000,
                "original_price" => null,
                "stock" => 50,
                "badge" => null,
                "is_popular" => false,
                "sort_order" => 3,
            ],

            // ── Jasa Digital ───────────────────────────────────────────────────
            [
                "category" => "jasa",
                "name" => "Jasa Pembuatan Website Landing Page",
                "slug" => "jasa-website-landing-page",
                "short_description" =>
                    "Jasa buat landing page profesional untuk bisnis atau portofolio.",
                "description" =>
                    "Jasa Pembuatan Landing Page\n\nTermasuk:\n✅ Desain modern & responsif\n✅ Optimasi kecepatan\n✅ Form kontak / WhatsApp button\n✅ SEO dasar\n✅ Hosting gratis 1 bulan\n✅ Revisi hingga 3x\n✅ Selesai dalam 3-5 hari kerja\n\nNote: Hubungi admin untuk konsultasi gratis!",
                "price" => 250000,
                "original_price" => 500000,
                "stock" => 10,
                "badge" => "hot",
                "is_popular" => true,
                "sort_order" => 1,
            ],
            [
                "category" => "jasa",
                "name" => "Jasa Desain Logo Profesional",
                "slug" => "jasa-desain-logo",
                "short_description" =>
                    "Jasa desain logo unik untuk brand dan bisnis kamu.",
                "description" =>
                    "Jasa Desain Logo\n\nTermasuk:\n✅ Konsultasi brand identity\n✅ 3 konsep desain awal\n✅ Format PNG, SVG, PDF\n✅ Revisi hingga 5x\n✅ Selesai 2-3 hari kerja\n✅ File source bisa ditambahkan",
                "price" => 100000,
                "original_price" => 200000,
                "stock" => 15,
                "badge" => null,
                "is_popular" => true,
                "sort_order" => 2,
            ],
            [
                "category" => "jasa",
                "name" => "Jasa Kelola Media Sosial 1 Bulan",
                "slug" => "jasa-kelola-medsos-1-bulan",
                "short_description" =>
                    "Kelola Instagram/TikTok bisnis kamu selama 1 bulan penuh.",
                "description" =>
                    "Jasa Kelola Media Sosial 1 Bulan\n\nTermasuk:\n✅ 20 konten feed/bulan\n✅ Desain grafis konten\n✅ Caption menarik\n✅ Jadwal posting teratur\n✅ Laporan bulanan\n✅ Konsultasi strategi konten",
                "price" => 350000,
                "original_price" => 600000,
                "stock" => 5,
                "badge" => "new",
                "is_popular" => false,
                "sort_order" => 3,
            ],

            // ── Edukasi ────────────────────────────────────────────────────────
            [
                "category" => "edukasi",
                "name" => "E-Book Panduan Bisnis Digital",
                "slug" => "ebook-panduan-bisnis-digital",
                "short_description" =>
                    "E-book lengkap memulai bisnis digital dari nol hingga profit.",
                "description" =>
                    "E-Book: Panduan Bisnis Digital\n\n📚 Isi materi:\n✅ Strategi memulai bisnis online\n✅ Riset produk & target pasar\n✅ Teknik marketing digital\n✅ Manajemen keuangan bisnis\n✅ Studi kasus nyata\n\nFormat: PDF | 150+ halaman\nPengiriman: Langsung via WhatsApp/Email",
                "price" => 35000,
                "original_price" => 75000,
                "stock" => 999,
                "badge" => "sale",
                "is_popular" => false,
                "sort_order" => 1,
            ],
            [
                "category" => "edukasi",
                "name" => "Kursus Online Web Development",
                "slug" => "kursus-online-web-development",
                "short_description" =>
                    "Kursus lengkap web development dari HTML hingga Laravel.",
                "description" =>
                    "Kursus Online Web Development\n\n📖 Materi:\n✅ HTML, CSS, JavaScript\n✅ PHP & Laravel Framework\n✅ Database MySQL\n✅ Deploy ke Hosting\n✅ Project nyata siap portofolio\n\n🎥 Video tutorial HD\n📝 Materi PDF\n💬 Grup support Discord\nAkses seumur hidup!",
                "price" => 150000,
                "original_price" => 300000,
                "stock" => 999,
                "badge" => "hot",
                "is_popular" => true,
                "sort_order" => 2,
            ],

            // ── Voucher ────────────────────────────────────────────────────────
            [
                "category" => "voucher",
                "name" => "Voucher Tokopedia Rp 50.000",
                "slug" => "voucher-tokopedia-50rb",
                "short_description" =>
                    "Voucher belanja Tokopedia senilai Rp 50.000 min. belanja 100rb.",
                "description" =>
                    "Voucher Tokopedia Rp 50.000\n\n✅ Berlaku di semua produk\n✅ Min. belanja Rp 100.000\n✅ Kode langsung dikirim\n✅ Valid 30 hari setelah diterima",
                "price" => 45000,
                "original_price" => 50000,
                "stock" => 30,
                "badge" => "sale",
                "is_popular" => false,
                "sort_order" => 1,
            ],
            [
                "category" => "voucher",
                "name" => "Pulsa Semua Operator Rp 25.000",
                "slug" => "pulsa-semua-operator-25rb",
                "short_description" =>
                    "Isi pulsa Rp 25.000 semua operator (Telkomsel, Indosat, XL, dll).",
                "description" =>
                    "Pulsa Rp 25.000 Semua Operator\n\n✅ Telkomsel, Indosat, XL, Tri, Smartfren\n✅ Proses otomatis < 2 menit\n✅ Berlaku 24/7\n\nCara order: Sertakan nomor HP tujuan di catatan.",
                "price" => 26000,
                "original_price" => null,
                "stock" => 999,
                "badge" => null,
                "is_popular" => false,
                "sort_order" => 2,
            ],

            // ── AI Tools ───────────────────────────────────────────────────────
            [
                "category" => "ai-tools",
                "name" => "ChatGPT Plus 1 Bulan",
                "slug" => "chatgpt-plus-1-bulan",
                "short_description" =>
                    "Akses ChatGPT Plus dengan GPT-4o, lebih cepat & fitur premium.",
                "description" =>
                    "ChatGPT Plus 1 Bulan\n\n✅ Akses GPT-4o (model terbaru)\n✅ Lebih cepat dari versi gratis\n✅ Prioritas akses saat ramai\n✅ Fitur DALL-E (generate gambar)\n✅ Fitur Code Interpreter\n✅ Plugin & GPTs custom\n✅ Garansi full 1 bulan\n\nPengiriman: Detail akun via WhatsApp/Email",
                "price" => 50000,
                "original_price" => 85000,
                "stock" => 50,
                "badge" => "hot",
                "is_popular" => true,
                "sort_order" => 1,
            ],
            [
                "category" => "ai-tools",
                "name" => "Claude Pro 1 Bulan",
                "slug" => "claude-pro-1-bulan",
                "short_description" =>
                    "Akses Claude Pro dari Anthropic, AI percakapan terbaik.",
                "description" =>
                    "Claude Pro 1 Bulan\n\n✅ Akses Claude 3.5 Sonnet\n✅ 5x lebih banyak pesan\n✅ Prioritas akses saat sibuk\n✅ Fitur Projects & Artifacts\n✅ Upload file PDF, dokumen\n✅ Analisis gambar\n✅ Garansi full 1 bulan\n\nPengiriman: Detail akun via WhatsApp/Email",
                "price" => 55000,
                "original_price" => 95000,
                "stock" => 30,
                "badge" => "new",
                "is_popular" => true,
                "sort_order" => 2,
            ],
            [
                "category" => "ai-tools",
                "name" => "Midjourney Basic 1 Bulan",
                "slug" => "midjourney-basic-1-bulan",
                "short_description" =>
                    "Generate gambar AI dengan Midjourney Basic plan.",
                "description" =>
                    "Midjourney Basic 1 Bulan\n\n✅ ~200 generasi gambar/bulan\n✅ Akses fitur terbaru V6\n✅ Fast GPU time 3.3 jam\n✅ Unlimited Relax mode\n✅ Akses Discord Midjourney\n✅ Garansi full 1 bulan\n\nPengiriman: Invite Discord via WhatsApp",
                "price" => 65000,
                "original_price" => 120000,
                "stock" => 20,
                "badge" => "hot",
                "is_popular" => true,
                "sort_order" => 3,
            ],
            [
                "category" => "ai-tools",
                "name" => "GitHub Copilot 1 Bulan",
                "slug" => "github-copilot-1-bulan",
                "short_description" =>
                    "AI coding assistant untuk programmer, support semua IDE.",
                "description" =>
                    "GitHub Copilot Individual 1 Bulan\n\n✅ AI autocomplete code\n✅ Support VS Code, JetBrains, Neovim\n✅ Chat dengan Copilot\n✅ Multi-language support\n✅ Code explanation & fix\n✅ Garansi full 1 bulan\n\nPengiriman: Akses via GitHub invite",
                "price" => 45000,
                "original_price" => 75000,
                "stock" => 40,
                "badge" => "sale",
                "is_popular" => false,
                "sort_order" => 4,
            ],
            [
                "category" => "ai-tools",
                "name" => "Grammarly Premium 1 Bulan",
                "slug" => "grammarly-premium-1-bulan",
                "short_description" =>
                    "AI writing assistant untuk grammar, style, dan plagiarism check.",
                "description" =>
                    "Grammarly Premium 1 Bulan\n\n✅ Advanced grammar & spelling\n✅ Style & tone suggestions\n✅ Plagiarism checker\n✅ Word choice improvements\n✅ Browser extension + desktop app\n✅ Garansi full 1 bulan",
                "price" => 35000,
                "original_price" => 60000,
                "stock" => 35,
                "badge" => "sale",
                "is_popular" => false,
                "sort_order" => 5,
            ],
            [
                "category" => "ai-tools",
                "name" => "Perplexity Pro 1 Bulan",
                "slug" => "perplexity-pro-1-bulan",
                "short_description" =>
                    "AI search engine premium dengan sumber akurat dan GPT-4.",
                "description" =>
                    "Perplexity Pro 1 Bulan\n\n✅ Unlimited Pro searches\n✅ Akses GPT-4o & Claude\n✅ File upload & analysis\n✅ Sumber referensi terpercaya\n✅ API access\n✅ Garansi full 1 bulan\n\nPengiriman: Detail akun via WhatsApp/Email",
                "price" => 50000,
                "original_price" => 90000,
                "stock" => 25,
                "badge" => "new",
                "is_popular" => false,
                "sort_order" => 6,
            ],
            [
                "category" => "ai-tools",
                "name" => "Notion AI 1 Bulan",
                "slug" => "notion-ai-1-bulan",
                "short_description" =>
                    "AI writing assistant terintegrasi di Notion workspace.",
                "description" =>
                    "Notion AI Add-on 1 Bulan\n\n✅ AI writing & summarization\n✅ Brainstorm & ideation\n✅ Fix spelling & grammar\n✅ Translate to any language\n✅ Terintegrasi di Notion\n✅ Garansi full 1 bulan\n\nNote: Memerlukan akun Notion (Plus/Team recommended)",
                "price" => 40000,
                "original_price" => 70000,
                "stock" => 30,
                "badge" => null,
                "is_popular" => false,
                "sort_order" => 7,
            ],
            [
                "category" => "ai-tools",
                "name" => "Runway ML Standard 1 Bulan",
                "slug" => "runway-ml-standard-1-bulan",
                "short_description" =>
                    "AI video generator untuk create video dari text/image.",
                "description" =>
                    "Runway ML Standard 1 Bulan\n\n✅ 625 credits/bulan\n✅ Gen-3 Alpha video generation\n✅ Text to video, image to video\n✅ Video editing AI tools\n✅ Upscale & slow motion\n✅ Garansi full 1 bulan\n\nPengiriman: Detail akun via WhatsApp/Email",
                "price" => 75000,
                "original_price" => 130000,
                "stock" => 15,
                "badge" => "hot",
                "is_popular" => false,
                "sort_order" => 8,
            ],
        ];

        foreach ($products as $productData) {
            $categorySlug = $productData["category"];
            unset($productData["category"]);

            if (!isset($createdCategories[$categorySlug])) {
                continue;
            }

            Product::updateOrCreate(
                ["slug" => $productData["slug"]],
                array_merge($productData, [
                    "category_id" => $createdCategories[$categorySlug]->id,
                    "is_active" => true,
                    "views" => rand(10, 500),
                ]),
            );
        }

        $this->command->info("✅ Seeder berhasil dijalankan!");
        $this->command->info("");
        $this->command->info("🔐 Admin Login:");
        $this->command->info("   Email    : admin@shurizastore.com");
        $this->command->info("   Password : admin123");
        $this->command->info("");
        $this->command->info("👤 User Test:");
        $this->command->info("   Email    : user@example.com");
        $this->command->info("   Password : password");
        $this->command->info("");
        $this->command->info("📦 " . Product::count() . " produk ditambahkan");
        $this->command->info(
            "🗂️  " . Category::count() . " kategori ditambahkan",
        );
    }
}
