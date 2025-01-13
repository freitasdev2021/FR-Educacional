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
                <form action="{{route('Escolas/Disciplinas/Save')}}" method="POST">
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
                    <div class="row">
                        <div class="col-sm-6">
                            <label>Disciplina</label>
                            <input type="text" name="NMDisciplina" class="form-control" required maxlength="100" required value="{{isset($Registro->NMDisciplina) ? $Registro->NMDisciplina : ''}}">
                        </div>
                        <div class="col-sm-4">
                            <label>Carga horária</label>
                            <input type="number" name="CargaHoraria" class="form-control" required required value="{{isset($Registro->CargaHoraria) ? $Registro->CargaHoraria : ''}}">
                        </div>
                        <div class="col-sm-2">
                            <label>Obrigatória</label>
                            <select name="Obrigatoria" class="form-control" required>
                                <option value="Sim" {{(isset($Registro->Obrigatoria) && $Registro->Obrigatoria == 'Sim' ) ? 'selected' : ''}}>Sim</option>
                                <option value="Não" {{(isset($Registro->Obrigatoria) && $Registro->Obrigatoria == 'Não' ) ? 'selected' : ''}}>Não</option>
                            </select>
                        </div>
                    </div>
                    <br>
                    <label>Escolas</label>
                    <div class="checkboxEscolas">
                        @foreach($escolas as $e)
                            <div class="form-check escola">
                                {{-- <input type="hidden" name="Escola[]" value="{{isset($Registro->Escolas) && in_array($e->Nome,json_decode($Registro->Escolas,true)) ? $e->id : ''}}"> --}}
                                <input class="form-check-input" type="checkbox" value="{{$e->id}}" name="Escola[]" {{isset($Registro) && $e->Alocado == 1 ? 'checked' : ''}} id="flexCheckDefault">
                                <label class="form-check-label" for="flexCheckDefault">
                                {{$e->Nome}}
                                </label>
                            </div>
                        @endforeach
                    </div>
                    <br>
                    <div class="col-sm-12 text-left row">
                        <button type="submit" class="btn btn-fr col-auto">Salvar</button>
                        &nbsp;
                        <a class="btn btn-light col-auto" href="{{route('Escolas/Disciplinas')}}">Voltar</a>
                    </div>
                    @if(isset($Registro->id))
                    <hr>
                    <table class="table" border>
                        <thead>
                            <tr>
                                <th>Turma</th>
                                <th>Professor</th>
                                <th>Gerar Relatórios</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($Turmas as $t)
                            <tr>
                                <td>{{$t->Serie}} - {{$t->Turma}}</td>
                                <td>{{$t->Professor}}</td>
                                <td>
                                    <a href="{{route('Relatorios/Mapas',["Periodo"=>"Ano","IDTurma"=>$t->IDTurma,"IDProfessor"=>$t->IDProfessor])}}" class="btn btn-xs btn-success">Mapa Final</a>
                                    <a href="" class="btn btn-xs btn-primary">Mapa 1 BIM</a>
                                    <a href="" class="btn btn-xs btn-primary">Mapa 2 BIM</a>
                                    <a href="" class="btn btn-xs btn-primary">Mapa 3 BIM</a>
                                    <a href="" class="btn btn-xs btn-primary">Mapa 4 BIM</a>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                    @endif
                </form>    
            </div>
            <!--//-->
        </div>
    </div>
    <script>
        // $(".escola").find("input[type=checkbox]").on("click",function(){
        //     if($(this).is(":checked")){
        //         $(this).parent().find("input[type=hidden]").val($(this).val())
        //     }else{
        //         $(this).parent().find("input[type=hidden]").val(0)
        //     }
        // })
    </script>
</x-educacional-layout>