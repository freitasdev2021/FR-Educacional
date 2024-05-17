var urlAtual = window.location.href;
const urlParams = new URLSearchParams(window.location.search);
//FILTRO DE MOVIMENTAÇÕES DE COLABORADORES
var partesDaUrlMovimentacoes = urlAtual.split('/');
var IDColaborador = partesDaUrlMovimentacoes[partesDaUrlMovimentacoes.length - 1];
//FILTRO DE COLABORADORES
if(urlParams.get('de')){
    var de = urlParams.get('de')
    var ate = urlParams.get('ate')
    urlApontamentos = $("#example2").attr("data-route")+"?de="+de+"&ate="+ate
}else{
    urlApontamentos = $("#example2").attr("data-route")
}

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