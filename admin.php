<?php
session_start();

// Configuration
$password = 'admin123'; // Simple hardcoded password
$dataFile = 'data/posts.json';
$uploadDir = 'uploads/';

// Ensure directories exist
if (!file_exists('data')) { mkdir('data', 0777, true); }
if (!file_exists($uploadDir)) { mkdir($uploadDir, 0777, true); }
if (!file_exists($dataFile)) { file_put_contents($dataFile, '[]'); }

// Handle Login
if (isset($_POST['login'])) {
    if ($_POST['password'] === $password) {
        $_SESSION['admin_logged_in'] = true;
        header('Location: admin.php');
        exit;
    } else {
        $error = "Contraseña incorrecta";
    }
}

// Handle Logout
if (isset($_GET['logout'])) {
    session_destroy();
    header('Location: admin.php');
    exit;
}

// Require Login for other actions
if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Biaja Tours Admin</title>
    <link rel="stylesheet" href="styles.css">
    <style>
        body { background-color: #f3f4f6; display: flex; align-items: center; justify-content: center; height: 100vh; margin: 0; }
        .login-box { background: white; padding: 2rem; border-radius: 8px; box-shadow: 0 4px 6px rgba(0,0,0,0.1); width: 100%; max-width: 400px; }
        .form-group { margin-bottom: 1rem; }
        .form-group label { display: block; margin-bottom: 0.5rem; }
        .form-group input { width: 100%; padding: 0.75rem; border: 1px solid #ddd; border-radius: 4px; box-sizing: border-box;}
        .btn { width: 100%; padding: 0.75rem; background: #007bff; color: white; border: none; border-radius: 4px; cursor: pointer; }
        .btn:hover { background: #0056b3; }
        .error { color: red; margin-bottom: 1rem; text-align: center; }
    </style>
</head>
<body>
    <div class="login-box">
        <h2 style="text-align: center; margin-top: 0;">Admin Login</h2>
        <?php if (isset($error)) echo "<p class='error'>$error</p>"; ?>
        <form method="POST">
            <div class="form-group">
                <label>Contraseña</label>
                <input type="password" name="password" required autofocus>
            </div>
            <button type="submit" name="login" class="btn">Entrar</button>
        </form>
    </div>
</body>
</html>
<?php
    exit;
}

// Handle New Post
if (isset($_POST['create_post'])) {
    $title = $_POST['title'];
    $content = $_POST['content'];
    $imagePath = '';

    // Handle Image Upload
    if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
        $fileTmpPath = $_FILES['image']['tmp_name'];
        $fileName = time() . '_' . $_FILES['image']['name'];
        $destPath = $uploadDir . $fileName;

        if(move_uploaded_file($fileTmpPath, $destPath)) {
            $imagePath = $destPath;
        }
    }

    $newPost = [
        'id' => uniqid(),
        'title' => $title,
        'content' => $content, // HTML allowed
        'image' => $imagePath,
        'date' => date('Y-m-d H:i:s')
    ];

    $posts = json_decode(file_get_contents($dataFile), true);
    array_unshift($posts, $newPost); // Add to beginning (newest first)
    file_put_contents($dataFile, json_encode($posts, JSON_PRETTY_PRINT));
    
    header('Location: admin.php');
    exit;
}

// Handle Delete Post
if (isset($_GET['delete'])) {
    $id = $_GET['delete'];
    $posts = json_decode(file_get_contents($dataFile), true);
    
    // Filter out the post to delete
    $posts = array_filter($posts, function($post) use ($id) {
        if ($post['id'] === $id) {
            // Optional: Delete image file
            if (!empty($post['image']) && file_exists($post['image'])) {
                unlink($post['image']); 
            }
            return false; 
        }
        return true;
    });

    file_put_contents($dataFile, json_encode(array_values($posts), JSON_PRETTY_PRINT));
    header('Location: admin.php');
    exit;
}

// Get Posts
$posts = json_decode(file_get_contents($dataFile), true);
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard - Biaja Tours</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        /* Basic Admin Styles */
        body { font-family: sans-serif; background: #f3f4f6; margin: 0; padding: 0; }
        .header { background: #fff; padding: 1rem 2rem; display: flex; justify-content: space-between; align-items: center; box-shadow: 0 2px 4px rgba(0,0,0,0.05); }
        .container { max-width: 1200px; margin: 2rem auto; padding: 0 1rem; display: grid; grid-template-columns: 1fr 2fr; gap: 2rem; }
        .card { background: #fff; padding: 2rem; border-radius: 8px; box-shadow: 0 2px 4px rgba(0,0,0,0.05); }
        h2, h3 { margin-top: 0; }
        
        /* Form */
        .form-group { margin-bottom: 1rem; }
        .form-group label { display: block; margin-bottom: 0.5rem; font-weight: bold; }
        .form-control { width: 100%; padding: 0.75rem; border: 1px solid #ddd; border-radius: 4px; box-sizing: border-box; }
        textarea.form-control { min-height: 150px; font-family: inherit; }
        .btn { padding: 0.75rem 1.5rem; border: none; border-radius: 4px; cursor: pointer; font-weight: bold; }
        .btn-primary { background: #007bff; color: white; }
        .btn-danger { background: #dc3545; color: white; text-decoration: none; padding: 0.5rem 1rem; font-size: 0.9rem; }
        .btn-secondary { background: #6c757d; color: white; text-decoration: none; }
        
        /* List */
        .post-item { display: flex; gap: 1rem; border-bottom: 1px solid #eee; padding: 1rem 0; align-items: start; }
        .post-item:last-child { border-bottom: none; }
        .post-img { width: 100px; height: 75px; object-fit: cover; border-radius: 4px; background: #eee; }
        .post-content { flex: 1; }
        .post-title { margin: 0 0 0.5rem 0; font-size: 1.1rem; }
        .post-meta { font-size: 0.85rem; color: #666; }

        @media (max-width: 768px) {
            .container { grid-template-columns: 1fr; }
        }
    </style>
    <!-- Simple WYSIWYG Editor (optional, using standard textarea for now but enabling HTML) -->
</head>
<body>

    <div class="header">
        <h2>Biaja Tours Admin</h2>
        <div>
            <a href="index.html" target="_blank" class="btn btn-secondary" style="margin-right: 10px;"><i class="fa-solid fa-eye"></i> Ver Sitio</a>
            <a href="?logout" class="btn btn-secondary"><i class="fa-solid fa-right-from-bracket"></i> Cerrar Sesión</a>
        </div>
    </div>

    <div class="container">
        <!-- Sidebar / Create Post -->
        <div class="card">
            <h3><i class="fa-solid fa-plus-circle"></i> Nuevo Post</h3>
            <form method="POST" enctype="multipart/form-data">
                <div class="form-group">
                    <label>Título</label>
                    <input type="text" name="title" class="form-control" required placeholder="Ej: Oferta Especial Punta Cana">
                </div>
                
                <div class="form-group">
                    <label>Imagen</label>
                    <input type="file" name="image" class="form-control" accept="image/*" required>
                    <small style="color: #666;">Se recomienda formato horizontal (16:9)</small>
                </div>

                <div class="form-group">
                    <label>Descripción (HTML permitido)</label>
                    <textarea name="content" class="form-control" placeholder="<p>Detalles de la oferta...</p>"></textarea>
                </div>

                <button type="submit" name="create_post" class="btn btn-primary" style="width: 100%;">Publicar Oferta</button>
            </form>
        </div>

        <!-- Main Content / List Posts -->
        <div class="card">
            <h3><i class="fa-solid fa-list"></i> Ofertas Publicadas (<?php echo count($posts); ?>)</h3>
            
            <?php if (empty($posts)): ?>
                <p style="color: #666; text-align: center; padding: 2rem;">No hay ofertas publicadas.</p>
            <?php else: ?>
                <div class="post-list">
                    <?php foreach ($posts as $post): ?>
                        <div class="post-item">
                            <?php if (!empty($post['image'])): ?>
                                <img src="<?php echo htmlspecialchars($post['image']); ?>" class="post-img" alt="Post Image">
                            <?php else: ?>
                                <div class="post-img" style="display: flex; align-items: center; justify-content: center; color: #ccc;"><i class="fa-solid fa-image"></i></div>
                            <?php endif; ?>
                            
                            <div class="post-content">
                                <h4 class="post-title"><?php echo htmlspecialchars($post['title']); ?></h4>
                                <div class="post-meta">
                                    <i class="fa-regular fa-calendar"></i> <?php echo date('d/m/Y', strtotime($post['date'])); ?>
                                </div>
                                <div style="margin-top: 0.5rem; font-size: 0.9rem; color: #444; max-height: 60px; overflow: hidden; text-overflow: ellipsis;">
                                    <?php echo strip_tags($post['content']); ?>
                                </div>
                            </div>
                            
                            <div>
                                <a href="?delete=<?php echo $post['id']; ?>" class="btn btn-danger" onclick="return confirm('¿Estás seguro de borrar esta oferta?');">
                                    <i class="fa-solid fa-trash"></i>
                                </a>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </div>
    </div>

</body>
</html>
