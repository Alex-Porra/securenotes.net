<?php
require_once 'config/config.php';
// require_once 'classes/Database.php';

if (!$_SESSION['admin_logged_in'] === true) {
    header('Location: /login/');
    exit;
}


// Initialize variables
$action = isset($_GET['action']) ? $_GET['action'] : 'list';
$post_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
$message = '';
$error = '';

// Database connection
$db = Database::getInstance()->getConnection();

// Handle form submissions
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['save_post'])) {
        // Prepare post data
        $title = trim($_POST['title']);
        $slug = trim($_POST['slug']) ?: createSlug($title);
        $excerpt = trim($_POST['excerpt']);
        $content = trim($_POST['content']);
        $category_id = (int)$_POST['category_id'];
        $status = $_POST['status'];
        $is_featured = isset($_POST['is_featured']) ? 1 : 0;
        $meta_title = trim($_POST['meta_title']) ?: $title;
        $meta_description = trim($_POST['meta_description']) ?: $excerpt;
        $meta_keywords = trim($_POST['meta_keywords']);
        $author_name = trim($_POST['author_name']);
        $author_email = trim($_POST['author_email']);
        $reading_time = estimateReadingTime($content);

        // Calculate reading time (words per minute)
        $reading_time = estimateReadingTime($content);

        try {
            // Check if we're updating or creating
            if ($post_id > 0) {
                // Update existing post
                $stmt = $db->prepare("
                    UPDATE blog_posts SET 
                    title = ?, slug = ?, excerpt = ?, content = ?, 
                    category_id = ?, status = ?, is_featured = ?,
                    meta_title = ?, meta_description = ?, meta_keywords = ?,
                    author_name = ?, author_email = ?, reading_time = ?,
                    updated_at = NOW()
                    WHERE id = ?
                ");

                $stmt->execute([
                    $title,
                    $slug,
                    $excerpt,
                    $content,
                    $category_id,
                    $status,
                    $is_featured,
                    $meta_title,
                    $meta_description,
                    $meta_keywords,
                    $author_name,
                    $author_email,
                    $reading_time,
                    $post_id
                ]);

                $message = "Post updated successfully!";
            } else {
                // Create new post
                $stmt = $db->prepare("
                    INSERT INTO blog_posts 
                    (title, slug, excerpt, content, category_id, status, 
                    is_featured, meta_title, meta_description, meta_keywords,
                    author_name, author_email, reading_time, 
                    published_at, created_at, updated_at) 
                    VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, 
                    CASE WHEN ? = 'published' THEN NOW() ELSE NULL END, NOW(), NOW())
                ");

                $stmt->execute([
                    $title,
                    $slug,
                    $excerpt,
                    $content,
                    $category_id,
                    $status,
                    $is_featured,
                    $meta_title,
                    $meta_description,
                    $meta_keywords,
                    $author_name,
                    $author_email,
                    $reading_time,
                    $status
                ]);

                $post_id = $db->lastInsertId();
                $message = "Post created successfully!";
            }

            // Handle tags if submitted
            if (isset($_POST['tags']) && is_array($_POST['tags'])) {
                // First delete existing tag connections
                $stmt = $db->prepare("DELETE FROM blog_post_tags WHERE post_id = ?");
                $stmt->execute([$post_id]);

                // Then add new ones
                foreach ($_POST['tags'] as $tag_id) {
                    $stmt = $db->prepare("INSERT INTO blog_post_tags (post_id, tag_id) VALUES (?, ?)");
                    $stmt->execute([$post_id, (int)$tag_id]);

                    // Update tag usage count
                    $db->prepare("UPDATE blog_tags SET usage_count = usage_count + 1 WHERE id = ?")->execute([(int)$tag_id]);
                }
            }

            // Redirect to avoid form resubmission
            header("Location: /blog-admin/?action=edit&id={$post_id}&success=1");
            exit;
        } catch (PDOException $e) {
            $error = "Database error: " . $e->getMessage();
        }
    } elseif (isset($_POST['delete_post']) && $post_id > 0) {
        try {
            // Delete post
            $stmt = $db->prepare("DELETE FROM blog_posts WHERE id = ?");
            $stmt->execute([$post_id]);

            $message = "Post deleted successfully!";

            // Redirect to list view
            header("Location: /blog-admin/?deleted=1");
            exit;
        } catch (PDOException $e) {
            $error = "Error deleting post: " . $e->getMessage();
        }
    }
}

