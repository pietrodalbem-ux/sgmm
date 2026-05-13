        </main>
    </div>
</div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script src="assets/js/sgm-ui.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function () {
    if (window.SGM && SGM.initSidebarToggle) SGM.initSidebarToggle();
});
</script>
<?php
$SGM_EXTRA_SCRIPTS = $SGM_EXTRA_SCRIPTS ?? [];
if (!empty($SGM_EXTRA_SCRIPTS) && is_array($SGM_EXTRA_SCRIPTS)) {
    foreach ($SGM_EXTRA_SCRIPTS as $src) {
        $u = htmlspecialchars($src, ENT_QUOTES, 'UTF-8');
        echo '<script src="' . $u . '"></script>' . "\n";
    }
}
?>
</body>
</html>
