<x-educacional-layout>
    <div class="fr-card p-0 shadow col-sm-12">
        <div class="fr-card-header">
           @foreach($submodulos as $s)
            <x-submodulo nome="{{$s['nome']}}" endereco="{{$s['endereco']}}" rota="{{route($s['rota'],$id)}}" icon="bx bx-list-ul"/>
           @endforeach
        </div>
        <div class="fr-card-body">
            <!--LISTAS-->
            <div class="col-sm-12 p-2 center-form">
                <form action="{{route('Aulas/Atividades/Save')}}" method="POST">
                    @csrf
                    @method("POST")
                    @if(session('success'))
                    <div class="col-sm-12 shadow p-2 bg-success text-white">
                        <strong>{{session('success')}}</strong>
                    </div>
                    @elseif(session('error'))
                    <div class="col-sm-12 shadow p-2 bg-danger text-white">
                        <strong>{{session('error')}}</strong>
                    </div>
                    <br>
                    @endif
                    @if(isset($Registro->id))
                    <input type="hidden" name="id" value="{{$Registro->id}}">
                    @endif
                    <input type="hidden" name="IDOrg" value="{{Auth::user()->id_org}}">
                    <div class="row">
                        <!--CADASTRO-->
                        <div class="col-sm-6">
                            <div class="row">
                                <div class="col-sm-6">
                                    <label>Aula da Atividade</label>
                                    <select class="form-control" name="IDAula" data-alunos="{{route('Aulas/getAlunos')}}">
                                        <option value="">Selecione</option>
                                        @foreach($Aulas as $a)
                                        <option value="{{$a->id}}" {{(isset($Registro) && $Registro->IDAula == $a->id) ? 'selected' : ''}}>{{$a->DSAula}}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-sm-6">
                                    <label>Data de Entrega</label>
                                    <input type="datetime-local" class="form-control" name="DTEntrega" value="{{isset($Registro->DTEntrega) ? $Registro->DTEntrega : ''}}">
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-sm-10">
                                    <label>Conteudo</label>
                                    <input type="text" class="form-control" name="TPConteudo" value="{{isset($Registro->TPConteudo) ? $Registro->TPConteudo : ''}}">
                                </div>
                                <div class="col-sm-2">
                                    <label>Pontuação</label>
                                    <input type="number" class="form-control" name="Pontuacao" value="{{isset($Registro->Pontuacao) ? $Registro->Pontuacao : ''}}">
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-sm-12">
                                    <label>Descrição da Atividade</label>
                                    <textarea name="DSAtividade" class="form-control">{{isset($Registro->DSAtividade) ? $Registro->DSAtividade : ''}}</textarea>
                                </div>
                            </div>
                        </div>
                        <!--ATRIBUIÇÕES DO CARGO-->
                        <div class="col-sm-6" align="center">
                            <label col="col-sm-6">Atribuições das Atividades</label>
                            @if(isset($Registro->id))
                            ( <input type="checkbox" name="alterarAtt"> Alterar Atribuições )
                            @endif
                            <table>
                                <table class="table table-sm tabela">
                                    <thead>
                                      <tr>
                                        <th style="text-align:center;" scope="col">Aluno</th>
                                        <th style="text-align:center;" scope="col">Atividade</th>
                                      </tr>
                                    </thead>
                                    <tbody id="aulaAlunos">
                                      @if(isset($Registro->id))
                                        @foreach($Alunos as $at)
                                            <tr>
                                                <td><?=$at->Aluno ?></td>
                                                <td><input type="checkbox" {{$at->Atribuido}} value="<?=$at->IDAluno?>" name="Aluno[]"></td>
                                            </tr>
                                        @endforeach
                                      @endif
                                    </tbody>
                                </table>
                            </table>
                        </div>
                    </div>
                    <br>
                    <div class="col-sm-12 text-left row">
                        @if(Auth::user()->tipo == 6)
                        <button type="submit" class="btn btn-fr col-auto">Salvar</button>
                        &nbsp;
                        @endif
                        <a class="btn btn-light col-auto" href="{{route('Aulas/Atividades/index')}}">Voltar</a>
                    </div>
                </form>    
            </div>
            <!--//-->
        </div>
    </div>
    <script>
        $(document).ready(function(){
            $("select[name=IDAula]").on("change",function(){
                $.ajax({
                    method : "POST",
                    url : $(this).attr("data-alunos"),
                    data : {
                        IDAula : $(this).val()
                    },
                    headers : {
                        "X-CSRF-TOKEN" : $('meta[name="csrf-token"]').attr('content')
                    }
                }).done(function(resp){
                    $("#aulaAlunos").html(resp)
                })
            })
        })
    </script>
</x-educacional-layout>