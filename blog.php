<?php
require_once 'config/config.php';

// Pagination settings
$postsPerPage = 9;
$currentPage = (int)($_GET['page'] ?? 1);
$offset = ($currentPage - 1) * $postsPerPage;

// Filter settings
$categoryFilter = $_GET['category'] ?? '';
$tagFilter = $_GET['tag'] ?? '';
$searchQuery = $_GET['search'] ?? '';

try {
    $db = Database::getInstance()->getConnection();

    // Build WHERE clause for filters
    $whereConditions = ["bp.status = 'published'"];
    $params = [];

    if (!empty($categoryFilter)) {
        $whereConditions[] = "bc.slug = ?";
        $params[] = $categoryFilter;
    }

    if (!empty($tagFilter)) {
        $whereConditions[] = "EXISTS (SELECT 1 FROM blog_post_tags bpt JOIN blog_tags bt ON bpt.tag_id = bt.id WHERE bpt.post_id = bp.id AND bt.slug = ?)";
        $params[] = $tagFilter;
    }

    if (!empty($searchQuery)) {
        $whereConditions[] = "MATCH(bp.title, bp.excerpt, bp.content) AGAINST(? IN NATURAL LANGUAGE MODE)";
        $params[] = $searchQuery;
    }

    $whereClause = implode(' AND ', $whereConditions);

    // Get total count for pagination
    $countQuery = "
        SELECT COUNT(DISTINCT bp.id) as total
        FROM blog_posts bp 
        LEFT JOIN blog_categories bc ON bp.category_id = bc.id 
        WHERE $whereClause
    ";
    $stmt = $db->prepare($countQuery);
    $stmt->execute($params);
    $totalPosts = $stmt->fetch()['total'];
    $totalPages = ceil($totalPosts / $postsPerPage);

    // Get posts
    $postsQuery = "
        SELECT 
            bp.*,
            bc.name as category_name,
            bc.slug as category_slug,
            bc.color as category_color,
            bc.icon as category_icon
        FROM blog_posts bp 
        LEFT JOIN blog_categories bc ON bp.category_id = bc.id 
        WHERE $whereClause
        ORDER BY bp.is_featured DESC, bp.published_at DESC 
        LIMIT ? OFFSET ?
    ";
    $params[] = $postsPerPage;
    $params[] = $offset;
    $stmt = $db->prepare($postsQuery);
    $stmt->execute($params);
    $posts = $stmt->fetchAll();

    // Get featured post (if on first page and no filters)
    $featuredPost = null;
    if ($currentPage === 1 && empty($categoryFilter) && empty($tagFilter) && empty($searchQuery)) {
        $featuredQuery = "
            SELECT 
                bp.*,
                bc.name as category_name,
                bc.slug as category_slug,
                bc.color as category_color,
                bc.icon as category_icon
            FROM blog_posts bp 
            LEFT JOIN blog_categories bc ON bp.category_id = bc.id 
            WHERE bp.status = 'published' AND bp.is_featured = TRUE
            ORDER BY bp.published_at DESC 
            LIMIT 1
        ";
        $stmt = $db->query($featuredQuery);
        $featuredPost = $stmt->fetch();

        // Remove featured post from regular posts if it exists
        if ($featuredPost) {
            $posts = array_filter($posts, function ($post) use ($featuredPost) {
                return $post['id'] !== $featuredPost['id'];
            });
        }
    }

    // Get categories for filter
    $categoriesQuery = "
        SELECT bc.*, COUNT(bp.id) as post_count
        FROM blog_categories bc 
        LEFT JOIN blog_posts bp ON bc.id = bp.category_id AND bp.status = 'published'
        WHERE bc.is_active = TRUE
        GROUP BY bc.id 
        ORDER BY bc.sort_order, bc.name
    ";
    $stmt = $db->query($categoriesQuery);
    $categories = $stmt->fetchAll();

    // Get popular tags
    $tagsQuery = "
        SELECT bt.*, COUNT(bpt.post_id) as post_count
        FROM blog_tags bt 
        JOIN blog_post_tags bpt ON bt.id = bpt.tag_id
        JOIN blog_posts bp ON bpt.post_id = bp.id AND bp.status = 'published'
        GROUP BY bt.id
        ORDER BY post_count DESC, bt.name
        LIMIT 15
    ";
    $stmt = $db->query($tagsQuery);
    $popularTags = $stmt->fetchAll();
} catch (Exception $e) {
    logError('Blog page error: ' . $e->getMessage());
    $posts = [];
    $categories = [];
    $popularTags = [];
    $totalPages = 1;
    $featuredPost = null;
}