// Handle success message from redirects
if (isset($_GET['success'])) {
    $message = "Operation completed successfully!";
}
if (isset($_GET['deleted'])) {
    $message = "Post deleted successfully!";
}

// Function to create a slug from a title
function createSlug($string)
{
    $string = strtolower($string);
    $string = preg_replace('/[^a-z0-9\s-]/', '', $string);
    $string = preg_replace('/[\s-]+/', '-', $string);
    $string = trim($string, '-');
    return $string;
}

// Function to estimate reading time
function estimateReadingTime($content)
{
    $words = str_word_count(strip_tags($content));
    $minutes = ceil($words / 200); // Assuming 200 words per minute reading speed
    return max(1, $minutes); // Minimum 1 minute
}

// Get post data if editing
$post = null;
$selected_tags = [];
if ($action === 'edit' && $post_id > 0) {
    try {
        $stmt = $db->prepare("SELECT * FROM blog_posts WHERE id = ?");
        $stmt->execute([$post_id]);
        $post = $stmt->fetch();

        if (!$post) {
            $error = "Post not found!";
            $action = 'list';
        } else {
            // Get selected tags for this post
            $stmt = $db->prepare("
                SELECT tag_id FROM blog_post_tags WHERE post_id = ?
            ");
            $stmt->execute([$post_id]);
            $tag_rows = $stmt->fetchAll();
            $selected_tags = array_column($tag_rows, 'tag_id');
        }
    } catch (PDOException $e) {
        $error = "Error fetching post: " . $e->getMessage();
    }
}

// Get all posts for listing
$posts = [];
if ($action === 'list') {
    try {
        $stmt = $db->query("
            SELECT p.*, c.name as category_name 
            FROM blog_posts p
            LEFT JOIN blog_categories c ON p.category_id = c.id
            ORDER BY p.created_at DESC
        ");
        $posts = $stmt->fetchAll();
    } catch (PDOException $e) {
        $error = "Error fetching posts: " . $e->getMessage();
    }
}

// Get all categories for dropdown
$categories = [];
try {
    $stmt = $db->query("SELECT id, name FROM blog_categories WHERE is_active = 1 ORDER BY sort_order, name");
    $categories = $stmt->fetchAll();
} catch (PDOException $e) {
    $error = "Error fetching categories: " . $e->getMessage();
}

// Get all tags for checkbox list
$tags = [];
try {
    $stmt = $db->query("SELECT id, name, color FROM blog_tags ORDER BY name");
    $tags = $stmt->fetchAll();
} catch (PDOException $e) {
    $error = "Error fetching tags: " . $e->getMessage();
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo APP_NAME; ?> - Blog Management</title>
    <meta name="robots" content="noindex, nofollow">
    <?php include "./includes/head.php" ?>

    <!-- Additional CSS for the blog admin -->
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

        .tag-badge {
            display: inline-block;
            margin-right: 5px;
            margin-bottom: 5px;
        }

        .post-status {
            font-size: 0.8rem;
            padding: 0.2rem 0.5rem;
            border-radius: 4px;
        }

        .status-draft {
            background-color: #e2e3e5;
            color: #383d41;
        }

        .status-published {
            background-color: #d4edda;
            color: #155724;
        }

        .status-archived {
            background-color: #f8d7da;
            color: #721c24;
        }

        .featured-star {
            color: #ffc107;
        }

        .tag-item {
            display: inline-block;
            margin: 0.2rem;
            padding: 0.3rem 0.6rem;
            border-radius: 20px;
            font-size: 0.85rem;
        }

        .actions-column {
            white-space: nowrap;
        }

        .post-item:hover {
            background-color: #f8f9fa;
        }
    </style>
</head>

<body class="custom-body">
    <!-- Navigation -->
    <?php include "./includes/nav.php" ?>

    <div class="container py-5">
        <?php if ($message): ?>
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <?php echo $message; ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        <?php endif; ?>

        <?php if ($error): ?>
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <?php echo $error; ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        <?php endif; ?>

        <div class="card custom-card">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <h1 class="h3 mb-0 text-primary">
                        <?php if ($action === 'edit'): ?>
                            <?php echo $post_id > 0 ? 'Edit Post' : 'Create New Post'; ?>
                        <?php else: ?>
                            Blog Posts
                        <?php endif; ?>
                    </h1>

                    <?php if ($action === 'list'): ?>
                        <a href="?action=edit" class="btn btn-primary">
                            <i class="bi bi-plus-circle"></i> New Post
                        </a>
                    <?php else: ?>
                        <a href="?action=list" class="btn btn-secondary">
                            <i class="bi bi-arrow-left"></i> Back to List
                        </a>
                    <?php endif; ?>
                </div>

                <?php if ($action === 'list'): ?>
                    <!-- Post listing -->
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead class="table-light">
                                <tr>
                                    <th>Title</th>
                                    <th>Category</th>
                                    <th>Status</th>
                                    <th>Published</th>
                                    <th>Views</th>
                                    <th class="text-end">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (empty($posts)): ?>
                                    <tr>
                                        <td colspan="6" class="text-center py-4">
                                            <i class="bi bi-journal-x text-muted display-4 d-block mb-2"></i>
                                            <p class="text-muted">No blog posts found.</p>
                                            <a href="?action=edit" class="btn btn-sm btn-primary">Create your first post</a>
                                        </td>
                                    </tr>
                                <?php else: ?>
                                    <?php foreach ($posts as $post): ?>
                                        <tr class="post-item">
                                            <td>
                                                <div class="d-flex align-items-center">
                                                    <?php if ($post['is_featured']): ?>
                                                        <i class="bi bi-star-fill featured-star me-2" title="Featured Post"></i>
                                                    <?php endif; ?>
                                                    <div>
                                                        <a href="?action=edit&id=<?php echo $post['id']; ?>" class="text-decoration-none">
                                                            <?php echo htmlspecialchars($post['title']); ?>
                                                        </a>
                                                        <small class="d-block text-muted">
                                                            <?php echo substr(htmlspecialchars($post['excerpt']), 0, 60); ?>...
                                                        </small>
                                                    </div>
                                                </div>
                                            </td>
                                            <td>
                                                <?php echo htmlspecialchars($post['category_name'] ?? 'Uncategorized'); ?>
                                            </td>
                                            <td>
                                                <span class="post-status status-<?php echo $post['status']; ?>">
                                                    <?php echo ucfirst($post['status']); ?>
                                                </span>
                                            </td>
                                            <td>
                                                <?php if ($post['published_at']): ?>
                                                    <?php echo date('M j, Y', strtotime($post['published_at'])); ?>
                                                <?php else: ?>
                                                    <span class="text-muted">-</span>
                                                <?php endif; ?>
                                            </td>
                                            <td>
                                                <?php echo number_format($post['view_count']); ?>
                                            </td>
                                            <td class="text-end actions-column">
                                                <a href="?action=edit&id=<?php echo $post['id']; ?>" class="btn btn-sm btn-outline-primary me-1" title="Edit">
                                                    <i class="bi bi-pencil"></i>
                                                </a>
                                                <?php if ($post['status'] === 'published'): ?>
                                                    <a href="/blog/<?php echo $post['slug']; ?>" target="_blank" class="btn btn-sm btn-outline-success me-1" title="View">
                                                        <i class="bi bi-eye"></i>
                                                    </a>
                                                <?php endif; ?>
                                                <button type="button" class="btn btn-sm btn-outline-danger"
                                                    onclick="confirmDelete(<?php echo $post['id']; ?>, '<?php echo addslashes($post['title']); ?>')"
                                                    title="Delete">
                                                    <i class="bi bi-trash"></i>
                                                </button>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>

                <?php else: ?>
                    <!-- Post form -->
                    <form method="post" action="/blog-admin/?action=edit&id=<?php echo $post_id; ?>">
                        <div class="row mb-4">
                            <div class="col-md-8">
                                <div class="form-group mb-3">
                                    <label for="title" class="form-label">Title <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control" id="title" name="title" required
                                        value="<?php echo htmlspecialchars($post['title'] ?? ''); ?>"
                                        placeholder="Enter post title">
                                </div>

                                <div class="form-group mb-3">
                                    <label for="slug" class="form-label">Slug</label>
                                    <div class="input-group">
                                        <span class="input-group-text">/blog/</span>
                                        <input type="text" class="form-control" id="slug" name="slug"
                                            value="<?php echo htmlspecialchars($post['slug'] ?? ''); ?>"
                                            placeholder="auto-generated-if-empty">
                                    </div>
                                    <small class="text-muted">Leave empty to auto-generate from title</small>
                                </div>

                                <div class="form-group mb-3">
                                    <label for="excerpt" class="form-label">Excerpt</label>
                                    <textarea class="form-control" id="excerpt" name="excerpt" rows="3"
                                        placeholder="Brief description for listings and social sharing"><?php echo htmlspecialchars($post['excerpt'] ?? ''); ?></textarea>
                                </div>

                                <div class="form-group mb-3">
                                    <label for="content" class="form-label">Content <span class="text-danger">*</span></label>
                                    <textarea class="form-control" id="content" name="content" rows="12" required
                                        placeholder="Write your blog post content here"><?php echo htmlspecialchars($post['content'] ?? ''); ?></textarea>
                                </div>
                            </div>

                            <div class="col-md-4">
                                <div class="card mb-3">
                                    <div class="card-header bg-light">
                                        <strong>Publication</strong>
                                    </div>
                                    <div class="card-body">
                                        <div class="form-group mb-3">
                                            <label for="status" class="form-label">Status</label>
                                            <select class="form-select" id="status" name="status">
                                                <option value="draft" <?php echo (($post['status'] ?? 'draft') === 'draft') ? 'selected' : ''; ?>>Draft</option>
                                                <option value="published" <?php echo (($post['status'] ?? '') === 'published') ? 'selected' : ''; ?>>Published</option>
                                                <option value="archived" <?php echo (($post['status'] ?? '') === 'archived') ? 'selected' : ''; ?>>Archived</option>
                                            </select>
                                        </div>

                                        <div class="form-check mb-3">
                                            <input class="form-check-input" type="checkbox" id="is_featured" name="is_featured" value="1"
                                                <?php echo (($post['is_featured'] ?? 0) == 1) ? 'checked' : ''; ?>>
                                            <label class="form-check-label" for="is_featured">
                                                Featured Post
                                            </label>
                                        </div>

                                        <div class="form-group mb-3">
                                            <label for="category_id" class="form-label">Category</label>
                                            <select class="form-select" id="category_id" name="category_id">
                                                <option value="">-- Select Category --</option>
                                                <?php foreach ($categories as $category): ?>
                                                    <option value="<?php echo $category['id']; ?>"
                                                        <?php echo (($post['category_id'] ?? 0) == $category['id']) ? 'selected' : ''; ?>>
                                                        <?php echo htmlspecialchars($category['name']); ?>
                                                    </option>
                                                <?php endforeach; ?>
                                            </select>
                                        </div>

                                        <div class="form-group mb-3">
                                            <label class="form-label">Tags</label>
                                            <div class="border rounded p-2" style="max-height: 150px; overflow-y: auto;">
                                                <?php foreach ($tags as $tag): ?>
                                                    <div class="form-check">
                                                        <input class="form-check-input" type="checkbox" name="tags[]"
                                                            value="<?php echo $tag['id']; ?>" id="tag<?php echo $tag['id']; ?>"
                                                            <?php echo in_array($tag['id'], $selected_tags) ? 'checked' : ''; ?>>
                                                        <label class="form-check-label" for="tag<?php echo $tag['id']; ?>">
                                                            <span class="tag-item" style="background-color: <?php echo $tag['color']; ?>20; color: <?php echo $tag['color']; ?>;">
                                                                <?php echo htmlspecialchars($tag['name']); ?>
                                                            </span>
                                                        </label>
                                                    </div>
                                                <?php endforeach; ?>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="card mb-3">
                                    <div class="card-header bg-light">
                                        <strong>Author</strong>
                                    </div>
                                    <div class="card-body">
                                        <div class="form-group mb-3">
                                            <label for="author_name" class="form-label">Name <span class="text-danger">*</span></label>
                                            <input type="text" class="form-control" id="author_name" name="author_name" required
                                                value="<?php echo htmlspecialchars($post['author_name'] ?? ''); ?>">
                                        </div>

                                        <div class="form-group mb-3">
                                            <label for="author_email" class="form-label">Email</label>
                                            <input type="email" class="form-control" id="author_email" name="author_email"
                                                value="<?php echo htmlspecialchars($post['author_email'] ?? ''); ?>">
                                        </div>
                                    </div>
                                </div>

                                <div class="card mb-3">
                                    <div class="card-header bg-light">
                                        <strong>SEO Settings</strong>
                                    </div>
                                    <div class="card-body">
                                        <div class="form-group mb-3">
                                            <label for="meta_title" class="form-label">Meta Title</label>
                                            <input type="text" class="form-control" id="meta_title" name="meta_title"
                                                value="<?php echo htmlspecialchars($post['meta_title'] ?? ''); ?>"
                                                placeholder="Leave empty to use post title">
                                        </div>

                                        <div class="form-group mb-3">
                                            <label for="meta_description" class="form-label">Meta Description</label>
                                            <textarea class="form-control" id="meta_description" name="meta_description" rows="2"
                                                placeholder="Leave empty to use excerpt"><?php echo htmlspecialchars($post['meta_description'] ?? ''); ?></textarea>
                                        </div>

                                        <div class="form-group mb-3">
                                            <label for="meta_keywords" class="form-label">Meta Keywords</label>
                                            <input type="text" class="form-control" id="meta_keywords" name="meta_keywords"
                                                value="<?php echo htmlspecialchars($post['meta_keywords'] ?? ''); ?>"
                                                placeholder="Comma-separated keywords">
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="border-top pt-3 d-flex justify-content-between">
                            <div>
                                <?php if ($post_id > 0): ?>
                                    <button type="button" class="btn btn-danger" onclick="confirmDelete(<?php echo $post_id; ?>, '<?php echo addslashes($post['title']); ?>')">
                                        <i class="bi bi-trash"></i> Delete
                                    </button>
                                <?php endif; ?>
                            </div>

                            <div>
                                <a href="?action=list" class="btn btn-secondary me-2">Cancel</a>
                                <button type="submit" name="save_post" class="btn btn-primary">
                                    <i class="bi bi-save"></i> Save Post
                                </button>
                            </div>
                        </div>
                    </form>

                    <!-- Hidden form for delete action -->
                    <form id="delete-form" method="post" action="/blog-admin/?action=edit&id=<?php echo $post_id; ?>" style="display: none;">
                        <input type="hidden" name="delete_post" value="1">
                    </form>
                <?php endif; ?>

            </div>
        </div>
    </div>

    <!-- Footer -->
    <?php include "./includes/footer.php" ?>

    <!-- Delete confirmation modal -->
    <div class="modal fade" id="deleteModal" tabindex="-1" aria-labelledby="deleteModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="deleteModalLabel">Confirm Delete</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <p>Are you sure you want to delete the post "<span id="delete-post-title"></span>"?</p>
                    <p class="text-danger"><strong>Warning:</strong> This action cannot be undone.</p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="button" class="btn btn-danger" id="confirm-delete">Delete</button>
                </div>
            </div>
        </div>
    </div>

    <script>
        // Function to convert title to slug
        document.getElementById('title')?.addEventListener('blur', function() {
            const slugField = document.getElementById('slug');
            if (slugField && !slugField.value) {
                const title = this.value.toLowerCase();
                const slug = title
                    .replace(/[^\w\s-]/g, '') // Remove special chars
                    .replace(/\s+/g, '-') // Replace spaces with hyphens
                    .replace(/-+/g, '-') // Replace multiple hyphens with single
                    .trim(); // Trim leading/trailing hyphens

                slugField.value = slug;
            }
        });

        // Delete confirmation
        function confirmDelete(postId, postTitle) {
            const modal = new bootstrap.Modal(document.getElementById('deleteModal'));
            document.getElementById('delete-post-title').textContent = postTitle;

            document.getElementById('confirm-delete').onclick = function() {
                document.getElementById('delete-form').action = `/blog-admin/?action=edit&id=${postId}`;
                document.getElementById('delete-form').submit();
            };

            modal.show();
        }

        // Initialize bootstrap tooltips
        var tooltipTriggerList = [].slice.call(document.querySelectorAll('[title]'))
        var tooltipList = tooltipTriggerList.map(function(tooltipTriggerEl) {
            return new bootstrap.Tooltip(tooltipTriggerEl)
        });
    </script>
</body>

</html>