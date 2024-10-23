<x-educacional-layout>
    <div class="fr-card p-0 shadow col-sm-12">
       <div class="fr-card-header">
          @foreach($submodulos as $s)
          <x-submodulo nome="{{$s['nome']}}" endereco="{{$s['endereco']}}" rota="{{route($s['rota'],$id)}}" icon="bx bx-list-ul"/>
          @endforeach
       </div>
       <div class="fr-card-body">
        <input type='hidden' name="Periodo" value="{{$Registro->Periodo}}">
        @if(Auth::user()->tipo == 6)
        <div class="row">
            <div class="col-sm-4">
                <button class="btn bg-fr text-white savePlanejamento">Salvar</button>
            </div>
        </div>
        <hr>
        @endif
        {{-- <pre>
            {{print_r($Curriculo)}}
        </pre> --}}
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
                                <table class="table table-bordered border-primary text-center bimestri primeiroBimestre bimestral" data-periodo='primeiroBimestre'>
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
                                <!--PRIMEIRO BIMESTRE PHP-->
                                @if(isset($Curriculo->primeiroBimestre))
                                    @foreach($Curriculo->primeiroBimestre as $pb)
                                    <table class="table table-bordered border-primary text-center bimestri primeiroBimestre bimestral" data-periodo='primeiroBimestre'>
                                        <thead>
                                            <tr>
                                                <th colspan="2"><strong contenteditable="true" style="padding:5px;" class="componente">{{$pb->Conteudo}}</strong></th>
                                            </tr>
                                            <tr>
                                                <th><strong>Inicio:&nbsp;</strong> <b contenteditable="true" class="inicio">{{$pb->Inicio}}</b></th>
                                                <th><strong>Termino:&nbsp;</strong> <b contenteditable="true" class="termino">{{$pb->Termino}}</b></th>
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
                                            @foreach($pb->Conteudos as $cpb)
                                            <tr class="conteudoModel">
                                                <td contenteditable="true" class="conteudo">{{$cpb}}</td>
                                                <td><button class='btn btn-danger btn-xs btnRemoveConteudo'>X</button></td>
                                            </tr>
                                            @endforeach
                                            <tr id="adcButton">
                                                <td colspan="2" style="padding:0px;"><button class="btn btn-light col-sm-12 btnConteudo" type="button">Adicionar Conteúdo</button></td>
                                            </tr>
                                            <tr id="rmvButton">
                                                <td colspan="2" style="padding:0px;"><button class="btn btn-danger col-sm-12 btnRemoveComponente" type="button">Remover Componente</button></td>
                                            </tr>
                                        </tbody>
                                    </table>
                                    @endforeach
                                @endif
                                <!------------------------->
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
                                <table class="table table-bordered border-primary text-center bimestri segundoBimestre bimestral" data-periodo='segundoBimestre'>
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
                                <!--SEGUNDO BIMESTRE PHP-->
                                @if(isset($Curriculo->segundoBimestre))
                                    @foreach($Curriculo->segundoBimestre as $sb)
                                    <table class="table table-bordered border-primary text-center bimestri segundoBimestre bimestral" data-periodo='segundoBimestre'>
                                        <thead>
                                            <tr>
                                                <th colspan="2"><strong contenteditable="true" style="padding:5px;" class="componente">{{$sb->Conteudo}}</strong></th>
                                            </tr>
                                            <tr>
                                                <th><strong>Inicio:&nbsp;</strong> <b contenteditable="true" class="inicio">{{$sb->Inicio}}</b></th>
                                                <th><strong>Termino:&nbsp;</strong> <b contenteditable="true" class="termino">{{$sb->Termino}}</b></th>
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
                                            @foreach($sb->Conteudos as $csb)
                                            <tr class="conteudoModel">
                                                <td contenteditable="true" class="conteudo">{{$csb}}</td>
                                                <td><button class='btn btn-danger btn-xs btnRemoveConteudo'>X</button></td>
                                            </tr>
                                            @endforeach
                                            <tr id="adcButton">
                                                <td colspan="2" style="padding:0px;"><button class="btn btn-light col-sm-12 btnConteudo" type="button">Adicionar Conteúdo</button></td>
                                            </tr>
                                            <tr id="rmvButton">
                                                <td colspan="2" style="padding:0px;"><button class="btn btn-danger col-sm-12 btnRemoveComponente" type="button">Remover Componente</button></td>
                                            </tr>
                                        </tbody>
                                    </table>
                                    @endforeach
                                @endif
                                <!------------------------->
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
                                <table class="table table-bordered border-primary text-center bimestri terceiroBimestre bimestral" data-periodo='terceiroBimestre'>
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
                            <!--TERCEIRO BIMESTRE PHP-->
                            @if(isset($Curriculo->terceiroBimestre))
                                @foreach($Curriculo->terceiroBimestre as $tb)
                                <table class="table table-bordered border-primary text-center bimestri terceiroBimestre bimestral" data-periodo='terceiroBimestre'>
                                    <thead>
                                        <tr>
                                            <th colspan="2"><strong contenteditable="true" style="padding:5px;" class="componente">{{$tb->Conteudo}}</strong></th>
                                        </tr>
                                        <tr>
                                            <th><strong>Inicio:&nbsp;</strong> <b contenteditable="true" class="inicio">{{$tb->Inicio}}</b></th>
                                            <th><strong>Termino:&nbsp;</strong> <b contenteditable="true" class="termino">{{$tb->Termino}}</b></th>
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
                                        @foreach($tb->Conteudos as $ctb)
                                        <tr class="conteudoModel">
                                            <td contenteditable="true" class="conteudo">{{$ctb}}</td>
                                            <td><button class='btn btn-danger btn-xs btnRemoveConteudo'>X</button></td>
                                        </tr>
                                        @endforeach
                                        <tr id="adcButton">
                                            <td colspan="2" style="padding:0px;"><button class="btn btn-light col-sm-12 btnConteudo" type="button">Adicionar Conteúdo</button></td>
                                        </tr>
                                        <tr id="rmvButton">
                                            <td colspan="2" style="padding:0px;"><button class="btn btn-danger col-sm-12 btnRemoveComponente" type="button">Remover Componente</button></td>
                                        </tr>
                                    </tbody>
                                </table>
                                @endforeach
                            @endif
                            <!------------------------->
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
                                <table class="table table-bordered border-primary text-center bimestri quartoBimestre bimestral" data-periodo='quartoBimestre'>
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
                            <!--QUARTO BIMESTRE PHP-->
                            @if(isset($Curriculo->quartoBimestre))
                                @foreach($Curriculo->quartoBimestre as $qb)
                                <table class="table table-bordered border-primary text-center bimestri quartoBimestre bimestral" data-periodo='quartoBimestre'>
                                    <thead>
                                        <tr>
                                            <th colspan="2"><strong contenteditable="true" style="padding:5px;" class="componente">{{$qb->Conteudo}}</strong></th>
                                        </tr>
                                        <tr>
                                            <th><strong>Inicio:&nbsp;</strong> <b contenteditable="true" class="inicio">{{$qb->Inicio}}</b></th>
                                            <th><strong>Termino:&nbsp;</strong> <b contenteditable="true" class="termino">{{$qb->Termino}}</b></th>
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
                                        @foreach($qb->Conteudos as $cqb)
                                        <tr class="conteudoModel">
                                            <td contenteditable="true" class="conteudo">{{$cqb}}</td>
                                            <td><button class='btn btn-danger btn-xs btnRemoveConteudo'>X</button></td>
                                        </tr>
                                        @endforeach
                                        <tr id="adcButton">
                                            <td colspan="2" style="padding:0px;"><button class="btn btn-light col-sm-12 btnConteudo" type="button">Adicionar Conteúdo</button></td>
                                        </tr>
                                        <tr id="rmvButton">
                                            <td colspan="2" style="padding:0px;"><button class="btn btn-danger col-sm-12 btnRemoveComponente" type="button">Remover Componente</button></td>
                                        </tr>
                                    </tbody>
                                </table>
                                @endforeach
                            @endif
                            <!------------------------->
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
                                <table class="table table-bordered border-primary text-center bimestri primeiroTrimestre trimestral" data-periodo="primeiroTrimestre">
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
                                <!--QUARTO BIMESTRE PHP-->
                                @if(isset($Curriculo->primeiroTrimestre))
                                    @foreach($Curriculo->primeiroTrimestre as $pt)
                                    <table class="table table-bordered border-primary text-center bimestri primeiroTrimestre trimestral" data-periodo='primeiroTrimestre'>
                                        <thead>
                                            <tr>
                                                <th colspan="2"><strong contenteditable="true" style="padding:5px;" class="componente">{{$pt->Conteudo}}</strong></th>
                                            </tr>
                                            <tr>
                                                <th><strong>Inicio:&nbsp;</strong> <b contenteditable="true" class="inicio">{{$pt->Inicio}}</b></th>
                                                <th><strong>Termino:&nbsp;</strong> <b contenteditable="true" class="termino">{{$pt->Termino}}</b></th>
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
                                            @foreach($pt->Conteudos as $cpt)
                                            <tr class="conteudoModel">
                                                <td contenteditable="true" class="conteudo">{{$cpt}}</td>
                                                <td><button class='btn btn-danger btn-xs btnRemoveConteudo'>X</button></td>
                                            </tr>
                                            @endforeach
                                            <tr id="adcButton">
                                                <td colspan="2" style="padding:0px;"><button class="btn btn-light col-sm-12 btnConteudo" type="button">Adicionar Conteúdo</button></td>
                                            </tr>
                                            <tr id="rmvButton">
                                                <td colspan="2" style="padding:0px;"><button class="btn btn-danger col-sm-12 btnRemoveComponente" type="button">Remover Componente</button></td>
                                            </tr>
                                        </tbody>
                                    </table>
                                    @endforeach
                                @endif
                                <!------------------------->
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
                                <table class="table table-bordered border-primary text-center bimestri segundoTrimestre trimestral" data-periodo="segundoTrimestre">
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
                                <!--QUARTO BIMESTRE PHP-->
                                @if(isset($Curriculo->segundoTrimestre))
                                    @foreach($Curriculo->segundoTrimestre as $st)
                                    <table class="table table-bordered border-primary text-center bimestri segundoTrimestre trimestral" data-periodo='segundoTrimestre'>
                                        <thead>
                                            <tr>
                                                <th colspan="2"><strong contenteditable="true" style="padding:5px;" class="componente">{{$pt->Conteudo}}</strong></th>
                                            </tr>
                                            <tr>
                                                <th><strong>Inicio:&nbsp;</strong> <b contenteditable="true" class="inicio">{{$st->Inicio}}</b></th>
                                                <th><strong>Termino:&nbsp;</strong> <b contenteditable="true" class="termino">{{$st->Termino}}</b></th>
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
                                            @foreach($st->Conteudos as $cst)
                                            <tr class="conteudoModel">
                                                <td contenteditable="true" class="conteudo">{{$cst}}</td>
                                                <td><button class='btn btn-danger btn-xs btnRemoveConteudo'>X</button></td>
                                            </tr>
                                            @endforeach
                                            <tr id="adcButton">
                                                <td colspan="2" style="padding:0px;"><button class="btn btn-light col-sm-12 btnConteudo" type="button">Adicionar Conteúdo</button></td>
                                            </tr>
                                            <tr id="rmvButton">
                                                <td colspan="2" style="padding:0px;"><button class="btn btn-danger col-sm-12 btnRemoveComponente" type="button">Remover Componente</button></td>
                                            </tr>
                                        </tbody>
                                    </table>
                                    @endforeach
                                @endif
                                <!------------------------->
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
                                <table class="table table-bordered border-primary text-center bimestri terceiroTrimestre trimestral" data-periodo="terceiroTrimestre">
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
                                <!--QUARTO BIMESTRE PHP-->
                                @if(isset($Curriculo->terceiroTrimestre))
                                    @foreach($Curriculo->terceiroTrimestre as $tt)
                                    <table class="table table-bordered border-primary text-center bimestri terceiroTrimestre trimestral" data-periodo='terceiroTrimestre'>
                                        <thead>
                                            <tr>
                                                <th colspan="2"><strong contenteditable="true" style="padding:5px;" class="componente">{{$tt->Conteudo}}</strong></th>
                                            </tr>
                                            <tr>
                                                <th><strong>Inicio:&nbsp;</strong> <b contenteditable="true" class="inicio">{{$tt->Inicio}}</b></th>
                                                <th><strong>Termino:&nbsp;</strong> <b contenteditable="true" class="termino">{{$tt->Termino}}</b></th>
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
                                            @foreach($tt->Conteudos as $ctt)
                                            <tr class="conteudoModel">
                                                <td contenteditable="true" class="conteudo">{{$ctt}}</td>
                                                <td><button class='btn btn-danger btn-xs btnRemoveConteudo'>X</button></td>
                                            </tr>
                                            @endforeach
                                            <tr id="adcButton">
                                                <td colspan="2" style="padding:0px;"><button class="btn btn-light col-sm-12 btnConteudo" type="button">Adicionar Conteúdo</button></td>
                                            </tr>
                                            <tr id="rmvButton">
                                                <td colspan="2" style="padding:0px;"><button class="btn btn-danger col-sm-12 btnRemoveComponente" type="button">Remover Componente</button></td>
                                            </tr>
                                        </tbody>
                                    </table>
                                    @endforeach
                                @endif
                                <!------------------------->
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
                                <table class="table table-bordered border-primary text-center bimestri primeiroSemestre semestral" data-periodo="primeiroSemestre">
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
                                <!--QUARTO BIMESTRE PHP-->
                                @if(isset($Curriculo->primeiroSemestre))
                                    @foreach($Curriculo->primeiroSemestre as $ps)
                                    <table class="table table-bordered border-primary text-center bimestri primeiroSemestre semestral" data-periodo='primeiroSemestre'>
                                        <thead>
                                            <tr>
                                                <th colspan="2"><strong contenteditable="true" style="padding:5px;" class="componente">{{$ps->Conteudo}}</strong></th>
                                            </tr>
                                            <tr>
                                                <th><strong>Inicio:&nbsp;</strong> <b contenteditable="true" class="inicio">{{$ps->Inicio}}</b></th>
                                                <th><strong>Termino:&nbsp;</strong> <b contenteditable="true" class="termino">{{$ps->Termino}}</b></th>
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
                                            @foreach($ps->Conteudos as $cps)
                                            <tr class="conteudoModel">
                                                <td contenteditable="true" class="conteudo">{{$cps}}</td>
                                                <td><button class='btn btn-danger btn-xs btnRemoveConteudo'>X</button></td>
                                            </tr>
                                            @endforeach
                                            <tr id="adcButton">
                                                <td colspan="2" style="padding:0px;"><button class="btn btn-light col-sm-12 btnConteudo" type="button">Adicionar Conteúdo</button></td>
                                            </tr>
                                            <tr id="rmvButton">
                                                <td colspan="2" style="padding:0px;"><button class="btn btn-danger col-sm-12 btnRemoveComponente" type="button">Remover Componente</button></td>
                                            </tr>
                                        </tbody>
                                    </table>
                                    @endforeach
                                @endif
                                <!------------------------->
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
                                <table class="table table-bordered border-primary text-center bimestri segundoSemestre semestral" data-periodo="segundoSemestre">
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
                                <!--QUARTO BIMESTRE PHP-->
                                @if(isset($Curriculo->segundoSemestre))
                                    @foreach($Curriculo->segundoSemestre as $ss)
                                    <table class="table table-bordered border-primary text-center bimestri segundoSemestre semestral" data-periodo='segundoSemestre'>
                                        <thead>
                                            <tr>
                                                <th colspan="2"><strong contenteditable="true" style="padding:5px;" class="componente">{{$ss->Conteudo}}</strong></th>
                                            </tr>
                                            <tr>
                                                <th><strong>Inicio:&nbsp;</strong> <b contenteditable="true" class="inicio">{{$ss->Inicio}}</b></th>
                                                <th><strong>Termino:&nbsp;</strong> <b contenteditable="true" class="termino">{{$ss->Termino}}</b></th>
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
                                            @foreach($ss->Conteudos as $css)
                                            <tr class="conteudoModel">
                                                <td contenteditable="true" class="conteudo">{{$css}}</td>
                                                <td><button class='btn btn-danger btn-xs btnRemoveConteudo'>X</button></td>
                                            </tr>
                                            @endforeach
                                            <tr id="adcButton">
                                                <td colspan="2" style="padding:0px;"><button class="btn btn-light col-sm-12 btnConteudo" type="button">Adicionar Conteúdo</button></td>
                                            </tr>
                                            <tr id="rmvButton">
                                                <td colspan="2" style="padding:0px;"><button class="btn btn-danger col-sm-12 btnRemoveComponente" type="button">Remover Componente</button></td>
                                            </tr>
                                        </tbody>
                                    </table>
                                    @endforeach
                                @endif
                                <!------------------------->
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
                                <table class="table table-bordered border-primary text-center bimestri primeiroPeriodo anual" data-periodo="primeiroPeriodo">
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
                                <!--QUARTO BIMESTRE PHP-->
                                @if(isset($Curriculo->primeiroPeriodo))
                                    @foreach($Curriculo->primeiroPeriodo as $pp)
                                    <table class="table table-bordered border-primary text-center bimestri primeiroPeriodo anual" data-periodo='primeiroPeriodo'>
                                        <thead>
                                            <tr>
                                                <th colspan="2"><strong contenteditable="true" style="padding:5px;" class="componente">{{$pp->Conteudo}}</strong></th>
                                            </tr>
                                            <tr>
                                                <th><strong>Inicio:&nbsp;</strong> <b contenteditable="true" class="inicio">{{$pp->Inicio}}</b></th>
                                                <th><strong>Termino:&nbsp;</strong> <b contenteditable="true" class="termino">{{$pp->Termino}}</b></th>
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
                                            @foreach($pp->Conteudos as $cpp)
                                            <tr class="conteudoModel">
                                                <td contenteditable="true" class="conteudo">{{$cpp}}</td>
                                                <td><button class='btn btn-danger btn-xs btnRemoveConteudo'>X</button></td>
                                            </tr>
                                            @endforeach
                                            <tr id="adcButton">
                                                <td colspan="2" style="padding:0px;"><button class="btn btn-light col-sm-12 btnConteudo" type="button">Adicionar Conteúdo</button></td>
                                            </tr>
                                            <tr id="rmvButton">
                                                <td colspan="2" style="padding:0px;"><button class="btn btn-danger col-sm-12 btnRemoveComponente" type="button">Remover Componente</button></td>
                                            </tr>
                                        </tbody>
                                    </table>
                                    @endforeach
                                @endif
                                <!------------------------->
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
            $(this).parents(".card-body").find(".componentes").append($(this).parents('.card-body').find(".componenteModel").html())
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
            if($("input[name=Periodo]").val() == 'Bimestral'){
                var planejamento = {
                    'primeiroBimestre' : [],
                    'segundoBimestre' : [],
                    'terceiroBimestre' : [],
                    'quartoBimestre' : []
                }
                var conteudos = []

                $(".bimestral").each(function(){
                    //PRIMEIRO BIMESTRE
                    if($(this).attr('data-periodo') == 'primeiroBimestre' && $(".componente",this).text() != ''){
                        planejamento.primeiroBimestre.push({
                            Conteudo : $(".componente",this).text(),
                            Inicio   : $('.inicio',this).text(),
                            Termino  : $('.termino',this).text(),
                            Conteudos  : $.map($('.conteudo',this), function(element) {
                                if($(element).text() != ''){
                                    return $(element).text();
                                }
                            })
                        })
                    }
                    //SEGUNDO BIMESTRE
                    if($(this).attr('data-periodo') == 'segundoBimestre' && $(".componente",this).text() != ''){
                        planejamento.segundoBimestre.push({
                            Conteudo : $(".componente",this).text(),
                            Inicio   : $('.inicio',this).text(),
                            Termino  : $('.termino',this).text(),
                            Conteudos  : $.map($('.conteudo',this), function(element) {
                                if($(element).text() != ''){
                                    return $(element).text();
                                }
                            })
                        }) 
                    }
                    //TERCEIRO BIMESTRE
                    if($(this).attr('data-periodo') == 'terceiroBimestre' && $(".componente",this).text() != ''){
                        planejamento.terceiroBimestre.push({
                            Conteudo : $(".componente",this).text(),
                            Inicio   : $('.inicio',this).text(),
                            Termino  : $('.termino',this).text(),
                            Conteudos  : $.map($('.conteudo',this), function(element) {
                                if($(element).text() != ''){
                                    return $(element).text();
                                }
                            })
                        }) 
                    }
                    //QUARTO BIMESTRE
                    if($(this).attr('data-periodo') == 'quartoBimestre' && $(".componente",this).text() != ''){
                        planejamento.quartoBimestre.push({
                            Conteudo : $(".componente",this).text(),
                            Inicio   : $('.inicio',this).text(),
                            Termino  : $('.termino',this).text(),
                            Conteudos  : $.map($('.conteudo',this), function(element) {
                                if($(element).text() != ''){
                                    return $(element).text();
                                }
                            })
                        }) 
                    }
                    //
                })
                //
            }else if($("input[name=Periodo]").val() == 'Trimestral'){
                var planejamento = {
                    'primeiroTrimestre' : [],
                    'segundoTrimestre' : [],
                    'terceiroTrimestre' : []
                }
                var conteudos = []
                $(".trimestral").each(function(){
                    //PRIMEIRO TRIMESTRE
                    if($(this).attr('data-periodo') == 'primeiroTrimestre' && $(".componente",this).text() != ''){
                        planejamento.primeiroTrimestre.push({
                            Conteudo : $(".componente",this).text(),
                            Inicio   : $('.inicio',this).text(),
                            Termino  : $('.termino',this).text(),
                            Conteudos  : $.map($('.conteudo',this), function(element) {
                                if($(element).text() != ''){
                                    return $(element).text();
                                }
                            })
                        })
                    }
                    //SEGUNDO TRIMESTRE
                    if($(this).attr('data-periodo') == 'segundoTrimestre' && $(".componente",this).text() != ''){
                        planejamento.segundoTrimestre.push({
                            Conteudo : $(".componente",this).text(),
                            Inicio   : $('.inicio',this).text(),
                            Termino  : $('.termino',this).text(),
                            Conteudos  : $.map($('.conteudo',this), function(element) {
                                if($(element).text() != ''){
                                    return $(element).text();
                                }
                            })
                        }) 
                    }
                    //TERCEIRO TRIMESTRE
                    if($(this).attr('data-periodo') == 'terceiroTrimestre' && $(".componente",this).text() != ''){
                        planejamento.terceiroTrimestre.push({
                            Conteudo : $(".componente",this).text(),
                            Inicio   : $('.inicio',this).text(),
                            Termino  : $('.termino',this).text(),
                            Conteudos  : $.map($('.conteudo',this), function(element) {
                                if($(element).text() != ''){
                                    return $(element).text();
                                }
                            })
                        }) 
                    }
                    //
                })
                //
            }else if($("input[name=Periodo]").val() == 'Semestral'){
                var planejamento = {
                    'primeiroSemestre' : [],
                    'segundoSemestre' : []
                }
                var conteudos = []
                $(".semestral").each(function(){
                    //PRIMEIRO BIMESTRE
                    if($(this).attr('data-periodo') == 'primeiroSemestre' && $(".componente",this).text() != ''){
                        planejamento.primeiroSemestre.push({
                            Conteudo : $(".componente",this).text(),
                            Inicio   : $('.inicio',this).text(),
                            Termino  : $('.termino',this).text(),
                            Conteudos  : $.map($('.conteudo',this), function(element) {
                                if($(element).text() != ''){
                                    return $(element).text();
                                }
                            })
                        })
                    }
                    //SEGUNDO BIMESTRE
                    if($(this).attr('data-periodo') == 'segundoSemestre' && $(".componente",this).text() != ''){
                        planejamento.segundoSemestre.push({
                            Conteudo : $(".componente",this).text(),
                            Inicio   : $('.inicio',this).text(),
                            Termino  : $('.termino',this).text(),
                            Conteudos  : $.map($('.conteudo',this), function(element) {
                                if($(element).text() != ''){
                                    return $(element).text();
                                }
                            })
                        }) 
                    }
                    //
                })
                //
            }else if($("input[name=Periodo]").val() == 'Anual'){
                var planejamento = {
                    'primeiroSemestre' : [],
                    'segundoSemestre' : []
                }
                var conteudos = []
                $(".anual").each(function(){
                    //PRIMEIRO BIMESTRE
                    if($(this).attr('data-periodo') == 'primeiroPeriodo' && $(".componente",this).text() != ''){
                        planejamento.primeiroSemestre.push({
                            Conteudo : $(".componente",this).text(),
                            Inicio   : $('.inicio',this).text(),
                            Termino  : $('.termino',this).text(),
                            Conteudos  : $.map($('.conteudo',this), function(element) {
                                if($(element).text() != ''){
                                    return $(element).text();
                                }
                            })
                        })
                    }
                })
                //
            }
            //console.log(planejamento)
            //AJAX QUE ENVIA OS DADOS PARA O SERVIDOR
            $.ajax({
                method : 'POST',
                url : '/Planejamentos/Componentes/Save',
                data : {
                    PLConteudos : JSON.stringify(planejamento),
                    IDPlanejamento : {{$id}}
                },
                headers : {
                    'X-CSRF-TOKEN' : '{{csrf_token()}}'
                }
            }).done(function(resp){
                rsp = JSON.parse(resp)
                console.log(rsp)
                alert(rsp.mensagem)
            })
            //
        })
        //
    </script>
 </x-educacional-layout>