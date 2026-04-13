<?php

$moduleApiRoutes = [
    base_path('app/Modules/Auth/Routes/api.php'),
    base_path('app/Modules/Users/Routes/api.php'),
    base_path('app/Modules/Provas/Routes/api.php'),
    base_path('app/Modules/Treino/Routes/api.php'),
    base_path('app/Modules/Resultados/Routes/api.php'),
];

foreach ($moduleApiRoutes as $file) {
    require $file;
}
