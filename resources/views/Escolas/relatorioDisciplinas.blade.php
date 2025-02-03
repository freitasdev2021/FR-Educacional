<x-educacional-layout>
    <div class="fr-card p-0 shadow col-sm-12">
        <div class="fr-card-header">
           @foreach($submodulos as $s)
            <x-submodulo nome="{{$s['nome']}}" endereco="{{$s['endereco']}}" rota="{{route($s['rota'])}}" icon="bx bx-list-ul"/>
           @endforeach
        </div>
        <div class="fr-card-body">
            <!--CABECALHO-->
            <div class="col-sm-12 p-2 row">
                @if(in_array(Auth::user()->tipo,[2,2.5]))
                <div class="col-auto">
                    <a href="{{route('Escolas/Disciplinas/Novo')}}" class="btn btn-fr">Adicionar</a>
                </div>
                <hr>
                @elseif(in_array(Auth::user()->tipo,[6,5,5.5]))
                <form class="row" method="GET">
                    <div class="col-sm-4">
                        <label>Turmas</label>
                        <select name="IDTurma" class="form-control">
                            <option value="">Selecione a turma</option>
                            @foreach($Turmas as $t)
                            <option value="{{$t->id}}" {{isset($_GET['IDTurma']) && $_GET['IDTurma'] == $t->id ? 'selected' : ''}}>{{$t->Serie}} - {{$t->Nome}}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-auto">
                        <label style="visibility:hidden;">Turmas</label>
                        <input type="submit" class="form-control btn btn-default" value="Filtrar">
                    </div>
                </form>
                @endif
            </div>
            <!--LISTAS-->
            <div class="col-sm-12 p-2">
                @if($IDTurma)
                <hr>
                <form action="{{route('Relatorios/Mapas')}}" method="POST">
                    <label>Relatórios de Mapeamento</label>
                    <hr>
                    @csrf
                    <input name="IDTurma" value="{{$IDTurma}}" type="hidden">
                    @if(!isset($Professores))
                    <input name="IDProfessor" value="{{Auth::user()->IDProfissional}}" type="hidden">
                    @endif
                    <div class="row">
                        @if(isset($Professores))
                        <div class="col-sm-12">
                            <label>Professor</label>
                            <select class="form-control" name="IDProfessor">
                                @foreach($Professores as $p)
                                <option value="{{$p->IDProfessor}}">{{$p->Professor}}</option>
                                @endforeach
                            </select>
                        </div>
                        @endif
                        <div class="col-sm-4">
                            <label>Disciplina</label>
                            <select name="IDDisciplina" class="form-control">
                                <option value="">Selecione</option>
                                @foreach($Disciplinas as $d)
                                <option value="{{$d->IDDisciplina}}">{{$d->Disciplina}}</option>
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
                    <input name="IDTurma" value="{{$IDTurma}}" type="hidden">
                    @if(!isset($Professores))
                    <input name="IDProfessor" value="{{Auth::user()->IDProfissional}}" type="hidden">
                    @endif
                    <div class="row">
                        @if(isset($Professores))
                        <div class="col-sm-12">
                            <label>Professor</label>
                            <select class="form-control" name="IDProfessor">
                                @foreach($Professores as $p)
                                <option value="{{$p->IDProfessor}}">{{$p->Professor}}</option>
                                @endforeach
                            </select>
                        </div>
                        @endif
                        <div class="col-sm-6">
                            <label>Disciplina</label>
                            <select name="IDDisciplina" class="form-control">
                                <option value="">Selecione</option>
                                @foreach($Disciplinas as $d)
                                <option value="{{$d->IDDisciplina}}">{{$d->Disciplina}}</option>
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
                @if(Auth::user()->tipo == 5)
                <hr>
                <!----------------------AULAS DATA---------------------------------------->
                <label>Aulas por Data</label>
                <form action="{{route('Relatorios/Disciplinas/AulasData')}}" method="POST">
                    @csrf
                    <input type="hidden" value="{{$IDTurma}}" name="IDTurma">
                    <div class="row">
                        <div class="col-sm-12">
                            <label>Etapa</label>
                            <select name="Periodo" class="form-control">
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
                            <button class="btn btn-default" type="submit">Gerar</button>
                        </div>
                    </div>
                </form>
                @endif
                <!---------------------------------->
                @endif
            </div>
            <!--//-->
        </div>
    </div>
</x-educacional-layout>