<?php

namespace App\Http\Controllers;

use App\Models\Article;
use App\Models\Product;

class SeoController extends Controller
{
    public function sitemap()
    {
        $products = Product::active()->select('slug', 'updated_at')->get();
        $articles = Article::published()->select('slug', 'updated_at')->get();

        return response()
            ->view('sitemap', compact('products', 'articles'))
            ->header('Content-Type', 'application/xml');
    }

    public function robots()
    {
        $content = "User-agent: *\n";
        $content .= "Allow: /\n";
        $content .= "Disallow: /admin\n";
        $content .= "Disallow: /cart\n";
        $content .= "Disallow: /order\n";
        $content .= "Disallow: /dashboard\n";
        $content .= "Disallow: /login\n";
        $content .= "Disallow: /register\n\n";
        $content .= "Sitemap: " . url('/sitemap.xml') . "\n";

        return response($content)->header('Content-Type', 'text/plain');
    }
}
