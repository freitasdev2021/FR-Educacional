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
                </form>  
                <!--------------RELATORIOS DE MAPAS---------------------->
                @if(isset($Registro->id))
                    <hr>
                    <form action="{{route('Relatorios/Mapas')}}" method="POST">
                        <label>Relatórios de Mapeamento</label>
                        <hr>
                        @csrf
                        <input name="IDDisciplina" value="{{$Registro->id}}" type="hidden">
                        <input name="IDProfessor" value="" type="hidden">
                        <div class="row">
                            <div class="col-sm-4">
                                <label>Turma</label>
                                <select name="IDTurma" class="form-control">
                                    <option value="">Selecione</option>
                                    @foreach($Turmas as $t)
                                    <option value="{{$t->IDTurma}}" data-professor="{{$t->IDProfessor}}">{{$t->Serie}} - {{$t->Turma}} - {{$t->Professor}}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-sm-4">
                                <label>Relatório</label>
                                <select name="Tipo" class="form-control">
                                    <option value="Nota">Mapa</option>
                                    <option value="Frequencia">Frequência Bimestral</option>
                                </select>
                            </div>
                            <div class="col-sm-4">
                                <label>Etapa</label>
                                <select name="Periodo" class="form-control">
                                    <option value="Ano">Ano</option>
                                    <option value="1º BIM">1º BIM</option>
                                    <option value="2º BIM">2º BIM</option>
                                    <option value="3º BIM">3º BIM</option>
                                    <option value="4º BIM">4º BIM</option>
                                </select>
                            </div>
                            <div class="col-sm-12">
                                <label>Observações</label>
                                <textarea name="Observacoes" class="form-control"></textarea>
                            </div>
                            <div class="col-auto">
                                <br>
                                <button class="btn btn-default">Gerar</button>
                            </div>
                        </div>
                    </form>
                    <hr>
                    <!-------------RELATORIOS DE AULAS----------------------->
                    <form action="{{route('Relatorios/Disciplinas/Aulas')}}" method="POST">
                        <label>Relatórios de Aulas</label>
                        <hr>
                        @csrf
                        <input name="IDDisciplina" value="{{$Registro->id}}" type="hidden">
                        <input name="IDProfessor" value="" type="hidden">
                        <div class="row">
                            <div class="col-sm-6">
                                <label>Turma</label>
                                <select name="IDTurma" class="form-control">
                                    <option value="">Selecione</option>
                                    @foreach($Turmas as $t)
                                    <option value="{{$t->IDTurma}}" data-professor="{{$t->IDProfessor}}">{{$t->Serie}} - {{$t->Turma}} - {{$t->Professor}}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-sm-6">
                                <label>Etapa</label>
                                <select name="Periodo" class="form-control">
                                    <option value="Ano">Ano</option>
                                    <option value="1º BIM">1º BIM</option>
                                    <option value="2º BIM">2º BIM</option>
                                    <option value="3º BIM">3º BIM</option>
                                    <option value="4º BIM">4º BIM</option>
                                </select>
                            </div>
                            <div class="col-sm-12">
                                <label>Observações</label>
                                <textarea name="Observacoes" class="form-control"></textarea>
                            </div>
                            <div class="col-auto">
                                <br>
                                <button class="btn btn-default">Gerar</button>
                            </div>
                        </div>
                    </form>
                    @endif
                <!------------------------------------>  
            </div>
            <!--//-->
        </div>
    </div>
    <script>
        $("select[name=IDTurma]").on("change",function(){
            var IDProfessor = $("option:selected",this).attr('data-professor')
            $("input[name=IDProfessor]").val(IDProfessor)
        })
    </script>
</x-educacional-layout>