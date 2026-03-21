<?php
$pageTitle = "Golstalgia - Podcast de Fútbol Retro";
ob_start();
?>

<div class="main-home-container">
    <section class="hero-simple" style="text-align: center; padding: 4rem 1rem; background: var(--azul-oscuro); border-bottom: 2px solid var(--amarillo);">
        <h1 style="font-family: var(--font-head); font-size: 3.5rem; color: var(--amarillo); margin-bottom: 1rem;">GOLSTALGIA</h1>
        <p style="font-size: 1.25rem; opacity: 0.9; max-width: 800px; margin: 0 auto;">El podcast donde el fútbol de ayer se vive como nunca. Historia, curiosidades y toda la pasión del fútbol retro.</p>
    </section>

    <section class="podcast-section" style="padding: 4rem 1rem; max-width: 1000px; margin: 0 auto;">
        <h2 style="font-family: var(--font-head); text-align: center; margin-bottom: 2rem; font-size: 2rem;">🎙️ ESCUCHA NUESTRO PODCAST</h2>
        
        <div class="ivoox-widget-wrapper" style="background: #1a1a1a; padding: 1rem; border-radius: 8px; box-shadow: 0 10px 30px rgba(0,0,0,0.5); overflow: hidden;">
            <iframe src="https://www.ivoox.com/player_es_podcast_287524_zp_1.html?c1=d93802" 
                width="100%" height="400" 
                frameborder="0" scrolling="no" 
                style="border: none; display: block;"
                allow="autoplay; clipboard-write; encrypted-media; picture-in-picture"
                allowfullscreen>
            </iframe>
        </div>

        <div style="margin-top: 3rem; text-align: center;">
            <a href="/trivial" class="btn btn-primary" style="padding: 1rem 3rem; font-size: 1.5rem; font-family: var(--font-head);">
                🏆 ENTRAR A LA LIGA TRIVIAL
            </a>
        </div>
    </section>
</div>


<?php
$content = ob_get_clean();
require_once __DIR__ . '/partials/layout.php';
?>
