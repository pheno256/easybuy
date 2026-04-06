            </main>
        </div>
    </div>
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        function showNotification(message, type) {
            Swal.fire({
                title: type === 'success' ? 'Success!' : 'Error!',
                text: message,
                icon: type,
                timer: 3000,
                showConfirmButton: false
            });
        }
    </script>
</body>
</html>