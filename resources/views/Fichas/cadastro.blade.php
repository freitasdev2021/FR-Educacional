<x-educacional-layout>
    {{-- <form action="{{route('FichasSave')}}" method="POST"> --}}
        <div class="col-sm-12">
            <div class="card">
                <div class="card-header bg-fr text-white">
                    <strong>Elaborar Formulário</strong>
                </div>
                @if(isset($id))
                <input type="hidden" name="id" value="{{$id}}">
                @endif
                <div class="card-body periudos">
                    <div class="row">
                        <div class="col-sm-12">
                            <label>Titulo</label>
                            <input type="text" name="Titulo" class="form-control" value="{{isset($Registro) ? $Registro->Titulo : ''}}">
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-sm-12">
                            <label>Escola</label>
                            <select name="IDEscola" class="form-control">
                                <option value="">Selecione</option>
                                @foreach($Escolas as $e)
                                <option value="{{$e['IDEscola']}}" {{isset($Registro) && $Registro->IDEscola == $e['IDEscola'] ? 'selected' : ''}}>{{$e['Nome']}}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <br>
                    <!--conteudo do card-->
                    {{-- <button class="btn btn-light col-sm-12 btnComponente">Adicionar Componente</button> --}}
                    <select name="AdicionarComponente" class="form-control">
                        <option value="">Adicionar Pergunta</option>
                        <option value="Dissertativa">Dissertativa</option>
                        <option value="Objetiva">Objetiva</option>
                    </select>
                    <br>
                    <!--OBJETIVAS-->
                    <div class="objetivaModel" style="display:none;">
                        <table class="table table-bordered border-primary text-center bimestri primeiroBimestre bimestral" data-periodo='primeiroBimestre'>
                            <thead>
                                <tr>
                                    <th colspan="2"><strong>Enunciado</strong></th>
                                </tr>
                                <tr>
                                    <th colspan="2"><strong contenteditable="true" style="padding:5px;" class="componente"></strong></th>
                                </tr>
                                <tr>
                                    <th><strong>Opção</strong></th>
                                    <th>Excluir</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr style="display:none;" class="conteudoModel">
                                    <td contenteditable="true" class="conteudo"></td>
                                    <td><button class='btn btn-danger btn-xs btnRemoveConteudo'>X</button></td>
                                </tr>
                                <tr id="adcButton">
                                    <td colspan="2" style="padding:0px;"><button class="btn btn-light col-sm-12 btnConteudo" type="button">Adicionar Opção</button></td>
                                </tr>
                                <tr id="rmvButton">
                                    <td colspan="2" style="padding:0px;"><button class="btn btn-danger col-sm-12 btnRemoveComponente" type="button">RemoveR</button></td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                    <!--DISSERTATIVAS-->
                    <div class="dissertativaModel" style="display:none;">
                        <table class="table table-bordered border-primary text-center bimestri primeiroBimestre bimestral" data-periodo='primeiroBimestre'>
                            <thead>
                                <tr>
                                    <th><strong>Enunciado</strong></th>
                                </tr>
                                <tr>
                                    <th colspan="2"><strong contenteditable="true" style="padding:5px;" class="componente"></strong></th>
                                </tr>
                                <tr id="rmvButton">
                                    <td colspan="2" style="padding:0px;"><button class="btn btn-danger col-sm-12 btnRemoveComponente" type="button">Remover</button></td>
                                </tr>
                            </thead>
                        </table>
                    </div>
                    <!--FIM DOS MODELOS-->
                    <div class="componentes">
                        <!--PRIMEIRO BIMESTRE PHP-->
                        @if(isset($Formulario))
                            @foreach($Formulario as $f)
                            <table class="table table-bordered border-primary text-center bimestri primeiroBimestre bimestral" data-periodo='primeiroBimestre'>
                                <thead>
                                    <tr>
                                        <th colspan="2"><strong>Enunciado</strong></th>
                                    </tr>
                                    <tr>
                                        <th colspan="2"><strong contenteditable="true" style="padding:5px;" class="componente">{{$f->Conteudo}}</strong></th>
                                    </tr>
                                    <tr>
                                        <th><strong>Conteúdo</strong></th>
                                        <th>Excluir</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr style="display:none;" class="conteudoModel">
                                        <td contenteditable="true" class="conteudo"></td>
                                        <td><button class='btn btn-danger btn-xs btnRemoveConteudo'>X</button></td>
                                    </tr>
                                    @if(count($f->Conteudos) > 0)
                                        @foreach($f->Conteudos as $fc)
                                        <tr class="conteudoModel">
                                            <td contenteditable="true" class="conteudo">{{$fc}}</td>
                                            <td><button class='btn btn-danger btn-xs btnRemoveConteudo'>X</button></td>
                                        </tr>
                                        @endforeach
                                    @endif
                                    <tr id="adcButton">
                                        <td colspan="2" style="padding:0px;"><button class="btn btn-light col-sm-12 btnConteudo" type="button">Adicionar Opção</button></td>
                                    </tr>
                                    <tr id="rmvButton">
                                        <td colspan="2" style="padding:0px;"><button class="btn btn-danger col-sm-12 btnRemoveComponente" type="button">Remover</button></td>
                                    </tr>
                                </tbody>
                            </table>
                            @endforeach
                        @endif
                        <!------------------------->
                    </div>
                    <br>
                    <div class="row">
                        <button class="btn btn-success saveEnunciado col-auto">Salvar</button>
                        <a class="btn btn-light col-auto" href="{{route('Fichas/index')}}">Voltar</a>
                    </div>
                    <!---->
                </div>
            </div>
        </div>
    {{-- </form> --}}
    <script>
        //COMPONENTES
        // $(".btnComponente").on("click",function(){
        //     $(this).parents(".card-body").find(".componentes").append($(this).parents('.card-body').find(".componenteModel").html())
        // })
        //BOTÃO DE ADICIONAR COMPONENTE
        $("select[name=AdicionarComponente]").on("change",function(){
            //alert("aa")
            if($(this).val() == "Objetiva"){
                //alert("aa")
                $(this).parents(".card-body").find(".componentes").append($(this).parents('.card-body').find(".objetivaModel").html())
            }else{
                $(this).parents(".card-body").find(".componentes").append($(this).parents('.card-body').find(".dissertativaModel").html())
            }
        })
        //REMOVER COMPONENTE
        $(".componentes").on("click", ".btnRemoveComponente", function() {
            $(this).parents('.bimestri').remove()
        });
        //OPÇÕES DE DISSERTATIVAS
        $(".componentes").on("click", ".btnConteudo", function() {
            var $closestComponent = $(this).closest(".bimestri");
            $("<tr>" + $(".conteudoModel").html() + "</tr>").insertBefore($closestComponent.find("#adcButton"));
        });
        //
        $(".bimestre").on("click",".btnRemoveConteudo",function(){
            $(this).parents("tr").remove()
        })
        //MONTAGEM DO  JSON
        $(".saveEnunciado").on("click",function(){
            var enunciados = []
                var conteudos = []

            $(".bimestral").each(function(){
                //PRIMEIRO BIMESTRE
                enunciados.push({
                        Conteudo : $(".componente",this).text(),
                        Conteudos  : $.map($('.conteudo',this), function(element) {
                            if($(element).text() != ''){
                                return $(element).text();
                            }
                        })
                })
                //
            })
            
            var submit = {
                Titulo : $("input[name=Titulo]").val(),
                IDEscola : $("select[name=IDEscola]").val(),
                Formulario : JSON.stringify(enunciados)
            }

            if($("input[name=id]").val() > 0){
                submit.id = $("input[name=id]").val()
            }
            // console.log(submit)
            // return false
            //console.log()
            //
            //console.log(planejamento)
            //AJAX QUE ENVIA OS DADOS PARA O SERVIDOR
            $.ajax({
                method : 'POST',
                url : "{{route('Fichas/Save')}}",
                data : submit,
                headers : {
                    'X-CSRF-TOKEN' : '{{csrf_token()}}'
                }
            }).done(function(resp){
                // console.log(resp)
                // return false
                r = JSON.parse(resp)
                if(r.status == 'error'){
                    alert("Houve um erro: "+r.mensagem)
                }else{
                    alert("Ficha Avaliativa Salva")
                }
            })
            //
        })
        //
    </script>
</x-educacional-layout>