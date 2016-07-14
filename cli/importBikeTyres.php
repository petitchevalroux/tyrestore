<?php

/**
 * Identifie les dimensions des pneus des motos
 */
require realpath(__DIR__ . DIRECTORY_SEPARATOR . '..')
        . DIRECTORY_SEPARATOR
        . 'vendor'
        . DIRECTORY_SEPARATOR
        . 'autoload.php';

use TyreStore\Di;

$stderr = fopen('php://stderr', 'w');
$stdout = fopen('php://stdout', 'w');

$di = Di::getInstance();
$config = $di->config->get('pluxml');

foreach ($config->sites as $site) {
    $directory = $site['directory'];
    foreach (glob($directory . '/data/articles/*.xml') as $file) {
        try {
            $doc = simplexml_load_file($file);
            if (empty($doc)) {
                throw new Exception('Impossible de parser le contenu du fichier');
            }
            preg_match_all('~([0-9]+)\s*/\s*([0-9]+)\s*([A-Z]+)\s*([0-9]+)*~', (string) $doc->content, $matches);
            $dimensions = [];
            foreach ($matches[0] as $k => $v) {
                $width = (int) $matches[1][$k];
                $height = (int) $matches[2][$k];
                $construction = $matches[3][$k];
                $diameter = (int) $matches[4][$k];
                $dimensions[] = [
                    'width' => $width,
                    'height' => $height,
                    'construction' => $construction,
                    'diameter' => $diameter
                ];
            }
            if (empty($dimensions)) {
                throw new Exception('Aucune dimension trouvée');
            }
            $title = trim($doc->title);
            if (empty($title)) {
                throw new Exception('Titre vide');
            }
            $filenameData = explode('.', basename($file));
            if (count($filenameData) != 6) {
                throw new Exception('Format de non de fichier invalide');
            }
            $data = [
                'title' => $title,
                'dimensions' => $dimensions,
                'url' => sprintf(
                        '%s://%s/%s.%s.html', $site['scheme'], $site['url'], $filenameData[4], $filenameData[0]
                )
            ];

            fwrite($stdout, json_encode($data) . "\n");
        } catch (Exception $ex) {
            fwrite($stderr, $ex->getMessage() . ' file:' . $file . "\n");
        }
    }
}
fclose($stderr);
fclose($stdout);
