// console.log(urlApontamentos)
//

$("#secretarias").dataTable({
    "responsive": true,
    "autoWidth": false,
    "bDestroy": true,
    "serverside": true,
    "ajax" : $("#secretarias").attr("data-rota")
});

$("#escolas").dataTable({
    "responsive": true,
    "autoWidth": false,
    "bDestroy": true,
    "serverside": true,
    "ajax" : $("#escolas").attr("data-rota")
});

$("#secretariasAdministradores").dataTable({
    "responsive": true,
    "autoWidth": false,
    "bDestroy": true,
    "serverside": true,
    "ajax" : $("#secretariasAdministradores").attr("data-rota")
});