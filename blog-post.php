<?php
require_once 'config/config.php';

// Get the post slug from URL
$slug = $_GET['slug'] ?? '';

if (empty($slug)) {
    header('Location: /blog');
    exit;
}

try {
    $db = Database::getInstance()->getConnection();

    // Get the blog post with category and tags
    $postQuery = "
        SELECT 
            bp.*,
            bc.name as category_name,
            bc.slug as category_slug,
            bc.color as category_color,
            bc.icon as category_icon
        FROM blog_posts bp 
        LEFT JOIN blog_categories bc ON bp.category_id = bc.id 
        WHERE bp.slug = ? AND bp.status = 'published'
    ";
    $stmt = $db->prepare($postQuery);
    $stmt->execute([$slug]);
    $post = $stmt->fetch();

    if (!$post) {
        header('HTTP/1.0 404 Not Found');
        include '404.php';
        exit;
    }

    // Get post tags
    $tagsQuery = "
        SELECT bt.* 
        FROM blog_tags bt
        JOIN blog_post_tags bpt ON bt.id = bpt.tag_id
        WHERE bpt.post_id = ?
        ORDER BY bt.name
    ";
    $stmt = $db->prepare($tagsQuery);
    $stmt->execute([$post['id']]);
    $tags = $stmt->fetchAll();

    // Update view count
    $updateViewQuery = "UPDATE blog_posts SET view_count = view_count + 1 WHERE id = ?";
    $stmt = $db->prepare($updateViewQuery);
    $stmt->execute([$post['id']]);
    $post['view_count']++;

    // Get related posts (same category, excluding current post)
    $relatedQuery = "
        SELECT 
            bp.*,
            bc.name as category_name,
            bc.slug as category_slug,
            bc.color as category_color
        FROM blog_posts bp 
        LEFT JOIN blog_categories bc ON bp.category_id = bc.id 
        WHERE bp.category_id = ? AND bp.id != ? AND bp.status = 'published'
        ORDER BY bp.published_at DESC 
        LIMIT 3
    ";
    $stmt = $db->prepare($relatedQuery);
    $stmt->execute([$post['category_id'], $post['id']]);
    $relatedPosts = $stmt->fetchAll();

    // If not enough related posts, get recent posts
    if (count($relatedPosts) < 3) {
        $recentQuery = "
            SELECT 
                bp.*,
                bc.name as category_name,
                bc.slug as category_slug,
                bc.color as category_color
            FROM blog_posts bp 
            LEFT JOIN blog_categories bc ON bp.category_id = bc.id 
            WHERE bp.id != ? AND bp.status = 'published'
            ORDER BY bp.published_at DESC 
            LIMIT ?
        ";
        $stmt = $db->prepare($recentQuery);
        $stmt->execute([$post['id'], 3 - count($relatedPosts)]);
        $additionalPosts = $stmt->fetchAll();
        $relatedPosts = array_merge($relatedPosts, $additionalPosts);
    }

    // Track analytics (privacy-friendly)
    $analyticsQuery = "
        INSERT INTO blog_analytics (post_id, visitor_id, ip_address, user_agent, referrer, visited_at)
        VALUES (?, ?, ?, ?, ?, NOW())
    ";
    $visitorId = hash('sha256', getClientIP() . date('Y-m-d')); // Daily unique visitor
    $stmt = $db->prepare($analyticsQuery);
    $stmt->execute([
        $post['id'],
        $visitorId,
        getClientIP(),
        $_SERVER['HTTP_USER_AGENT'] ?? '',
        $_SERVER['HTTP_REFERER'] ?? ''
    ]);
} catch (Exception $e) {
    logError('Blog post error: ' . $e->getMessage());
    header('HTTP/1.0 500 Internal Server Error');
    include '500.php';
    exit;
}

// Helper functions
function formatDate($date)
{
    return date('M j, Y', strtotime($date));
}

function timeAgo($date)
{
    $time = time() - strtotime($date);
    if ($time < 60) return 'just now';
    if ($time < 3600) return floor($time / 60) . ' min ago';
    if ($time < 86400) return floor($time / 3600) . ' hours ago';
    if ($time < 2592000) return floor($time / 86400) . ' days ago';
    return formatDate($date);
}

