<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ofertas de Viaje - Biaja Tours | Promociones Exclusivas</title>
    <link rel="stylesheet" href="styles.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <meta name="description"
        content="Descubre las mejores ofertas para viajar con Biaja Tours. Descuentos en hoteles, vuelos y paquetes turísticos por tiempo limitado.">

    <script type="application/ld+json">
    {
      "@context": "https://schema.org",
      "@type": "ItemPage",
      "mainEntity": {
        "@type": "OfferCatalog",
        "name": "Ofertas de Biaja Tours",
        "description": "Catálogo de ofertas y promociones de viaje vigentes."
      }
    }
    </script>
</head>

<body>

    <!-- Header Reuse (Ideally fetch this or use a component, but copying for static demo simplicity) -->
    <header class="header">
        <div class="container">
            <a href="index.html" class="logo">
                <img src="img/logo-biaja-horizontal.png" alt="Biaja Tours" style="height: 90px;">
            </a>

            <nav class="nav-menu">
                <a href="index.html" class="nav-link" data-i18n="nav.home">Inicio</a>
                <a href="nosotros.html" class="nav-link">Nosotros</a>
                <a href="index.html#destinos" class="nav-link" data-i18n="nav.destinations">Destinos</a>
                <a href="paquetes.html" class="nav-link" style="color: var(--primary); font-weight:700;">Paquetes ✈️</a>
                <a href="ofertas.php" class="nav-link active" data-i18n="nav.offers">Ofertas</a>
                <a href="index.html#servicios" class="nav-link" data-i18n="nav.services">Servicios</a>
                <a href="index.html#eventos" class="nav-link" data-i18n="nav.corporate">Empresas</a>
                <a href="index.html#contacto" class="btn btn-primary" data-i18n="nav.contact">Contáctanos</a>

                <!-- Language Toggle -->
                <div class="lang-switch">
                    <span class="lang-opt" data-lang="es">ES</span>
                    <span class="lang-divider">|</span>
                    <span class="lang-opt" data-lang="en">EN</span>
                </div>
            </nav>

            <!-- Mobile Toggle -->
            <button class="mobile-toggle">
                <i class="fa-solid fa-bars"></i>
            </button>
        </div>
    </header>

    <main>
        <!-- Offers Banner -->
        <section class="hero small-hero">
            <div class="hero-bg">
                <img src="https://images.unsplash.com/photo-1540541338287-41700207dee6?q=80&w=2070&auto=format&fit=crop"
                    alt="Ofertas de Viaje">
                <div style="position: absolute; top:0; left:0; width:100%; height:100%; background: rgba(0,0,0,0.4);">
                </div>
            </div>
            <div class="container">
                <div class="hero-content" style="padding-top: 0; text-align: center; margin: 0 auto;">
                    <h1 class="title-display" style="margin-bottom: 0;" data-i18n="offers.title">Ofertas Exclusivas</h1>
                    <p class="hero-subtitle" style="margin: 1rem auto 0;" data-i18n="offers.subtitle">Tus vacaciones
                        soñadas al mejor precio</p>
                </div>
            </div>
        </section>

        <section class="section" style="padding-top: 4rem; min-height: 60vh;">
            <div class="container">
                <style>
                    .offers-grid {
                        display: grid;
                        grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
                        gap: 2rem;
                    }

                    .offer-card {
                        background: #fff;
                        border-radius: 8px;
                        box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
                        overflow: hidden;
                        transition: transform 0.3s ease;
                        display: flex;
                        flex-direction: column;
                    }

                    .offer-card:hover {
                        transform: translateY(-5px);
                    }

                    .offer-img {
                        width: 100%;
                        height: 200px;
                        object-fit: cover;
                    }

                    .offer-body {
                        padding: 1.5rem;
                        flex: 1;
                        display: flex;
                        flex-direction: column;
                    }

                    .offer-title {
                        font-size: 1.25rem;
                        margin: 0 0 0.5rem 0;
                        color: #333;
                    }

                    .offer-date {
                        font-size: 0.85rem;
                        color: #777;
                        margin-bottom: 1rem;
                    }

                    .offer-text {
                        color: #555;
                        font-size: 0.95rem;
                        line-height: 1.5;
                        flex: 1;
                    }

                    .offer-btn {
                        display: inline-block;
                        margin-top: 1rem;
                        padding: 0.5rem 1rem;
                        background-color: var(--primary);
                        color: white;
                        text-decoration: none;
                        border-radius: 4px;
                        text-align: center;
                        align-self: flex-start;
                    }
                </style>

                <div class="offers-grid">
                    <?php
                    $dataFile = 'data/posts.json';
                    if (file_exists($dataFile)) {
                        $posts = json_decode(file_get_contents($dataFile), true);

                        // Limit to 9 posts
                        $posts = array_slice($posts, 0, 9);

                        if (!empty($posts)) {
                            foreach ($posts as $post) {
                                $image = !empty($post['image']) ? $post['image'] : 'img/default-offer.jpg'; // You might want a default image
                                $title = htmlspecialchars($post['title']);
                                $date = date('d/m/Y', strtotime($post['date']));
                                $content = $post['content']; // HTML is allowed, so be careful or strip tags for preview
                    
                                echo "
                                <article class='offer-card'>
                                    " . (!empty($post['image']) ? "<img src='{$image}' class='offer-img' alt='{$title}'>" : "") . "
                                    <div class='offer-body'>
                                        <h3 class='offer-title'>{$title}</h3>
                                        <div class='offer-date'><i class='fa-regular fa-calendar'></i> {$date}</div>
                                        <div class='offer-text'>
                                            {$content}
                                        </div>
                                        <a href='https://wa.me/18094225371?text=Hola, me interesa la oferta: {$title}' target='_blank' class='offer-btn'>
                                            <i class='fa-brands fa-whatsapp'></i> <span data-i18n='offers.btn.info'>Más Información</span>
                                        </a>
                                    </div>
                                </article>
                                ";
                            }
                        } else {
                            echo "<div style='grid-column: 1/-1; text-align: center; padding: 3rem;'>
                                    <h3 data-i18n='offers.empty'>No hay ofertas activas en este momento.</h3>
                                    <p data-i18n='offers.empty.desc'>Vuelve pronto para ver nuestras promociones.</p>
                                  </div>";
                        }
                    } else {
                        echo "<p data-i18n='offers.error'>Error: No se pudo cargar la base de datos de ofertas.</p>";
                    }
                    ?>
                </div>

            </div>
        </section>
    </main>

    <!-- Footer -->
    <footer class="footer">
        <div class="container">
            <div style="text-align: center; color: rgba(255,255,255,0.6); margin-bottom: 1rem;">
                &copy; 2026 Biaja Tours & Travels. | <a href="admin.html"
                    style="color: inherit; text-decoration: none;">Admin</a>
            </div>
        </div>
    </footer>

    <script src="js/translations.js"></script>
    <script src="script.js"></script>
    <script>
        // Legacy DB logic removed for Instagram integration
    </script>
    <a href="https://wa.me/18094225371" class="whatsapp-float" target="_blank">
        <i class="fa-brands fa-whatsapp"></i>
    </a>
</body>

</html>