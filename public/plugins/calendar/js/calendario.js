jQuery(function(){
    //CONFIGURAÇÃO DAS DATAS
    $(".calendar_content div").each(function(){
        if(!$(this).hasClass(".past-date")){
            if(parseInt($(this).text()) <=9){
                dia = 0+$(this).text()
            }else{
                dia = $(this).text()
            }

            if(parseInt($(this).parents(".calendar").attr("data-mes")) <=9){
                mes = 0+$(this).parents(".calendar").attr("data-mes")
            }else{
                mes = $(this).parents(".calendar").attr("data-mes")
            }

            $(this).attr("data",dia+"/"+mes+"/"+$(this).parents(".calendar").attr("data-ano"))
        }
    })
    //RASTREAMENTO DAS DATAS
    //
})