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
                <form action="{{route('Aulas/Recuperacao/Save')}}" method="POST">
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
                    @if(isset($id))
                    <input type="hidden" name="id" value="{{$id}}">
                    @endif
                    <div class="row">
                        <div class="col-sm-4">
                            <label>Aluno</label>
                            <select name="IDAluno" class="form-control" required>
                                <option value="">Selecione</option>
                                @foreach($alunos as $a)
                                <option value="{{$a->id}}" {{(isset($Registros->IDAluno) && $Registros->IDAluno == $a->id) ? 'selected' : ''}}>{{$a->Aluno}}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-sm-4">
                            <label>Disciplina</label>
                            <select name="IDDisciplina" class="form-control" required>
                                <option value="">Selecione</option>
                                @foreach($disciplinas as $d)
                                <option value="{{$d->IDDisciplina}}" {{(isset($Registros->IDDisciplina) && $Registros->IDDisciplina == $d->IDDisciplina) ? 'selected' : ''}}>{{$d->Disciplina}}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-sm-4">
                            <label>Estágio</label>
                            <select name="Estagio" class="form-control" required>
                                <optgroup label="Bimestre">
                                    <option value="1º BIM">1º Bimestre</option>
                                    <option value="2º BIM">2º Bimestre</option>
                                    <option value="3º BIM">3º Bimestre</option>
                                    <option value="4º BIM">4º Bimestre</option>
                                </optgroup>
                                <optgroup label="Trimestre">
                                    <option value="1º TRI">1º Trimestre</option>
                                    <option value="2º TRI">2º Trimestre</option>
                                    <option value="3º TRI">3º Trimestre</option>
                                </optgroup>
                                <optgroup label="Semestre">
                                    <option value="1º SEM">1º Semestre</option>
                                    <option value="2º SEM">2º Semestre</option>
                                </optgroup>
                                <optgroup label="Periodo">
                                    <option value="1º PER">1º Periodo</option>
                                </optgroup>
                            </select>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-sm-6">
                            <label>Pontuação</label>
                            <input type="number" name="Pontuacao" class="form-control" value="{{(isset($Registros->Pontuacao)) ? $Registros->Pontuacao : ''}}" required>
                        </div>
                        <div class="col-sm-6">
                            <label>Nota</label>
                            <input type="number" name="Nota" class="form-control" value="{{(isset($Registros->Nota)) ? $Registros->Nota : ''}}">
                        </div>
                    </div>
                    <br>
                    <div class="col-sm-12 text-left row">
                        <button type="submit" class="btn btn-fr col-auto">Salvar</button>
                        &nbsp;
                        <a class="btn btn-light col-auto" href="{{route('Aulas/Recuperacao/index')}}">Voltar</a>
                    </div>
                </form>    
            </div>
            <!--//-->
        </div>
    </div>>
</x-educacional-layout>