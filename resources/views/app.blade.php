<!doctype html>
<html lang="pt-BR">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>ENEM | ENEM.pratica</title>
  <link rel="icon" type="image/svg+xml" href="/favicon-check.svg" />
  <script>
    (function () {
      try {
        var t = localStorage.getItem('enem_theme');
        var root = document.documentElement;
        if (t === 'dark') root.classList.add('dark');
        else root.classList.remove('dark');
      } catch (e) {}
    })();
  </script>
  @viteReactRefresh
  @vite('resources/js/main.jsx')
</head>
<body>
  <div id="root"></div>
</body>
</html>
