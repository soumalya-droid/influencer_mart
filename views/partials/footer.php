
  </main>
  <footer class="footer">
    <p>&copy; <?= date('Y') ?> <?= htmlspecialchars($cfg['app_name']) ?>.</p>
  </footer>
  <script>
    // basic helper to fetch as JSON
    async function postJSON(url, data) {
      const res = await fetch(url, {method:'POST', headers:{'Content-Type':'application/x-www-form-urlencoded'}, body:new URLSearchParams(data)});
      return res.json?.() ?? {};
    }
  </script>
</body>
</html>