function truncateContent($content, $length = 120)
{
    $text = strip_tags($content);
    return strlen($text) > $length ? substr($text, 0, $length) . '...' : $text;
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($post['meta_title'] ?: $post['title']); ?></title>
    <meta name="description" content="<?php echo htmlspecialchars($post['meta_description'] ?: $post['excerpt']); ?>">
    <meta name="keywords" content="<?php echo htmlspecialchars($post['meta_keywords']); ?>">
    <meta name="author" content="<?php echo htmlspecialchars($post['author_name']); ?>">
    <meta name="robots" content="index, follow">

    <!-- Open Graph / Facebook -->
    <meta property="og:type" content="article">
    <meta property="og:url" content="<?php echo APP_URL; ?>/blog/<?php echo $post['slug']; ?>">
    <meta property="og:title" content="<?php echo htmlspecialchars($post['title']); ?>">
    <meta property="og:description" content="<?php echo htmlspecialchars($post['excerpt']); ?>">
    <?php if ($post['featured_image']): ?>
        <meta property="og:image" content="<?php echo htmlspecialchars($post['featured_image']); ?>">
    <?php endif; ?>

    <!-- Twitter -->
    <meta property="twitter:card" content="summary_large_image">
    <meta property="twitter:url" content="<?php echo APP_URL; ?>/blog/<?php echo $post['slug']; ?>">
    <meta property="twitter:title" content="<?php echo htmlspecialchars($post['title']); ?>">
    <meta property="twitter:description" content="<?php echo htmlspecialchars($post['excerpt']); ?>">
    <?php if ($post['featured_image']): ?>
        <meta property="twitter:image" content="<?php echo htmlspecialchars($post['featured_image']); ?>">
    <?php endif; ?>

    <!-- Schema.org structured data -->
    <script type="application/ld+json">
        {
            "@context": "https://schema.org",
            "@type": "BlogPosting",
            "headline": "<?php echo htmlspecialchars($post['title']); ?>",
            "description": "<?php echo htmlspecialchars($post['excerpt']); ?>",
            "author": {
                "@type": "Person",
                "name": "<?php echo htmlspecialchars($post['author_name']); ?>"
            },
            "publisher": {
                "@type": "Organization",
                "name": "<?php echo APP_NAME; ?>",
                "logo": {
                    "@type": "ImageObject",
                    "url": "<?php echo APP_URL; ?>/assets/images/logo.png"
                }
            },
            "datePublished": "<?php echo date('c', strtotime($post['published_at'])); ?>",
            "dateModified": "<?php echo date('c', strtotime($post['updated_at'])); ?>",
            "mainEntityOfPage": {
                "@type": "WebPage",
                "@id": "<?php echo APP_URL; ?>/blog/<?php echo $post['slug']; ?>"
            }
        }
    </script>

    <?php include "./includes/head.php" ?>

    <link href="https://cdnjs.cloudflare.com/ajax/libs/prism/1.29.0/themes/prism.min.css" rel="stylesheet">

    <!-- Additional CSS -->
    <style>
        body.custom-body {
            background: linear-gradient(135deg, #f5f7fa 0%, #c3cfe2 100%) !important;
            min-height: 100vh !important;
        }

        .custom-card {
            border-radius: 12px !important;
            border: none !important;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1) !important;
        }

        .article-header {
            background: linear-gradient(135deg, rgba(0, 123, 255, 0.1) 0%, rgba(0, 123, 255, 0.05) 100%) !important;
            border-radius: 16px !important;
            padding: 3rem 2rem !important;
            margin-bottom: 2rem !important;
        }

        .article-content {
            background: white !important;
            border-radius: 16px !important;
            padding: 3rem !important;
            margin-bottom: 2rem !important;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1) !important;
            line-height: 1.8 !important;
        }

        .article-content h1,
        .article-content h2,
        .article-content h3,
        .article-content h4,
        .article-content h5,
        .article-content h6 {
            color: #495057 !important;
            margin-top: 2rem !important;
            margin-bottom: 1rem !important;
            font-weight: 600 !important;
        }

        .article-content h2 {
            color: #667eea !important;
            border-bottom: 2px solid #e9ecef !important;
            padding-bottom: 0.5rem !important;
        }

        .article-content h3 {
            color: rgb(75, 75, 75) !important;
            font-size: 1.25rem !important;
        }

        .article-content p {
            margin-bottom: 1.5rem !important;
            color: #495057 !important;
        }

        .article-content ul,
        .article-content ol {
            margin-bottom: 1.5rem !important;
            padding-left: 2rem !important;
        }

        .article-content li {
            margin-bottom: 0.5rem !important;
            color: #495057 !important;
        }

        .article-content blockquote {
            border-left: 4px solid #667eea !important;
            padding-left: 1.5rem !important;
            margin: 2rem 0 !important;
            font-style: italic !important;
            color: #6c757d !important;
            background: #f8f9fa !important;
            padding: 1rem 1.5rem !important;
            border-radius: 0 8px 8px 0 !important;
        }

        .article-content code {
            background: #f8f9fa !important;
            padding: 0.2rem 0.4rem !important;
            border-radius: 4px !important;
            color: #e83e8c !important;
            font-size: 0.875em !important;
        }

        .article-content pre code {
            background: none !important;
            padding: 0 !important;
            color: inherit !important;
        }

        .category-badge {
            padding: 0.5rem 1rem !important;
            border-radius: 20px !important;
            font-size: 0.9rem !important;
            font-weight: 500 !important;
            text-decoration: none !important;
            display: inline-flex !important;
            align-items: center !important;
        }

        .tag-list .tag {
            display: inline-block !important;
            padding: 0.25rem 0.75rem !important;
            margin: 0.25rem 0.25rem 0.25rem 0 !important;
            background: white !important;
            border: 1px solid #e9ecef !important;
            border-radius: 15px !important;
            text-decoration: none !important;
            color: #495057 !important;
            font-size: 0.8rem !important;
            transition: all 0.3s ease !important;
        }

        .tag-list .tag:hover {
            background: #667eea !important;
            color: white !important;
            border-color: #667eea !important;
        }

        .author-bio {
            background: linear-gradient(135deg, rgba(0, 0, 0, 0.09) 0%, rgba(43, 43, 43, 0.07) 100%) !important;
            border-radius: 12px !important;
            padding: 2rem !important;
            margin: 2rem 0 !important;
        }

        .related-posts .card {
            transition: all 0.3s ease !important;
            height: 100% !important;
        }

        .related-posts .card:hover {
            transform: translateY(-5px) !important;
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.15) !important;
        }

        .share-buttons {
            position: sticky !important;
            top: 2rem !important;
        }

        .share-btn {
            display: block !important;
            width: 50px !important;
            height: 50px !important;
            border-radius: 50% !important;
            margin-bottom: 1rem !important;
            display: flex !important;
            align-items: center !important;
            justify-content: center !important;
            text-decoration: none !important;
            color: white !important;
            font-size: 1.2rem !important;
            transition: all 0.3s ease !important;
        }

        .share-btn:hover {
            transform: scale(1.1) !important;
            color: white !important;
        }

        .share-facebook {
            background-color: #3b5998 !important;
        }

        .share-twitter {
            background-color: #1da1f2 !important;
        }

        .share-linkedin {
            background-color: #0077b5 !important;
        }

        .share-copy {
            background-color: #6c757d !important;
        }

        .reading-progress {
            position: fixed !important;
            top: 0 !important;
            left: 0 !important;
            width: 0% !important;
            height: 3px !important;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%) !important;
            z-index: 1000 !important;
            transition: width 0.3s ease !important;
        }

        .table-of-contents {
            background: white !important;
            border-radius: 12px !important;
            padding: 1.5rem !important;
            margin-bottom: 2rem !important;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1) !important;
            position: sticky !important;
            top: 100px !important;
            z-index: 9;
        }

        .table-of-contents a {
            color: #495057 !important;
            text-decoration: none !important;
            display: block !important;
            padding: 0.25rem 0 !important;
            border-left: 3px solid transparent !important;
            padding-left: 1rem !important;
            margin-left: -1rem !important;
            transition: all 0.3s ease !important;
        }

        .table-of-contents a:hover,
        .table-of-contents a.active {
            color: #667eea !important;
            border-left-color: #667eea !important;
            background: rgba(0, 123, 255, 0.05) !important;
        }

        .breadcrumb {
            background: transparent !important;
            padding: 0 !important;
            margin-bottom: 2rem !important;
        }

        .breadcrumb-item a {
            text-decoration: none !important;
            color: #6c757d !important;
        }

        .breadcrumb-item.active {
            color: #495057 !important;
        }

        .sidebar-widget {
            background: white !important;
            border-radius: 12px !important;
            padding: 1.5rem !important;
            margin-bottom: 1.5rem !important;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1) !important;
        }

        .sidebar-widget h5,
        .table-of-contents h5 {
            color: #495057 !important;
            margin-bottom: 1rem !important;
            font-weight: 600 !important;
        }

        .article-content a:not(.btn) {
            color: rgb(114, 136, 233);
            transition: all 0.2s;
        }

        .article-content a:not(.btn):hover {
            color: #667eea;
            font-weight: 500;
            transition: all 0.2s;
        }

        .call-to-action h3,
        .call-to-action p {
            color: #fff !important;
        }

        .call-to-action h3 {
            margin-top: 0px !important;
        }

        @media (max-width: 768px) {
            .article-content {
                padding: 2rem 1.5rem !important;
            }

            .article-header {
                padding: 2rem 1.5rem !important;
            }

            .share-buttons {
                position: static !important;
                display: flex !important;
                justify-content: center !important;
                gap: 1rem !important;
                margin: 2rem 0 !important;
            }

            .share-btn {
                margin-bottom: 0 !important;
            }
        }
    </style>