// Helper functions
function truncateContent($content, $length = 150)
{
    $text = strip_tags($content);
    return strlen($text) > $length ? substr($text, 0, $length) . '...' : $text;
}

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
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo APP_NAME; ?> - <?php echo !empty($searchQuery) ? "Search: $searchQuery" : 'Blog'; ?></title>
    <meta name="description" content="Stay informed with the latest insights on digital security, privacy, and secure communication. Expert articles from the SecureNotes team.">
    <meta name="robots" content="index, follow">

    <meta property="og:title" content="<?php echo APP_NAME; ?> - <?php echo !empty($searchQuery) ? "Search: $searchQuery" : 'Blog'; ?>">
    <meta property="og:description" content="Stay informed with the latest insights on digital security, privacy, and secure communication. Expert articles from the SecureNotes team.">
    <meta property="og:image" content="<?php echo APP_URL; ?>/assets/SecureNotes-Icon-sm.png">
    <meta property="og:url" content="<?php echo APP_URL; ?>/blog/">
    <meta property="og:type" content="website">
    <?php include "./includes/head.php" ?>


    <style>
        body.custom-body {
            background: linear-gradient(135deg, #f5f7fa 0%, #c3cfe2 100%) !important;
            min-height: 100vh !important;
        }

        .custom-card {
            border-radius: 12px !important;
            border: none !important;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1) !important;
            transition: all 0.3s ease !important;
        }

        .custom-card:hover {
            transform: translateY(-2px) !important;
            box-shadow: 0 8px 15px rgba(0, 0, 0, 0.15) !important;
        }

        .blog-hero {
            background: linear-gradient(135deg, rgba(0, 123, 255, 0.1) 0%, rgba(0, 123, 255, 0.05) 100%) !important;
            border-radius: 16px !important;
            padding: 3rem 2rem !important;
            margin-bottom: 3rem !important;
        }

        .featured-post {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%) !important;
            color: white !important;
            border-radius: 16px !important;
            overflow: hidden !important;
            margin-bottom: 3rem !important;
        }

        .featured-post .card-body {
            padding: 2.5rem !important;
        }

        .blog-card {
            height: 100% !important;
            transition: all 0.3s ease !important;
        }

        .blog-card:hover {
            transform: translateY(-5px) !important;
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.15) !important;
        }

        .category-badge {
            padding: 0.5rem 1rem !important;
            border-radius: 20px !important;
            font-size: 0.8rem !important;
            font-weight: 500 !important;
            text-decoration: none !important;
            display: inline-flex !important;
            align-items: center !important;
            margin-bottom: 1rem !important;
        }

        .tag-cloud .tag {
            display: inline-block !important;
            padding: 0.25rem 0.75rem !important;
            margin: 0.25rem !important;
            background: white !important;
            border: 1px solid #e9ecef !important;
            border-radius: 15px !important;
            text-decoration: none !important;
            color: #495057 !important;
            font-size: 0.8rem !important;
            transition: all 0.3s ease !important;
        }

        .tag-cloud .tag:hover {
            background: #667eea !important;
            color: white !important;
            border-color: #667eea !important;
        }

        .filter-section {
            background: white !important;
            border-radius: 12px !important;
            padding: 1.5rem !important;
            margin-bottom: 2rem !important;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1) !important;
        }

        .search-box {
            position: relative !important;
        }

        .search-box .form-control {
            padding-left: 2.5rem !important;
            border-radius: 25px !important;
            border: 2px solid #e9ecef !important;
        }

        .search-box .bi-search {
            position: absolute !important;
            left: 1rem !important;
            top: 50% !important;
            transform: translateY(-50%) !important;
            color: #6c757d !important;
        }

        .pagination {
            justify-content: center !important;
        }

        .page-link {
            border: none !important;
            margin: 0 0.25rem !important;
            border-radius: 8px !important;
            color: #495057 !important;
        }

        .page-link:hover,
        .page-item.active .page-link {
            background-color: #667eea !important;
            color: white !important;
        }

        .sidebar-widget {
            background: white !important;
            border-radius: 12px !important;
            padding: 1.5rem !important;
            margin-bottom: 1.5rem !important;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1) !important;
        }

        .sidebar-widget h5 {
            color: #495057 !important;
            margin-bottom: 1rem !important;
            font-weight: 600 !important;
        }

        .reading-time {
            color: #6c757d !important;
            font-size: 0.9rem !important;
        }

        .view-count {
            color: #6c757d !important;
            font-size: 0.9rem !important;
        }

        .no-results {
            text-align: center !important;
            padding: 3rem 2rem !important;
            color: #6c757d !important;
        }
    </style>

