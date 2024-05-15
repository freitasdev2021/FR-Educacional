<!doctype html>
<html>
   <head>
      <meta charset='utf-8'>
      <meta name='viewport' content='width=device-width, initial-scale=1'>
      <title>FR Educacional</title>
      <link href='https://cdn.jsdelivr.net/npm/bootstrap@5.0.0-beta1/dist/css/bootstrap.min.css' rel='stylesheet'>
      <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/admin-lte@3.1/dist/css/adminlte.min.css">
      <link rel="stylesheet" href="{{asset('css/snipets.css')}}">
      <link href='https://cdn.jsdelivr.net/npm/boxicons@latest/css/boxicons.min.css' rel='stylesheet'>
      <link href="https://cdn.datatables.net/v/dt/dt-2.0.7/datatables.min.css" rel="stylesheet">
      <link href="{{asset('js/chartjs/chart.css')}}" rel="stylesheet">
      <script type='text/javascript' src='https://cdnjs.cloudflare.com/ajax/libs/jquery/3.2.1/jquery.min.js'></script>
      <script src="{{asset('js/inputmask.js')}}"></script>
   </head>
   @yield('content')
         <script src="https://code.jquery.com/jquery-3.7.1.min.js" integrity="sha256-/JqT3SQfawRcv/BIHPThkBvs0OEvtFFmqPF/lYI/Cxo=" crossorigin="anonymous"></script>
         <script src="https://cdn.datatables.net/v/dt/dt-2.0.7/datatables.min.js"></script>
         <script src="{{asset('js/datatablesGeral.js')}}"></script>
         <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
        <script type='text/javascript'>document.addEventListener("DOMContentLoaded", function(event) {
            const showNavbar = (toggleId, navId, bodyId, headerId) =>{
            const toggle = document.getElementById(toggleId),
            nav = document.getElementById(navId),
            bodypd = document.getElementById(bodyId),
            headerpd = document.getElementById(headerId)
            
            // Validate that all variables exist
               if(toggle && nav && bodypd && headerpd){
                     toggle.addEventListener('click', ()=>{
                     // show navbar
                     nav.classList.toggle('show')
                     // change icon
                     toggle.classList.toggle('bx-x')
                     // add padding to body
                     bodypd.classList.toggle('body-pd')
                     // add padding to header
                     headerpd.classList.toggle('body-pd')
                  })
               }
            }
            
            showNavbar('header-toggle','nav-bar','body-pd','header')
            
            /*===== LINK ACTIVE =====*/
            const linkColor = document.querySelectorAll('.nav_link')
            
            function colorLink(){
            if(linkColor){
            linkColor.forEach(l=> l.classList.remove('active'))
            this.classList.add('active')
            }
            }
            linkColor.forEach(l=> l.addEventListener('click', colorLink))
            
             // Your code to run since DOM is loaded and ready
            });
         </script>
</html>