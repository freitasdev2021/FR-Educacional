var urlAtual = window.location.href;
const urlParams = new URLSearchParams(window.location.search);
//FILTRO DE MOVIMENTAÇÕES DE COLABORADORES
var partesDaUrlMovimentacoes = urlAtual.split('/');
var IDColaborador = partesDaUrlMovimentacoes[partesDaUrlMovimentacoes.length - 1];
//FILTRO DE COLABORADORES
if(urlParams.get('Status')){
    var Status = urlParams.get('Status')
    urlApontamentos = $("#escolas").attr("data-rota")+"?Status="+Status
}else{
    urlApontamentos = $("#escolas").attr("data-route")
}

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