</head>

<body class="custom-body">
    <!-- Navigation -->
    <?php include "./includes/nav.php" ?>


    <div class="container py-5">
        <!-- Blog Hero Section -->
        <?php if ($currentPage === 1 && empty($categoryFilter) && empty($tagFilter) && empty($searchQuery)): ?>
            <div class="blog-hero text-center">
                <i class="bi bi-newspaper display-4 text-primary mb-3"></i>
                <h1 class="h2 fw-bold mb-3">Security & Privacy Blog</h1>
                <p class="lead text-muted mb-0">Stay informed with the latest insights on digital security, privacy, and secure communication</p>
            </div>
        <?php endif; ?>

        <!-- Search and Filters -->
        <div class="filter-section">
            <div class="row align-items-center">
                <div class="col-lg-6">
                    <form method="GET" action="/blog" class="search-box">
                        <i class="bi bi-search"></i>
                        <input type="text" name="search" class="form-control" placeholder="Search articles..."
                            value="<?php echo htmlspecialchars($searchQuery); ?>">
                        <?php if ($categoryFilter): ?>
                            <input type="hidden" name="category" value="<?php echo htmlspecialchars($categoryFilter); ?>">
                        <?php endif; ?>
                        <?php if ($tagFilter): ?>
                            <input type="hidden" name="tag" value="<?php echo htmlspecialchars($tagFilter); ?>">
                        <?php endif; ?>
                    </form>
                </div>
                <div class="col-lg-6">
                    <div class="d-flex justify-content-lg-end justify-content-start mt-3 mt-lg-0">
                        <?php if ($categoryFilter || $tagFilter || $searchQuery): ?>
                            <a href="/blog" class="btn btn-primary me-2">
                                <i class="bi bi-x-circle me-1"></i>Clear Filters
                            </a>
                        <?php endif; ?>
                        <div class="dropdown">
                            <button class="btn btn-secondary dropdown-toggle" type="button" data-bs-toggle="dropdown">
                                <i class="bi bi-funnel me-1"></i>Categories
                            </button>
                            <ul class="dropdown-menu">
                                <li><a class="dropdown-item" href="/blog">All Categories</a></li>
                                <?php foreach ($categories as $category): ?>
                                    <li>
                                        <a class="dropdown-item <?php echo $categoryFilter === $category['slug'] ? 'active' : ''; ?>"
                                            href="/blog?category=<?php echo urlencode($category['slug']); ?>">
                                            <i class="<?php echo $category['icon']; ?> me-2" style="color: <?php echo $category['color']; ?>"></i>
                                            <?php echo htmlspecialchars($category['name']); ?>
                                            <span class="badge bg-light text-dark ms-2"><?php echo $category['post_count']; ?></span>
                                        </a>
                                    </li>
                                <?php endforeach; ?>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="row">
            <!-- Main Content -->
            <div class="col-lg-8">
                <!-- Featured Post -->
                <?php if ($featuredPost): ?>
                    <article class="featured-post custom-card">
                        <div class="card-body">
                            <div class="d-flex align-items-center mb-3">
                                <span class="badge bg-light text-dark">
                                    <i class="bi bi-star-fill text-warning me-1"></i>
                                    Featured
                                </span>
                                <span class="ms-auto text-white-50">
                                    <i class="bi bi-clock me-1"></i>
                                    <?php echo $featuredPost['reading_time']; ?> min read
                                </span>
                            </div>
                            <h2 class="card-title h3 mb-3">
                                <a href="/blog/<?php echo $featuredPost['slug']; ?>" class="text-white text-decoration-none">
                                    <?php echo htmlspecialchars($featuredPost['title']); ?>
                                </a>
                            </h2>
                            <p class="card-text text-white-75 mb-3">
                                <?php echo htmlspecialchars($featuredPost['excerpt']); ?>
                            </p>
                            <div class="d-flex align-items-center justify-content-between">
                                <div class="d-flex align-items-center text-white-50">
                                    <small>
                                        <i class="bi bi-person me-1"></i>
                                        <?php echo htmlspecialchars($featuredPost['author_name']); ?>
                                    </small>
                                    <small class="ms-3">
                                        <i class="bi bi-calendar me-1"></i>
                                        <?php echo formatDate($featuredPost['published_at']); ?>
                                    </small>
                                </div>
                                <a href="/blog/<?php echo $featuredPost['slug']; ?>" class="btn btn-light">
                                    Read More <i class="bi bi-arrow-right ms-1"></i>
                                </a>
                            </div>
                        </div>
                    </article>
                <?php endif; ?>

                <!-- Blog Posts Grid -->
                <?php if (!empty($posts)): ?>
                    <div class="row g-4">
                        <?php foreach ($posts as $post): ?>
                            <div class="col-md-6 col-lg-4">
                                <article class="card blog-card custom-card h-100">
                                    <div class="card-body">
                                        <?php if ($post['category_name']): ?>
                                            <a href="/blog?category=<?php echo urlencode($post['category_slug']); ?>"
                                                class="category-badge"
                                                style="background-color: <?php echo $post['category_color']; ?>15; color: <?php echo $post['category_color']; ?>;">
                                                <i class="<?php echo $post['category_icon']; ?> me-1"></i>
                                                <?php echo htmlspecialchars($post['category_name']); ?>
                                            </a>
                                        <?php endif; ?>

                                        <h3 class="card-title h5 mb-3">
                                            <a href="/blog/<?php echo $post['slug']; ?>" class="text-decoration-none text-dark">
                                                <?php echo htmlspecialchars($post['title']); ?>
                                            </a>
                                        </h3>

                                        <p class="card-text text-muted mb-3">
                                            <?php echo truncateContent($post['excerpt']); ?>
                                        </p>

                                        <div class="mt-auto">
                                            <div class="d-flex align-items-center justify-content-between text-muted small mb-3">
                                                <span>
                                                    <i class="bi bi-person me-1"></i>
                                                    <?php echo htmlspecialchars($post['author_name']); ?>
                                                </span>
                                                <span>
                                                    <i class="bi bi-clock me-1"></i>
                                                    <?php echo $post['reading_time']; ?> min
                                                </span>
                                            </div>

                                            <div class="d-flex align-items-center justify-content-between">
                                                <small class="text-muted">
                                                    <?php echo timeAgo($post['published_at']); ?>
                                                </small>
                                                <a href="<?php echo APP_URL; ?>/blog/<?php echo $post['slug']; ?>" class="btn btn-sm btn-primary">
                                                    Read More
                                                </a>
                                            </div>
                                        </div>
                                    </div>
                                </article>
                            </div>
                        <?php endforeach; ?>
                    </div>

                    <!-- Pagination -->
                    <?php if ($totalPages > 1): ?>
                        <nav aria-label="Blog pagination" class="mt-5">
                            <ul class="pagination">
                                <?php if ($currentPage > 1): ?>
                                    <li class="page-item">
                                        <a class="page-link" href="?page=<?php echo $currentPage - 1; ?><?php echo $categoryFilter ? '&category=' . urlencode($categoryFilter) : ''; ?><?php echo $tagFilter ? '&tag=' . urlencode($tagFilter) : ''; ?><?php echo $searchQuery ? '&search=' . urlencode($searchQuery) : ''; ?>">
                                            <i class="bi bi-chevron-left"></i>
                                        </a>
                                    </li>
                                <?php endif; ?>

                                <?php for ($i = max(1, $currentPage - 2); $i <= min($totalPages, $currentPage + 2); $i++): ?>
                                    <li class="page-item <?php echo $i === $currentPage ? 'active' : ''; ?>">
                                        <a class="page-link" href="?page=<?php echo $i; ?><?php echo $categoryFilter ? '&category=' . urlencode($categoryFilter) : ''; ?><?php echo $tagFilter ? '&tag=' . urlencode($tagFilter) : ''; ?><?php echo $searchQuery ? '&search=' . urlencode($searchQuery) : ''; ?>">
                                            <?php echo $i; ?>
                                        </a>
                                    </li>
                                <?php endfor; ?>

                                <?php if ($currentPage < $totalPages): ?>
                                    <li class="page-item">
                                        <a class="page-link" href="?page=<?php echo $currentPage + 1; ?><?php echo $categoryFilter ? '&category=' . urlencode($categoryFilter) : ''; ?><?php echo $tagFilter ? '&tag=' . urlencode($tagFilter) : ''; ?><?php echo $searchQuery ? '&search=' . urlencode($searchQuery) : ''; ?>">
                                            <i class="bi bi-chevron-right"></i>
                                        </a>
                                    </li>
                                <?php endif; ?>
                            </ul>
                        </nav>
                    <?php endif; ?>

                <?php else: ?>
                    <!-- No Results -->
                    <div class="no-results">
                        <i class="bi bi-search display-4 text-muted mb-3"></i>
                        <h3>No articles found</h3>
                        <p class="text-muted">
                            <?php if ($searchQuery): ?>
                                No articles match your search for "<?php echo htmlspecialchars($searchQuery); ?>".
                            <?php elseif ($categoryFilter): ?>
                                No articles found in this category.
                            <?php elseif ($tagFilter): ?>
                                No articles found with this tag.
                            <?php else: ?>
                                No articles have been published yet.
                            <?php endif; ?>
                        </p>
                        <a href="/blog" class="btn btn-primary">View All Articles</a>
                    </div>
                <?php endif; ?>
            </div>

            <!-- Sidebar -->
            <div class="col-lg-4">
                <!-- Popular Tags -->
                <?php if (!empty($popularTags)): ?>
                    <div class="sidebar-widget">
                        <h5>
                            <i class="bi bi-tags-fill text-primary me-2"></i>
                            Popular Tags
                        </h5>
                        <div class="tag-cloud">
                            <?php foreach ($popularTags as $tag): ?>
                                <a href="<?php echo APP_URL; ?>/blog?tag=<?php echo urlencode($tag['slug']); ?>" class="tag">
                                    <?php echo htmlspecialchars($tag['name']); ?>
                                    <small>(<?php echo $tag['post_count']; ?>)</small>
                                </a>
                            <?php endforeach; ?>
                        </div>
                    </div>
                <?php endif; ?>

                <!-- Categories -->
                <?php if (!empty($categories)): ?>
                    <div class="sidebar-widget">
                        <h5>
                            <i class="bi bi-folder-fill text-primary me-2"></i>
                            Categories
                        </h5>
                        <div class="list-group list-group-flush">
                            <?php foreach ($categories as $category): ?>
                                <a href="<?php echo APP_URL; ?>/blog?category=<?php echo urlencode($category['slug']); ?>"
                                    class="list-group-item list-group-item-action d-flex justify-content-between align-items-center border-0 px-0">
                                    <span>
                                        <i class="<?php echo $category['icon']; ?> me-2" style="color: <?php echo $category['color']; ?>"></i>
                                        <?php echo htmlspecialchars($category['name']); ?>
                                    </span>
                                    <span class="badge bg-light text-dark"><?php echo $category['post_count']; ?></span>
                                </a>
                            <?php endforeach; ?>
                        </div>
                    </div>
                <?php endif; ?>

                <!-- Newsletter Signup -->
                <div class="sidebar-widget">
                    <h5>
                        <i class="bi bi-envelope-fill text-primary me-2"></i>
                        Stay Updated
                    </h5>
                    <p class="text-muted small mb-3">Get the latest security insights delivered to your inbox.</p>
                    <form>
                        <div class="mb-3">
                            <input type="email" class="form-control" placeholder="Enter your email" required>
                        </div>
                        <button type="submit" class="btn btn-primary w-100">
                            <i class="bi bi-bell me-1"></i>
                            Subscribe
                        </button>
                    </form>
                    <small class="text-muted">No spam, unsubscribe anytime.</small>
                </div>

                <!-- Quick Links -->
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



    <!-- Custom JavaScript -->
    <script>
        // Auto-submit search form on enter
        document.querySelector('input[name="search"]').addEventListener('keypress', function(e) {
            if (e.key === 'Enter') {
                this.closest('form').submit();
            }
        });

        // Smooth scroll for internal links
        document.querySelectorAll('a[href^="#"]').forEach(anchor => {
            anchor.addEventListener('click', function(e) {
                e.preventDefault();
                const target = document.querySelector(this.getAttribute('href'));
                if (target) {
                    target.scrollIntoView({
                        behavior: 'smooth',
                        block: 'start'
                    });
                }
            });
        });

        // Newsletter form handling
        document.querySelector('form').addEventListener('submit', function(e) {
            e.preventDefault();
            const email = this.querySelector('input[type="email"]').value;
            if (email) {
                alert('Thanks for subscribing! We\'ll keep you updated on the latest security insights.');
                this.reset();
            }
        });
    </script>
</body>

</html>