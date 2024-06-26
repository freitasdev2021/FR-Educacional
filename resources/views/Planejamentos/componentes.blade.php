<x-educacional-layout>
    <div class="fr-card p-0 shadow col-sm-12">
       <div class="fr-card-header">
          @foreach($submodulos as $s)
          <x-submodulo nome="{{$s['nome']}}" endereco="{{$s['endereco']}}" rota="{{route($s['rota'],$id)}}" icon="bx bx-list-ul"/>
          @endforeach
       </div>
       <div class="fr-card-body">
        <input type='hidden' name="Periodo" value="{{$Registro->Periodo}}">
        <div class="row">
            <div class="col-sm-4">
                <button class="btn bg-fr text-white savePlanejamento">Salvar</button>
            </div>
        </div>
        <hr>
        <!---BIMESTRE--->
        @if($Registro->Periodo == 'Bimestral')
            <div class="row">
                <!--PRIMEIRO BIMESTRE-->
                <div class="col-sm-6">
                    <div class="card bimestre">
                        <div class="card-header bg-fr text-white">
                            <strong>1º Bimestre</strong>
                        </div>
                        <div class="card-body periudos">
                            <!--conteudo do card-->
                            <button class="btn btn-light col-sm-12 btnComponente">Adicionar Componente</button>
                            <div class="componenteModel" style="display:none;">
                                <table class="table table-bordered border-primary text-center bimestri primeiroBimestre bimestral">
                                    <thead>
                                        <tr>
                                            <th colspan="2"><strong contenteditable="true" style="padding:5px;" class="componente"></strong></th>
                                        </tr>
                                        <tr>
                                            <th><strong>Inicio:&nbsp;</strong> <b contenteditable="true" class="inicio"></b></th>
                                            <th><strong>Termino:&nbsp;</strong> <b contenteditable="true" class="termino"></b></th>
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
                                        <tr id="adcButton">
                                            <td colspan="2" style="padding:0px;"><button class="btn btn-light col-sm-12 btnConteudo" type="button">Adicionar Conteúdo</button></td>
                                        </tr>
                                        <tr id="rmvButton">
                                            <td colspan="2" style="padding:0px;"><button class="btn btn-danger col-sm-12 btnRemoveComponente" type="button">Remover Componente</button></td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                            <div class="componentes">
                                
                            </div>
                            <!---->
                        </div>
                    </div>
                </div>
                <!--SEGUNDO BIMESTRE-->
                <div class="col-sm-6">
                    <div class="card bimestre">
                        <div class="card-header bg-fr text-white">
                            <strong>2º Bimestre</strong>
                        </div>
                        <div class="card-body">
                            <!--conteudo do card-->
                            <button class="btn btn-light col-sm-12 btnComponente">Adicionar Componente</button>
                            <div class="componenteModel" style="display:none;">
                                <hr>
                                <table class="table table-bordered border-primary text-center bimestri segundoBimestre bimestral">
                                    <thead>
                                        <tr>
                                            <th colspan="2"><strong contenteditable="true" style="padding:5px;" class="componente"></strong></th>
                                        </tr>
                                        <tr>
                                            <th><strong>Inicio:&nbsp;</strong> <b contenteditable="true" class="inicio"></b></th>
                                            <th><strong>Termino:&nbsp;</strong> <b contenteditable="true" class="termino"></b></th>
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
                                        <tr id="adcButton">
                                            <td colspan="2" style="padding:0px;"><button class="btn btn-light col-sm-12 btnConteudo" type="button">Adicionar Conteúdo</button></td>
                                        </tr>
                                        <tr id="rmvButton">
                                            <td colspan="2" style="padding:0px;"><button class="btn btn-danger col-sm-12 btnRemoveComponente" type="button">Remover Componente</button></td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                            <div class="componentes">
                                
                            </div>
                            <!---->
                        </div>
                    </div>
                </div>
                <!--TERCEIRO BIMESTRE-->
                <div class="col-sm-6">
                    <div class="card bimestre">
                        <div class="card-header bg-fr text-white">
                            <strong>3º Bimestre</strong>
                        </div>
                        <div class="card-body">
                            <!--conteudo do card-->
                            <button class="btn btn-light col-sm-12 btnComponente">Adicionar Componente</button>
                            <div class="componenteModel" style="display:none;">
                                <hr>
                                <table class="table table-bordered border-primary text-center bimestri terceiroBimestre bimestral">
                                    <thead>
                                        <tr>
                                            <th colspan="2"><strong contenteditable="true" style="padding:5px;" class="componente"></strong></th>
                                        </tr>
                                        <tr>
                                            <th><strong>Inicio:&nbsp;</strong> <b contenteditable="true" class="inicio"></b></th>
                                            <th><strong>Termino:&nbsp;</strong> <b contenteditable="true" class="termino"></b></th>
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
                                        <tr id="adcButton">
                                            <td colspan="2" style="padding:0px;"><button class="btn btn-light col-sm-12 btnConteudo" type="button">Adicionar Conteúdo</button></td>
                                        </tr>
                                        <tr id="rmvButton">
                                            <td colspan="2" style="padding:0px;"><button class="btn btn-danger col-sm-12 btnRemoveComponente" type="button">Remover Componente</button></td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                            <div class="componentes">
                                
                            </div>
                            <!---->
                        </div>
                    </div>
                </div>
                <!--QUARTO BIMESTRE-->
                <div class="col-sm-6">
                    <div class="card bimestre">
                        <div class="card-header bg-fr text-white">
                            <strong>4º Bimestre</strong>
                        </div>
                        <div class="card-body">
                            <!--conteudo do card-->
                            <button class="btn btn-light col-sm-12 btnComponente">Adicionar Componente</button>
                            <div class="componenteModel" style="display:none;">
                                <hr>
                                <table class="table table-bordered border-primary text-center bimestri quartoBimestre bimestral">
                                    <thead>
                                        <tr>
                                            <th colspan="2"><strong contenteditable="true" style="padding:5px;" class="componente"></strong></th>
                                        </tr>
                                        <tr>
                                            <th><strong>Inicio:&nbsp;</strong> <b contenteditable="true" class="inicio"></b></th>
                                            <th><strong>Termino:&nbsp;</strong> <b contenteditable="true" class="termino"></b></th>
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
                                        <tr id="adcButton">
                                            <td colspan="2" style="padding:0px;"><button class="btn btn-light col-sm-12 btnConteudo" type="button">Adicionar Conteúdo</button></td>
                                        </tr>
                                        <tr id="rmvButton">
                                            <td colspan="2" style="padding:0px;"><button class="btn btn-danger col-sm-12 btnRemoveComponente" type="button">Remover Componente</button></td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                            <div class="componentes">
                                
                            </div>
                            <!---->
                        </div>
                    </div>
                </div>
                <!------------------->
            </div>
            @elseif($Registro->Periodo == 'Trimestral')
            <!---TRIMESTRAL-->
            <div class="row">
                <!--PRIMEIRO BIMESTRE-->
                <div class="col-sm-4">
                    <div class="card bimestre">
                        <div class="card-header bg-fr text-white">
                            <strong>1º Trimestre</strong>
                        </div>
                        <div class="card-body">
                            <!--conteudo do card-->
                            <button class="btn btn-light col-sm-12 btnComponente">Adicionar Componente</button>
                            <div class="componenteModel" style="display:none;">
                                <hr>
                                <table class="table table-bordered border-primary text-center bimestri primeiroTrimestre trimestral">
                                    <thead>
                                        <tr>
                                            <th colspan="2"><strong contenteditable="true" style="padding:5px;" class="componente"></strong></th>
                                        </tr>
                                        <tr>
                                            <th><strong>Inicio:&nbsp;</strong> <b contenteditable="true" class="inicio"></b></th>
                                            <th><strong>Termino:&nbsp;</strong> <b contenteditable="true" class="termino"></b></th>
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
                                        <tr id="adcButton">
                                            <td colspan="2" style="padding:0px;"><button class="btn btn-light col-sm-12 btnConteudo" type="button">Adicionar Conteúdo</button></td>
                                        </tr>
                                        <tr id="rmvButton">
                                            <td colspan="2" style="padding:0px;"><button class="btn btn-danger col-sm-12 btnRemoveComponente" type="button">Remover Componente</button></td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                            <div class="componentes">
                                
                            </div>
                            <!---->
                        </div>
                    </div>
                </div>
                <!--SEGUNDO BIMESTRE-->
                <div class="col-sm-4">
                    <div class="card bimestre">
                        <div class="card-header bg-fr text-white">
                            <strong>2º Trimestre</strong>
                        </div>
                        <div class="card-body">
                            <!--conteudo do card-->
                            <button class="btn btn-light col-sm-12 btnComponente">Adicionar Componente</button>
                            <div class="componenteModel" style="display:none;">
                                <hr>
                                <table class="table table-bordered border-primary text-center bimestri segundoTrimestre trimestral">
                                    <thead>
                                        <tr>
                                            <th colspan="2"><strong contenteditable="true" style="padding:5px;" class="componente"></strong></th>
                                        </tr>
                                        <tr>
                                            <th><strong>Inicio:&nbsp;</strong> <b contenteditable="true" class="inicio"></b></th>
                                            <th><strong>Termino:&nbsp;</strong> <b contenteditable="true" class="termino"></b></th>
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
                                        <tr id="adcButton">
                                            <td colspan="2" style="padding:0px;"><button class="btn btn-light col-sm-12 btnConteudo" type="button">Adicionar Conteúdo</button></td>
                                        </tr>
                                        <tr id="rmvButton">
                                            <td colspan="2" style="padding:0px;"><button class="btn btn-danger col-sm-12 btnRemoveComponente" type="button">Remover Componente</button></td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                            <div class="componentes">
                                
                            </div>
                            <!---->
                        </div>
                    </div>
                </div>
                <!--TERCEIRO BIMESTRE-->
                <div class="col-sm-4">
                    <div class="card bimestre">
                        <div class="card-header bg-fr text-white">
                            <strong>3º Trimestre</strong>
                        </div>
                        <div class="card-body">
                            <!--conteudo do card-->
                            <button class="btn btn-light col-sm-12 btnComponente">Adicionar Componente</button>
                            <div class="componenteModel" style="display:none;">
                                <hr>
                                <table class="table table-bordered border-primary text-center bimestri terceiroTrimestre trimestral">
                                    <thead>
                                        <tr>
                                            <th colspan="2"><strong contenteditable="true" style="padding:5px;" class="componente"></strong></th>
                                        </tr>
                                        <tr>
                                            <th><strong>Inicio:&nbsp;</strong> <b contenteditable="true" class="inicio"></b></th>
                                            <th><strong>Termino:&nbsp;</strong> <b contenteditable="true" class="termino"></b></th>
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
                                        <tr id="adcButton">
                                            <td colspan="2" style="padding:0px;"><button class="btn btn-light col-sm-12 btnConteudo" type="button">Adicionar Conteúdo</button></td>
                                        </tr>
                                        <tr id="rmvButton">
                                            <td colspan="2" style="padding:0px;"><button class="btn btn-danger col-sm-12 btnRemoveComponente" type="button">Remover Componente</button></td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                            <div class="componentes">
                                
                            </div>
                            <!---->
                        </div>
                    </div>
                </div>
                <!------------------->
            </div>
            @elseif($Registro->Periodo == 'Semestral')
            <!--SEMESTRAL-->
            <div class="row">
                <!--PRIMEIRO BIMESTRE-->
                <div class="col-sm-6">
                    <div class="card bimestre">
                        <div class="card-header bg-fr text-white">
                            <strong>1º Semestre</strong>
                        </div>
                        <div class="card-body">
                            <!--conteudo do card-->
                            <button class="btn btn-light col-sm-12 btnComponente">Adicionar Componente</button>
                            <div class="componenteModel" style="display:none;">
                                <hr>
                                <table class="table table-bordered border-primary text-center bimestri primeiroSemestre semestral">
                                    <thead>
                                        <tr>
                                            <th colspan="2"><strong contenteditable="true" style="padding:5px;" class="componente"></strong></th>
                                        </tr>
                                        <tr>
                                            <th><strong>Inicio:&nbsp;</strong> <b contenteditable="true" class="inicio"></b></th>
                                            <th><strong>Termino:&nbsp;</strong> <b contenteditable="true" class="termino"></b></th>
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
                                        <tr id="adcButton">
                                            <td colspan="2" style="padding:0px;"><button class="btn btn-light col-sm-12 btnConteudo" type="button">Adicionar Conteúdo</button></td>
                                        </tr>
                                        <tr id="rmvButton">
                                            <td colspan="2" style="padding:0px;"><button class="btn btn-danger col-sm-12 btnRemoveComponente" type="button">Remover Componente</button></td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                            <div class="componentes">
                                
                            </div>
                            <!---->
                        </div>
                    </div>
                </div>
                <!--SEGUNDO BIMESTRE-->
                <div class="col-sm-6">
                    <div class="card bimestre">
                        <div class="card-header bg-fr text-white">
                            <strong>2º Semestre</strong>
                        </div>
                        <div class="card-body">
                            <!--conteudo do card-->
                            <button class="btn btn-light col-sm-12 btnComponente">Adicionar Componente</button>
                            <div class="componenteModel" style="display:none;">
                                <hr>
                                <table class="table table-bordered border-primary text-center bimestri segundoSemestre semestral">
                                    <thead>
                                        <tr>
                                            <th colspan="2"><strong contenteditable="true" style="padding:5px;" class="componente"></strong></th>
                                        </tr>
                                        <tr>
                                            <th><strong>Inicio:&nbsp;</strong> <b contenteditable="true" class="inicio"></b></th>
                                            <th><strong>Termino:&nbsp;</strong> <b contenteditable="true" class="termino"></b></th>
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
                                        <tr id="adcButton">
                                            <td colspan="2" style="padding:0px;"><button class="btn btn-light col-sm-12 btnConteudo" type="button">Adicionar Conteúdo</button></td>
                                        </tr>
                                        <tr id="rmvButton">
                                            <td colspan="2" style="padding:0px;"><button class="btn btn-danger col-sm-12 btnRemoveComponente" type="button">Remover Componente</button></td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                            <div class="componentes">
                                
                            </div>
                            <!---->
                        </div>
                    </div>
                </div>
                <!------------------->
            </div>
            @elseif($Registro->Periodo == 'Anual')
            <!--ANUAL-->
            <div class="row">
                <!--PRIMEIRO BIMESTRE-->
                <div class="col-sm-6">
                    <div class="card bimestre">
                        <div class="card-header bg-fr text-white">
                            <strong>1º Periodo</strong>
                        </div>
                        <div class="card-body">
                            <!--conteudo do card-->
                            <button class="btn btn-light col-sm-12 btnComponente">Adicionar Componente</button>
                            <div class="componenteModel" style="display:none;">
                                <hr>
                                <table class="table table-bordered border-primary text-center bimestri primeiroPeriodo anual">
                                    <thead>
                                        <tr>
                                            <th colspan="2"><strong contenteditable="true" style="padding:5px;" class="componente"></strong></th>
                                        </tr>
                                        <tr>
                                            <th><strong>Inicio:&nbsp;</strong> <b contenteditable="true" class="inicio"></b></th>
                                            <th><strong>Termino:&nbsp;</strong> <b contenteditable="true" class="termino"></b></th>
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
                                        <tr id="adcButton">
                                            <td colspan="2" style="padding:0px;"><button class="btn btn-light col-sm-12 btnConteudo" type="button">Adicionar Conteúdo</button></td>
                                        </tr>
                                        <tr id="rmvButton">
                                            <td colspan="2" style="padding:0px;"><button class="btn btn-danger col-sm-12 btnRemoveComponente" type="button">Remover Componente</button></td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                            <div class="componentes">
                                
                            </div>
                            <!---->
                        </div>
                    </div>
                </div>
                <!------------------->
            </div>
            @endif
            <!----------------------------->
        </div>
    </div>
    <script>
        //COMPONENTES
        $(".btnComponente").on("click",function(){
            $(this).parents(".card-body").find(".componentes").append($(".componenteModel").html())
        })

        $(".componentes").on("click", ".btnRemoveComponente", function() {
            $(this).parents('.bimestri').remove()
        });
        //CONTEUDOS
        $(".componentes").on("click", ".btnConteudo", function() {
            var $closestComponent = $(this).closest(".bimestri");
            $("<tr>" + $(".conteudoModel").html() + "</tr>").insertBefore($closestComponent.find("#adcButton"));
        });
        //
        $(".bimestre").on("click",".btnRemoveConteudo",function(){
            $(this).parents("tr").remove()
        })
        //MONTAGEM DO  JSON
        $(".savePlanejamento").on("click",function(){
            alert("aa")
            if($("input[name=Periodo]").val() == 'Bimestral'){
                var planejamento = {
                    'primeiroBimestre' : [],
                    'segundoBimestre' : [],
                    'terceiroBimestre' : [],
                    'quartoBimestre' : []
                }

                $(".bimestral").each(function(){
                    if($(".componente",this).text() !=''){
                        console.log($(".componente",this).text())
                    }
                })

            }else if($("input[name=Periodo]").val() == 'Trimestral'){
                var planejamento = {
                    'primeiroTrimestre' : [],
                    'segundoTrimestre' : [],
                    'terceiroTrimestre' : []
                }
            }else if($("input[name=Periodo]").val() == 'Semestral'){
                var planejamento = {
                    'primeiroSemestre' : [],
                    'segundoSemestre' : []
                }
            }else if($("input[name=Periodo]").val() == 'Anual'){
                var planejamento = {
                    'primeiroSemestre' : [],
                    'segundoSemestre' : []
                }
            }
            //console.log(planejamento)
        })
        //
    </script>
 </x-educacional-layout>