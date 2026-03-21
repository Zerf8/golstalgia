<?php

class HomeController
{
    public function index(): void
    {
        $ligaModel    = new LigaModel();
        $jornadaModel = new JornadaModel();
        $partidaModel = new PartidaModel();

        $liga = $ligaModel->activa();
        
        $ultimaJornada    = null;
        $partidasUltima   = [];
        $siguienteJornada = null;
        $partidasProxima  = [];
        $clasificacion    = [];

        if ($liga) {
            $ligaId = $liga['id'];
            
            // 1. Clasificación
            $clasificacion = $partidaModel->getClasificacion($ligaId);
            
            // 2. Última jornada disputada
            $ultimaJornada = $jornadaModel->findUltimaDisputada($ligaId);
            if ($ultimaJornada) {
                $partidasUltima = $partidaModel->allByJornada($ultimaJornada['id']);
            }
            
            // 3. Siguiente jornada
            $siguienteJornada = $jornadaModel->findSiguiente($ligaId);
            if ($siguienteJornada) {
                $partidasProxima = $partidaModel->allByJornada($siguienteJornada['id']);
            }
        }

        require_once __DIR__ . '/../views/trivial/home.php';
    }

    public function reglas(): void
    {
        require_once __DIR__ . '/../views/trivial/reglas.php';
    }

    public function calendario(): void
    {
        $ligaModel    = new LigaModel();
        $jornadaModel = new JornadaModel();
        $partidaModel = new PartidaModel();

        $liga = $ligaModel->activa();
        $jornadasCompletas = [];

        if ($liga) {
            $ligaId = $liga['id'];
            $jornadas = $jornadaModel->allByLiga($ligaId);
            foreach ($jornadas as $j) {
                $jornadasCompletas[] = [
                    'jornada'  => $j,
                    'partidas' => $partidaModel->allByJornada($j['id'])
                ];
            }
        }

        require_once __DIR__ . '/../views/trivial/calendario.php';
    }

    public function mainHome(): void
    {
        require_once __DIR__ . '/../views/main_home.php';
    }
}