</head>

<body class="custom-body">
    <!-- Reading Progress Bar -->
    <div class="reading-progress" id="reading-progress"></div>

    <!-- Navigation -->
    <?php include "./includes/nav.php" ?>


    <div class="container py-5">
        <!-- Breadcrumb -->
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="/">Home</a></li>
                <li class="breadcrumb-item"><a href="/blog">Blog</a></li>
                <?php if ($post['category_name']): ?>
                    <li class="breadcrumb-item">
                        <a href="/blog?category=<?php echo urlencode($post['category_slug']); ?>">
                            <?php echo htmlspecialchars($post['category_name']); ?>
                        </a>
                    </li>
                <?php endif; ?>
                <li class="breadcrumb-item active" aria-current="page">
                    <?php echo htmlspecialchars($post['title']); ?>
                </li>
            </ol>
        </nav>

        <div class="row">
            <!-- Share Buttons (Desktop) -->
            <div class="col-lg-1 d-none d-lg-block">
                <div class="share-buttons">
                    <a href="https://www.facebook.com/sharer/sharer.php?u=<?php echo urlencode(APP_URL . '/blog/' . $post['slug']); ?>"
                        class="share-btn share-facebook" target="_blank" title="Share on Facebook">
                        <i class="bi bi-facebook"></i>
                    </a>
                    <a href="https://twitter.com/intent/tweet?url=<?php echo urlencode(APP_URL . '/blog/' . $post['slug']); ?>&text=<?php echo urlencode($post['title']); ?>"
                        class="share-btn share-twitter" target="_blank" title="Share on Twitter">
                        <i class="bi bi-twitter"></i>
                    </a>
                    <a href="https://www.linkedin.com/sharing/share-offsite/?url=<?php echo urlencode(APP_URL . '/blog/' . $post['slug']); ?>"
                        class="share-btn share-linkedin" target="_blank" title="Share on LinkedIn">
                        <i class="bi bi-linkedin"></i>
                    </a>
                    <a href="#" class="share-btn share-copy" onclick="copyUrl()" title="Copy Link">
                        <i class="bi bi-link-45deg"></i>
                    </a>
                </div>
            </div>

            <!-- Main Content -->
            <div class="col-lg-8">
                <!-- Article Header -->
                <header class="article-header">
                    <?php if ($post['category_name']): ?>
                        <a href="/blog?category=<?php echo urlencode($post['category_slug']); ?>"
                            class="category-badge mb-3"
                            style="background-color: <?php echo $post['category_color']; ?>15; color: <?php echo $post['category_color']; ?>;">
                            <i class="<?php echo $post['category_icon']; ?> me-2"></i>
                            <?php echo htmlspecialchars($post['category_name']); ?>
                        </a>
                    <?php endif; ?>

                    <h1 class="display-5 fw-bold mb-4"><?php echo htmlspecialchars($post['title']); ?></h1>

                    <?php if ($post['excerpt']): ?>
                        <p class="lead text-muted mb-4"><?php echo htmlspecialchars($post['excerpt']); ?></p>
                    <?php endif; ?>

                    <div class="d-flex flex-wrap align-items-center text-muted">
                        <div class="me-4 mb-2">
                            <i class="bi bi-person me-1"></i>
                            <strong><?php echo htmlspecialchars($post['author_name']); ?></strong>
                        </div>
                        <div class="me-4 mb-2">
                            <i class="bi bi-calendar me-1"></i>
                            <?php echo formatDate($post['published_at']); ?>
                        </div>
                        <div class="me-4 mb-2">
                            <i class="bi bi-clock me-1"></i>
                            <?php echo $post['reading_time']; ?> min read
                        </div>
                        <div class="me-4 mb-2">
                            <i class="bi bi-eye me-1"></i>
                            <?php echo number_format($post['view_count']); ?> views
                        </div>
                    </div>
                </header>

                <!-- Featured Image -->
                <?php if ($post['featured_image']): ?>
                    <div class="mb-4">
                        <img src="<?php echo htmlspecialchars($post['featured_image']); ?>"
                            alt="<?php echo htmlspecialchars($post['title']); ?>"
                            class="img-fluid rounded shadow-sm w-100">
                    </div>
                <?php endif; ?>

                <!-- Article Content -->
                <article class="article-content" id="article-content">
                    <?php echo $post['content']; ?>
                </article>

                <!-- Tags -->
                <?php if (!empty($tags)): ?>
                    <div class="mb-4">
                        <h5 class="mb-3">
                            <i class="bi bi-tags-fill text-primary me-2"></i>
                            Tags
                        </h5>
                        <div class="tag-list">
                            <?php foreach ($tags as $tag): ?>
                                <a href="/blog?tag=<?php echo urlencode($tag['slug']); ?>" class="tag">
                                    <?php echo htmlspecialchars($tag['name']); ?>
                                </a>
                            <?php endforeach; ?>
                        </div>
                    </div>
                <?php endif; ?>

                <!-- Author Bio -->
                <div class="author-bio">
                    <div class="row align-items-center">
                        <div class="col-auto">
                            <?php if ($post['author_avatar']): ?>
                                <img src="<?php echo htmlspecialchars($post['author_avatar']); ?>"
                                    alt="<?php echo htmlspecialchars($post['author_name']); ?>"
                                    class="rounded-circle" width="80" height="80">
                            <?php else: ?>
                                <div class="bg-primary text-white rounded-circle d-flex align-items-center justify-content-center"
                                    style="width: 80px; height: 80px; font-size: 2rem; font-weight: bold;">
                                    <?php echo strtoupper(substr($post['author_name'], 0, 1)); ?>
                                </div>
                            <?php endif; ?>
                        </div>
                        <div class="col">
                            <h5 class="mb-1"><?php echo htmlspecialchars($post['author_name']); ?></h5>
                            <p class="text-muted mb-0">
                                Security expert and content creator at <?php echo APP_NAME; ?>.
                                Passionate about digital privacy and secure communication.
                            </p>
                        </div>
                    </div>
                </div>

                <!-- Share Buttons (Mobile) -->
                <div class="share-buttons d-lg-none">
                    <h5 class="text-center mb-3">Share this article</h5>
                    <a href="https://www.facebook.com/sharer/sharer.php?u=<?php echo urlencode(APP_URL . '/blog/' . $post['slug']); ?>"
                        class="share-btn share-facebook" target="_blank">
                        <i class="bi bi-facebook"></i>
                    </a>
                    <a href="https://twitter.com/intent/tweet?url=<?php echo urlencode(APP_URL . '/blog/' . $post['slug']); ?>&text=<?php echo urlencode($post['title']); ?>"
                        class="share-btn share-twitter" target="_blank">
                        <i class="bi bi-twitter"></i>
                    </a>
                    <a href="https://www.linkedin.com/sharing/share-offsite/?url=<?php echo urlencode(APP_URL . '/blog/' . $post['slug']); ?>"
                        class="share-btn share-linkedin" target="_blank">
                        <i class="bi bi-linkedin"></i>
                    </a>
                    <a href="#" class="share-btn share-copy" onclick="copyUrl()">
                        <i class="bi bi-link-45deg"></i>
                    </a>
                </div>

                <!-- Related Posts -->
                <?php if (!empty($relatedPosts)): ?>
                    <section class="related-posts mt-5">
                        <h3 class="mb-4">
                            <i class="bi bi-bookmark-fill text-primary me-2"></i>
                            Related Articles
                        </h3>
                        <div class="row g-4">
                            <?php foreach ($relatedPosts as $relatedPost): ?>
                                <div class="col-md-4">
                                    <article class="card custom-card h-100">
                                        <div class="card-body">
                                            <?php if ($relatedPost['category_name']): ?>
                                                <span class="badge mb-2" style="background-color: <?php echo $relatedPost['category_color']; ?>;">
                                                    <?php echo htmlspecialchars($relatedPost['category_name']); ?>
                                                </span>
                                            <?php endif; ?>

                                            <h5 class="card-title">
                                                <a href="/blog/<?php echo $relatedPost['slug']; ?>" class="text-decoration-none text-dark">
                                                    <?php echo htmlspecialchars($relatedPost['title']); ?>
                                                </a>
                                            </h5>

                                            <p class="card-text text-muted small">
                                                <?php echo truncateContent($relatedPost['excerpt']); ?>
                                            </p>

                                            <div class="mt-auto">
                                                <small class="text-muted">
                                                    <?php echo timeAgo($relatedPost['published_at']); ?> •
                                                    <?php echo $relatedPost['reading_time']; ?> min read
                                                </small>
                                            </div>
                                        </div>
                                    </article>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    </section>
                <?php endif; ?>

                <!-- Back to Blog -->
                <div class="text-center mt-5">
                    <a href="/blog" class="btn btn-primary btn-lg">
                        <i class="bi bi-arrow-left me-2"></i>
                        Back to Blog
                    </a>
                </div>
            </div>

            <!-- Sidebar -->
            <div class="col-lg-3">
                <!-- Table of Contents -->

                <div class="table-of-contents">
                    <h5 class="fw-bold mb-3">
                        <i class="bi bi-list-ul text-primary me-2"></i>
                        Table of Contents
                    </h5>
                    <div id="toc-list">
                        <!-- Dynamically generated by JavaScript -->
                    </div>
                </div>

                <!-- Quick Actions -->

                <div class="sidebar-widget">
                    <h5>
                        <i class="bi bi-link-45deg text-primary me-2"></i>
                        Quick Links
                    </h5>
                    <div class="list-group list-group-flush">
                        <a href="<?php echo APP_URL; ?>/api-docs/" class="list-group-item list-group-item-action border-0 px-0">
                            <i class="bi bi-code-slash me-2"></i>
                            API Documentation
                        </a>
                        <a href="<?php echo APP_URL; ?>/stats/" class="list-group-item list-group-item-action border-0 px-0">
                            <i class="bi bi-bar-chart me-2"></i>
                            Live Statistics
                        </a>
                        <a href="<?php echo APP_URL; ?>/privacy/" class="list-group-item list-group-item-action border-0 px-0">
                            <i class="bi bi-shield-check me-2"></i>
                            Privacy Policy
                        </a>
                        <a href="<?php echo APP_URL; ?>/" class="list-group-item list-group-item-action border-0 px-0">
                            <i class="bi bi-house me-2"></i>
                            Create Secure Note
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Footer -->
    <?php include "./includes/footer.php" ?>

    <!-- Prism.js for syntax highlighting -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/prism/1.29.0/components/prism-core.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/prism/1.29.0/plugins/autoloader/prism-autoloader.min.js"></script>

    <!-- Custom JavaScript -->
    <script>
        // Reading progress bar
        function updateReadingProgress() {
            const article = document.getElementById('article-content');
            const progressBar = document.getElementById('reading-progress');

            if (!article || !progressBar) return;

            const articleTop = article.offsetTop;
            const articleHeight = article.offsetHeight;
            const scrollTop = window.pageYOffset;
            const windowHeight = window.innerHeight;

            const start = articleTop - windowHeight / 3;
            const end = articleTop + articleHeight - windowHeight / 3;
            const progress = Math.max(0, Math.min(100, (scrollTop - start) / (end - start) * 100));

            progressBar.style.width = progress + '%';
        }

        // Generate table of contents
        function generateTOC() {
            const headings = document.querySelectorAll('#article-content h2, #article-content h3');
            const tocList = document.getElementById('toc-list');

            if (!tocList || headings.length === 0) return;

            headings.forEach((heading, index) => {
                // Add ID to heading if it doesn't have one
                if (!heading.id) {
                    heading.id = 'heading-' + index;
                }

                const link = document.createElement('a');
                link.href = '#' + heading.id;
                link.textContent = heading.textContent;
                link.className = heading.tagName === 'H2' ? '' : 'ms-3';

                link.addEventListener('click', function(e) {
                    e.preventDefault();
                    heading.scrollIntoView({
                        behavior: 'smooth',
                        block: 'start'
                    });

                    // Update active link
                    document.querySelectorAll('#toc-list a').forEach(l => l.classList.remove('active'));
                    link.classList.add('active');
                });

                tocList.appendChild(link);
            });
        }

        // Copy URL function
        function copyUrl() {
            const url = window.location.href;
            navigator.clipboard.writeText(url).then(() => {
                alert('Article URL copied to clipboard!');
            }).catch(() => {
                // Fallback for older browsers
                const input = document.createElement('input');
                input.value = url;
                document.body.appendChild(input);
                input.select();
                document.execCommand('copy');
                document.body.removeChild(input);
                alert('Article URL copied to clipboard!');
            });
        }

        // Highlight current section in TOC
        function highlightCurrentSection() {
            const headings = document.querySelectorAll('#article-content h2, #article-content h3');
            const tocLinks = document.querySelectorAll('#toc-list a');

            let currentHeading = '';
            headings.forEach(heading => {
                const rect = heading.getBoundingClientRect();
                if (rect.top <= 100 && rect.bottom >= 100) {
                    currentHeading = heading.id;
                }
            });

            tocLinks.forEach(link => {
                link.classList.remove('active');
                if (link.getAttribute('href') === '#' + currentHeading) {
                    link.classList.add('active');
                }
            });
        }

        // Initialize everything
        document.addEventListener('DOMContentLoaded', function() {
            generateTOC();
            updateReadingProgress();
            highlightCurrentSection();
        });

        // Update on scroll
        window.addEventListener('scroll', function() {
            updateReadingProgress();
            highlightCurrentSection();
        });

        // Social share tracking (optional analytics)
        document.querySelectorAll('.share-btn').forEach(btn => {
            btn.addEventListener('click', function() {
                const platform = this.classList.contains('share-facebook') ? 'facebook' :
                    this.classList.contains('share-twitter') ? 'twitter' :
                    this.classList.contains('share-linkedin') ? 'linkedin' : 'copy';

                // Track share event (implement your analytics here)
                console.log('Article shared on:', platform);
            });
        });
    </script>
</body>

</html>