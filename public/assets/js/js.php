<!-- flowbite -->
<script src="https://cdn.jsdelivr.net/npm/flowbite@2.5.2/dist/flowbite.min.js"></script>
<!-- sweetalert -->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<?php 
    if(isset($_SESSION['alert'])){
        // checking if session is started
        if (!session_id()) {
            session_start();
        }

        $alert = $_SESSION['alert'];
        unset($_SESSION['alert']);
        echo "
        <script>
            Swal.fire({
                icon: '{$alert['type']}',
                title: '{$alert['message']}',
                timer: 3000,
                timerProgressBar: true,
                showConfirmButton: false
            });
        </script>
        ";
        session_write_close();
    }
    
    
?>