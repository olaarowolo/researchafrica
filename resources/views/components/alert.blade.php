@if ($message = session('success'))
<script>
    Swal.fire({
      position: 'top-end',
      icon: 'success',
      title: '{{ $message }}',
      showConfirmButton: false,
      timer: 5000,
      toast: true,
    })
</script>
@endif


@if ($error = session('error'))
    <script>
        Swal.fire({
        position: 'top-end',
        icon: 'error',
        title: '{{ $error }}',
        showConfirmButton: false,
        timer: 5000,
        toast: true,
        })
    </script>
@endif

@if ($message = session('update'))
    <script>
        Swal.fire({
        position: 'top-end',
        icon: 'info',
        title: '{{ $message }}',
        showConfirmButton: false,
        timer: 5000,
        toast: true,
        })
    </script>
@endif

@if ($warn = session('warning'))
    <script>
        Swal.fire({
        position: 'top-end',
        icon: 'warning',
        title: '{{ $warn }}',
        showConfirmButton: false,
        timer: 5000,
        toast: true,
        })
    </script>
@endif
