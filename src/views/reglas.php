<?php
ob_start();
$pageTitle = "Reglamento Oficial – Golstalgia";
$fullWidth = true;
?>

<div class="rules-container" id="reglamento">
    <div class="card rules-card">
        <div class="rules-header">
            📜 REGLAMENTO I CAMPEONATO TRIVIAL GOLSTALGIA
        </div>
        <div class="rules-body">
            
            <div class="grid-2">
                <section class="rules-section">
                    <h2 class="rules-title">👥 PARTICIPANTES Y FORMATO</h2>
                    <p>La competición cuenta con <strong>12 participantes</strong> que jugarán una liga de todos contra todos, contabilizando un total de <strong>11 jornadas</strong>.</p>
                    <p>⏱️ Los partidos serán de <strong>2 partes de 10 minutos</strong> cada una.</p>
                    <p>🛡️ Cada equipo tendrá: portero, defensa, centro del campo y ataque.</p>
                    <p>🎲 Se sorteará quién inicia el juego y al inicio de la segunda mitad empezará atacando el otro equipo.</p>
                </section>

                <section class="rules-section">
                    <h2 class="rules-title">⚽ MECÁNICA DE JUEGO</h2>
                    <p>Cuando se inicie el partido, el equipo atacante iniciará con el balón en el <strong>centro del campo</strong>. Se le formulará una pregunta y si la acierta, podrá pasar el balón al <strong>ataque</strong>.</p>
                    <p>🔄 <strong>Rebotes:</strong> Si la falla, habrá rebote y si el contrario acierta, consigue la posesión del balón en el sitio donde la haya robado.</p>
                </section>
            </div>

            <!-- EJEMPLO FULL WIDTH CON ESCENARIOS -->
            <div class="rules-highlight-box">
                <h4 class="rules-highlight-title">EJEMPLO DE FALLO EN DEFENSA:</h4>
                <div class="rules-scenario-grid">
                    <div class="rules-scenario-card">
                        <div class="rules-scenario-title">ESCENARIO A</div>
                        <p>Si acierta, supera la línea de presión y <strong>pasa al centro del campo</strong>.</p>
                    </div>
                    <div class="rules-scenario-card">
                        <div class="rules-scenario-title">ESCENARIO B</div>
                        <p>Si falla y el rival no aprovecha el rebote, mantiene posesión pero baja un nivel (pasa al <strong>portero</strong>).</p>
                    </div>
                    <div class="rules-scenario-card">
                        <div class="rules-scenario-title">ESCENARIO C</div>
                        <p>Si falla y el rival acierta el rebote, el <strong>rival toma la posesión</strong> y puede atacar.</p>
                    </div>
                </div>
            </div>

            <div class="grid-2">
                <section class="rules-section">
                    <h2 class="rules-title">🥅 GOLES Y SANCIONES</h2>
                    <p>🎯 <strong>Goles:</strong> Se consiguen goles adivinando <strong>dos preguntas seguidas estando en la zona de ataque o marcando un penalti</strong>.</p>
                    <p>🟨 <strong>Tarjetas:</strong> Si un equipo comete tres errores seguidos sin perder la posesión, se llevará una tarjeta amarilla (se acumulan para el segundo tiempo).</p>
                    <p>🥅 <strong>Penaltis:</strong> A la segunda amarilla habrá penalti. Consiste en una sola pregunta: si se acierta es GOL, si no, la pelota pasa al portero defensor sin rebote.</p>
                    <p>⚡ <strong>Rapidez:</strong> Se debe responder rápido. Pérdidas de tiempo pueden suponer tarjeta amarilla. La no respuesta en tiempo justo dará advertencia y opción a rebote.</p>
                </section>

                <section class="rules-section">
                    <h2 class="rules-title">📊 CLASIFICACIÓN Y PLAY-OFF</h2>
                    <p>📈 <strong>Puntos:</strong> 3 por victoria y 1 por empate.</p>
                    <p>⚖️ <strong>Desempate:</strong> En caso de igualdad de puntos, los criterios son: <br> 1. Puntos directos. <br> 2. Goles a favor. <br> 3. Goal Average General.</p>
                    <p>🏆 <strong>Copa:</strong> Los 8 primeros se clasificarán a la Copa (los 4 primeros serán cabezas de serie).</p>
                    <p>🥄 <strong>Cucharas de Madera:</strong> Los clasificados del 9 al 12 jugarán un play-in. El perdedor final se llevará la "Cuchara de Madera" (pagar una ronda a los demás).</p>
                </section>
            </div>

            <section class="rules-section">
                <h2 class="rules-title">🎁 PREMIOS</h2>
                <ul style="list-style: none; padding-left: 0;">
                    <li style="margin-bottom: 1rem;">🥇 <strong>Ganador:</strong> Un lote con 1 fuet y 4 longanizas (cortesía de Toni) + Libro "El milagro del Castel di Sangro".</li>
                    <li>🥈 <strong>Segundo:</strong> Libro de Sport de Sant Jordi del año 2000 (cortesía del perico del grupo).</li>
                </ul>
            </section>

            <div class="rules-summary-box">
                🚀 RESUMEN PARA GOLSTÁLGICOS:
                <div class="rules-summary-detail">
                    11 Jornadas • Partidos 20 min • <strong>Gol: 2 aciertos seguidos en ataque o marcando un penalti</strong> • 3 Errores seguidos = Amarilla • Top 8 a Copa • ¡Prohibido fallar!
                </div>
            </div>

            <div class="rules-download-area">
                <a href="/normativa/I CAMPEONATO TRIVIAL GOLSTALGIA.pdf" target="_blank" class="btn btn-primary" style="padding: 1.5rem 3rem; font-size: 1.2rem;">DESCARGAR PDF ORIGINAL</a>
            </div>
        </div>
    </div>
</div>

<?php 
$content = ob_get_clean();
require_once __DIR__ . '/partials/layout.php';
?>
