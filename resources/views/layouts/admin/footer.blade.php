</div>
</div>
</div>
<script src="/template/src/assets/libs/jquery/dist/jquery.min.js"></script>
<script src="/template/src/assets/libs/bootstrap/dist/js/bootstrap.bundle.min.js"></script>
<script src="/template/src/assets/js/sidebarmenu.js"></script>
<script src="/template/src/assets/js/app.min.js"></script>
<script src="/template/src/assets/libs/simplebar/dist/simplebar.js"></script>
<script>
    setTimeout(function() {
        let alertElements = document.querySelectorAll('.alert');
        alertElements.forEach(function(alert) {
            // Tambahkan efek fade-out
            alert.style.transition = "opacity 0.5s ease";
            alert.style.opacity = "0";

            // Hapus elemen setelah fade-out
            setTimeout(() => alert.remove(), 500);
        });
    }, 3000); // 3000ms = 3 detik
</script>
</body>

</html>
